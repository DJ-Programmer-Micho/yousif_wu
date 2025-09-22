<?php
// app/Http/Livewire/Balance/ReceiverBalanceDetailsLivewire.php
namespace App\Http\Livewire\Balance;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReceiverBalance;
use Illuminate\Support\Facades\DB;

class ReceiverBalanceDetailsLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $userId;
    public string $mode = 'all'; // 'all','incoming','outgoing'
    public int $perPage = 10;

    public ?string $dateFrom = null;
    public ?string $dateTo   = null;

    public $deductInlineAmount = null;
    public ?string $deductInlineNote = null;

    protected function rules(): array
    {
        return [
            'dateFrom' => ['nullable','date'],
            'dateTo'   => ['nullable','date','after_or_equal:dateFrom'],
            'deductInlineAmount' => ['nullable','integer','min:1','max:999999999999'],
            'deductInlineNote'   => ['nullable','string','max:255'],
        ];
    }

    public function mount(int $userId) { $this->userId = $userId; }

    public function setMode(string $m)
    {
        $this->mode = in_array($m, ['all','incoming','outgoing'], true) ? $m : 'all';
        $this->resetPage();
    }

    public function updatedDateFrom(){ $this->validateOnly('dateFrom'); $this->resetPage(); }
    public function updatedDateTo(){ $this->validateOnly('dateTo'); $this->resetPage(); }
    public function clearDateFilter(){ $this->reset(['dateFrom','dateTo']); $this->resetPage(); }

    protected function filteredBase()
    {
        return ReceiverBalance::query()
            ->where('user_id', $this->userId)
            ->when($this->mode === 'incoming', fn($q) => $q->where('status','Incoming'))
            ->when($this->mode === 'outgoing', fn($q) => $q->where('status','Outgoing'))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at','>=',$this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at','<=',$this->dateTo));
    }

    public function resetToZero(): void
    {
        if ((int)auth()->user()->role !== 1) abort(403);

        $running = (int) ReceiverBalance::where('user_id',$this->userId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status='Incoming' THEN amount ELSE 0 END),0)
            - COALESCE(SUM(CASE WHEN status='Outgoing' THEN amount ELSE 0 END),0) as running
            ")
            ->value('running');

        if ($running > 0) {
            ReceiverBalance::create([
                'user_id'  => $this->userId,
                'amount'   => $running,       // positive
                'status'   => 'Outgoing',     // bring running to zero
                'admin_id' => auth()->id(),
                'note'     => 'Admin reset to zero',
            ]);
        }

        $this->dispatchBrowserEvent('toast', ['message' => __('Balance reset to zero')]);
        $this->emitSelf('$refresh');
    }

    public function saveInlineDeduct(): void
    {
        if ((int)auth()->user()->role !== 1) abort(403);
        $this->validateOnly('deductInlineAmount');

        $running = (int) ReceiverBalance::where('user_id',$this->userId)
            ->sum(DB::raw("CASE WHEN status='Incoming' THEN amount ELSE -amount END"));

        if ((int)$this->deductInlineAmount < 1 || (int)$this->deductInlineAmount > $running) {
            $this->addError('deductInlineAmount', __('Amount exceeds running balance.'));
            return;
        }

        ReceiverBalance::create([
            'user_id'  => $this->userId,
            'amount'   => (int)$this->deductInlineAmount,
            'status'   => 'Outgoing',
            'admin_id' => auth()->id(),
            'note'     => $this->deductInlineNote,
        ]);

        $this->reset(['deductInlineAmount','deductInlineNote']);
        $this->dispatchBrowserEvent('toast', ['message' => __('Deducted successfully')]);
        $this->emitSelf('$refresh');
    }

    public function deleteEntry(int $id): void
    {
        if ((int)auth()->user()->role !== 1) abort(403);

        $row = ReceiverBalance::where('id', $id)
            ->where('user_id', $this->userId)
            ->first();

        if (!$row) {
            $this->dispatchBrowserEvent('toast', ['message' => __('Not found or not allowed')]);
            return;
        }

        $row->delete();

        // If current page becomes empty, go back a page to avoid blank page
        $countOnPage = $this->filteredBase()->count();
        if ($countOnPage === 0 && $this->page > 1) {
            $this->previousPage();
        }

        $this->dispatchBrowserEvent('toast', ['message' => __('Entry deleted')]);
        $this->emitSelf('$refresh');
    }

    public function render()
    {
        $user = User::select('id','name','email','status')->findOrFail($this->userId);

        $rows = $this->filteredBase()
            ->with([
                'receiver:id,first_name,last_name',
                'admin:id,name',
            ])
            ->latest('id')
            ->paginate($this->perPage);

        $totals = ReceiverBalance::where('user_id',$this->userId)
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at','>=',$this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at','<=',$this->dateTo))
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status='Incoming' THEN amount ELSE 0 END),0) as inc,
                COALESCE(SUM(CASE WHEN status='Outgoing' THEN amount ELSE 0 END),0) as outg
            ")
            ->first();

        $incoming = (int) $totals->inc;
        $outgoing = (int) $totals->outg;
        $running  = $incoming - $outgoing;
        return view('components.tables.receiver-table-details', compact('user','rows','incoming','outgoing','running'));
    }
}
