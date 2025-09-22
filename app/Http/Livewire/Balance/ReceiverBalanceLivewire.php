<?php
// app/Http/Livewire/Balance/ReceiverBalanceLivewire.php
namespace App\Http\Livewire\Balance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\ReceiverBalance;

class ReceiverBalanceLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $q = '';
    public int $perPage = 10;

    public ?int $selectedUserId = null;
    public ?string $selectedUserName = null;

    // Deduct modal
    public ?int $deductUserId = null;
    public ?string $deductUserName = null;
    public $deductAmount = null; // int (IQD)
    public ?string $deductNote = null;

    protected $listeners = ['closeReceiverDetailsModal' => 'closeModal'];

    public function updatingQ() { $this->resetPage(); }

    public function getRegistersQuery()
    {
        $q = trim($this->q);
        return User::query()
            ->where('role', 2)
            ->when($q !== '', function($builder) use ($q) {
                $builder->where(function($bb) use ($q) {
                    $bb->where('name','like',"%{$q}%")
                       ->orWhere('email','like',"%{$q}%");
                });
            })
            ->select(['id','name','email','status'])
            ->withSum(['receiverBalances as incoming_sum' => function($qq){
                $qq->where('status','Incoming');
            }],'amount')
            ->withSum(['receiverBalances as outgoing_sum' => function($qq){
                $qq->where('status','Outgoing');
            }],'amount');
    }

    // Details modal
    public function openDetails(int $userId): void
    {
        $u = User::select('id','name')->findOrFail($userId);
        $this->selectedUserId = $u->id;
        $this->selectedUserName = $u->name;
        $this->dispatchBrowserEvent('open-receiver-details-modal');
    }
    public function closeModal(): void
    {
        $this->selectedUserId = null;
        $this->selectedUserName = null;
        $this->dispatchBrowserEvent('close-receiver-details-modal');
    }

    // Deduct modal (admin only)
    public function openDeduct(int $userId): void
    {
        if ((int)auth()->user()->role !== 1) abort(403);
        $u = User::select('id','name')->findOrFail($userId);
        $this->deductUserId = $u->id;
        $this->deductUserName = $u->name;
        $this->deductAmount = null;
        $this->deductNote = null;
        $this->dispatchBrowserEvent('open-receiver-deduct-modal');
    }

    public function saveDeduct(): void
    {
        if ((int)auth()->user()->role !== 1) abort(403);

        $this->validate([
            'deductUserId' => ['required','integer','exists:users,id'],
            'deductAmount' => ['required','integer','min:1','max:999999999999'],
            'deductNote'   => ['nullable','string','max:255'],
        ]);

        $incoming = (int) ReceiverBalance::where('user_id',$this->deductUserId)->where('status','Incoming')->sum('amount');
        $outgoing = (int) ReceiverBalance::where('user_id',$this->deductUserId)->where('status','Outgoing')->sum('amount');
        $running  = $incoming - $outgoing;

        if ((int)$this->deductAmount > $running) {
            $this->dispatchBrowserEvent('toast', ['message' => __('Deduct amount exceeds running balance.')]);
            return;
        }

        ReceiverBalance::create([
            'user_id'  => $this->deductUserId,
            'amount'   => (int)$this->deductAmount,
            'status'   => 'Outgoing',
            'admin_id' => auth()->id(),
            'note'     => $this->deductNote,
        ]);

        $this->dispatchBrowserEvent('toast', ['message' => __('Amount deducted successfully.')]);
        $this->dispatchBrowserEvent('close-receiver-deduct-modal');
        $this->reset(['deductUserId','deductUserName','deductAmount','deductNote']);
    }

    public function render()
    {
        $registers = $this->getRegistersQuery()->paginate($this->perPage);
        return view('components.tables.receiver-table', [
            'registers' => $registers,
            'isAdmin'   => (int)auth()->user()->role === 1,
        ]);
    }
}
