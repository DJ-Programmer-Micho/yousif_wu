<?php

namespace App\Http\Livewire\General;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// adjust namespaces if different in your app:
use App\Models\Sender;
use App\Models\Receiver;
use App\Models\User;

class ProfileLivewire extends Component
{
    public $user;

    // UI data
    public $roleLabel = 'User';
    public $joinedYear;
    public $joinedHuman;

    // Stats (personal – for this user only)
    public $sendersExecutedCount = 0;
    public $sendersExecutedTotal = 0.0;

    public $receiversExecutedCount = 0;
    public $receiversExecutedTotal = 0.0;

    // Recent activity
    public $recentSenders = [];
    public $recentReceivers = [];

    protected $listeners = [
        // e.g. 'profileUpdated' => '$refresh'
    ];

    public function mount()
    {
        $this->user = Auth::user();

        // Role label (adapt to your roles/permissions logic)
        // Example: role_id 1=Admin, 2=Register
        $this->roleLabel = auth()->user()->role == 1
            ? 'Admin'
            : (property_exists($this->user, 'role_id') && (int) $this->user->role_id === 2 ? 'Register' : 'User');

        $this->joinedYear  = optional($this->user->created_at)->format('Y');
        $this->joinedHuman = optional($this->user->created_at)->diffForHumans();

        // ===== Personal STATS (by user_id) =====
        // Senders (assumes columns: user_id, status, total)
        if (class_exists(Sender::class)) {
            $row = Sender::query()
                ->selectRaw('COUNT(*) as c, COALESCE(SUM(total),0) as s')
                ->where('user_id', $this->user->id)
                ->where('status', 'Executed')
                ->first();
            $this->sendersExecutedCount = (int) ($row->c ?? 0);
            $this->sendersExecutedTotal = (float) ($row->s ?? 0.0);

            $this->recentSenders = Sender::query()
                ->where('user_id', $this->user->id)
                ->where('status', 'Executed')
                ->latest('updated_at')
                ->limit(10)
                ->get(['id','mtcn','total','updated_at','first_name','last_name'])
                ->toArray();
        }

        // Receivers (assumes columns: user_id, status, amount_iqd or total)
        if (class_exists(Receiver::class)) {
            // If your receivers have USD total field, swap amount_iqd with that
            $row = Receiver::query()
                ->selectRaw('COUNT(*) as c, COALESCE(SUM(amount_iqd),0) as s')
                ->where('user_id', $this->user->id)
                ->where('status', 'Executed')
                ->first();
            $this->receiversExecutedCount = (int) ($row->c ?? 0);
            $this->receiversExecutedTotal = (float) ($row->s ?? 0.0);

            $this->recentReceivers = Receiver::query()
                ->where('user_id', $this->user->id)
                ->where('status', 'Executed')
                ->latest('updated_at')
                ->limit(10)
                ->get(['id','mtcn','amount_iqd','updated_at','first_name','last_name'])
                ->toArray();
        }
    }

    public function render()
    {
        return view('components.general.profile');
    }
}
