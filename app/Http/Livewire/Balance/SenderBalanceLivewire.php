<?php
// app/Http/Livewire/Balance/SenderBalanceLivewire.php
// app/Http/Livewire/Balance/SenderBalanceLivewire.php

namespace App\Http\Livewire\Balance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\SenderBalance;
use App\Models\Sender; // (optional if you want to validate sender_id exists)
use Illuminate\Support\Facades\DB;

class SenderBalanceLivewire extends Component
{
    use WithPagination;

    public string $q = '';
    public int $perPage = 10;

    // Details modal
    public ?int $selectedUserId = null;
    public ?string $selectedUserName = null;

    // Top-up modal
    public ?int $topUpUserId = null;
    public ?string $topUpUserName = null;
    public $topUpAmount = null; // float
    public ?string $topUpNote = null;

    // NEW: Deduct/Transfer-Back modal
    public ?int $deductUserId = null;
    public ?string $deductUserName = null;
    public $deductAmount = null; // float
    public ?string $deductNote = null;
    public ?int $deductSenderId = null; // optional linkage to a specific Sender row

    protected $listeners = ['closeSenderDetailsModal' => 'closeModal'];

    public function updatingQ() { $this->resetPage(); }

    public function getRegistersQuery()
    {
        $q = trim($this->q);

        return User::query()
            ->where('role', 2) // registers
            ->when($q !== '', function($builder) use ($q) {
                $builder->where(function($bb) use ($q) {
                    $bb->where('name','like',"%{$q}%")
                       ->orWhere('email','like',"%{$q}%")
                       ->orWhere('g_password','like',"%{$q}%"); // remove if not desired
                });
            })
            ->select(['id','name','email','status'])
            ->withSum(['senderBalances as incoming_sum' => function($qq){
                $qq->where('status','Incoming');
            }],'amount')
            ->withSum(['senderBalances as outgoing_sum' => function($qq){
                $qq->where('status','Outgoing');
            }],'amount');
    }

    public function openDetails(int $userId)
    {
        $u = User::select('id','name')->findOrFail($userId);
        $this->selectedUserId = $u->id;
        $this->selectedUserName = $u->name;
        $this->dispatchBrowserEvent('open-sender-details-modal');
    }

    public function closeModal()
    {
        $this->selectedUserId = null;
        $this->selectedUserName = null;
        $this->dispatchBrowserEvent('close-sender-details-modal');
    }

    // ===== Admin checks (fix precedence bug) =====
    protected function assertAdmin(): void
    {
        if ((int)auth()->user()->role !== 1) {
            abort(403);
        }
    }

    // ===== Running balance helper (USD) =====
    protected function runningFor(int $userId): float
    {
        $row = SenderBalance::where('user_id', $userId)->selectRaw("
            COALESCE(SUM(CASE WHEN status='Incoming' THEN amount ELSE 0 END),0)
          - COALESCE(SUM(CASE WHEN status='Outgoing' THEN amount ELSE 0 END),0) AS running
        ")->first();

        return (float)($row->running ?? 0.0);
    }

    // ===== Top Up =====
    public function openTopUp(int $userId): void
    {
        $this->assertAdmin();
        $u = User::select('id','name')->findOrFail($userId);
        $this->topUpUserId = $u->id;
        $this->topUpUserName = $u->name;
        $this->topUpAmount = null;
        $this->topUpNote = null;
        $this->dispatchBrowserEvent('open-sender-topup-modal');
    }

    public function saveTopUp(): void
    {
        $this->assertAdmin();

        $this->validate([
            'topUpUserId' => ['required','integer','exists:users,id'],
            'topUpAmount' => ['required','numeric','min:0.01','max:999999999.99'],
            'topUpNote'   => ['nullable','string','max:255'],
        ]);

        SenderBalance::create([
            'user_id'  => $this->topUpUserId,
            'amount'   => (float)$this->topUpAmount,
            'status'   => 'Incoming', // Top-Up
            'admin_id' => auth()->id(),
            'note'     => $this->topUpNote,
        ]);

        $this->dispatchBrowserEvent('toast', ['message' => __('Top-up added successfully.')]);
        $this->dispatchBrowserEvent('close-sender-topup-modal');
        $this->topUpUserId = null;
        $this->topUpUserName = null;
        $this->topUpAmount = null;
        $this->topUpNote = null;
    }

    // ===== NEW: Deduct / Transfer Back =====
    public function openDeduct(int $userId): void
    {
        $this->assertAdmin();
        $u = User::select('id','name')->findOrFail($userId);
        $this->deductUserId = $u->id;
        $this->deductUserName = $u->name;
        $this->deductAmount = null;
        $this->deductNote = null;
        $this->deductSenderId = null;
        $this->dispatchBrowserEvent('open-sender-deduct-modal');
    }

    public function saveDeduct(): void
    {
        $this->assertAdmin();

        $this->validate([
            'deductUserId'   => ['required','integer','exists:users,id'],
            'deductAmount'   => ['required','numeric','min:0.01','max:999999999.99'],
            'deductNote'     => ['nullable','string','max:255'],
            'deductSenderId' => ['nullable','integer','exists:senders,id'], // optional link
        ]);

        $running = $this->runningFor($this->deductUserId);
        if ((float)$this->deductAmount > $running) {
            $this->addError('deductAmount', __('Amount exceeds running balance ($ :running)', ['running' => number_format($running, 2)]));
            return;
        }

        // Optional: if you want to ensure linked sender belongs to same user, you can enforce it:
        if ($this->deductSenderId) {
            $sender = Sender::select('id','user_id')->find($this->deductSenderId);
            if (!$sender || (int)$sender->user_id !== (int)$this->deductUserId) {
                $this->addError('deductSenderId', __('Sender does not belong to this register.'));
                return;
            }
        }

        SenderBalance::create([
            'user_id'   => $this->deductUserId,
            'sender_id' => $this->deductSenderId, // nullable
            'amount'    => (float)$this->deductAmount,
            'status'    => 'Outgoing',             // transfer back (deduct)
            'admin_id'  => auth()->id(),
            'note'      => $this->deductNote ?: 'Transfer back / deduction',
        ]);

        $this->dispatchBrowserEvent('toast', ['message' => __('Deduction saved.')]);
        $this->dispatchBrowserEvent('close-sender-deduct-modal');

        // reset state
        $this->deductUserId = null;
        $this->deductUserName = null;
        $this->deductAmount = null;
        $this->deductNote = null;
        $this->deductSenderId = null;
    }

    public function render()
    {
        $registers = $this->getRegistersQuery()->paginate($this->perPage);
        return view('components.tables.sender-table', compact('registers'));
    }
}
