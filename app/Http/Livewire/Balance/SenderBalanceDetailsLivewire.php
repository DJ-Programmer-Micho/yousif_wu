<?php
// app/Http/Livewire/Balance/SenderBalanceDetailsLivewire.php
namespace App\Http\Livewire\Balance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SenderBalance;
use App\Models\User;

class SenderBalanceDetailsLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $userId;
    public string $mode = 'all'; // 'all','incoming','outgoing'
    public int $perPage = 10;

    // NEW: date filters (YYYY-MM-DD)
    public ?string $dateFrom = null;
    public ?string $dateTo   = null;

    protected $listeners = ['refreshSenderDetails' => '$refresh'];

    protected function rules(): array
    {
        return [
            'dateFrom' => ['nullable','date'],
            'dateTo'   => ['nullable','date','after_or_equal:dateFrom'],
        ];
    }

    public function mount(int $userId)
    {
        $this->userId = $userId;
    }

    public function setMode(string $m)
    {
        $this->mode = in_array($m, ['all','incoming','outgoing'], true) ? $m : 'all';
        $this->resetPage();
    }

    // Auto-apply filters on change
    public function updatedDateFrom(): void
    {
        $this->validateOnly('dateFrom');
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->validateOnly('dateTo');
        $this->resetPage();
    }

    public function clearDateFilter(): void
    {
        $this->reset(['dateFrom','dateTo']);
        $this->resetPage();
    }

    public function deleteEntry(int $id): void
    {
        if ((int)auth()->user()->role !== 1) abort(403);

        $row = SenderBalance::where('id', $id)
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

    protected function filteredBase()
    {
        return SenderBalance::query()
            ->where('user_id', $this->userId)
            ->when($this->mode === 'incoming', fn($q) => $q->where('status','Incoming'))
            ->when($this->mode === 'outgoing', fn($q) => $q->where('status','Outgoing'))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at','>=',$this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at','<=',$this->dateTo));
    }

    public function render()
    {
        $user = User::select('id','name','email','status')->findOrFail($this->userId);

        // Base query for rows
        $base = SenderBalance::query()
            ->with([
                'sender:id,first_name,last_name',   // eager-load sender person
                'admin:id,name',                    // eager-load admin user
            ])
            ->where('user_id', $this->userId)
            ->when($this->mode === 'incoming', fn($q) => $q->where('status','Incoming'))
            ->when($this->mode === 'outgoing', fn($q) => $q->where('status','Outgoing'))
            // Date range filters (inclusive)
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo));

        $rows = $base->latest('id')->paginate($this->perPage);

        // Totals query with the SAME date filters
        $totals = SenderBalance::where('user_id', $this->userId)
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status='Incoming' THEN amount ELSE 0 END),0) as inc,
                COALESCE(SUM(CASE WHEN status='Outgoing' THEN amount ELSE 0 END),0) as outg
            ")
            ->first();

        $incoming  = (float) $totals->inc;
        $outgoing  = (float) $totals->outg;
        $remaining = $incoming - $outgoing;

        return view('components.tables.sender-table-details', compact('user','rows','incoming','outgoing','remaining'));
    }
}
