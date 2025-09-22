<?php

namespace App\Http\Livewire\Sender;

use App\Models\User;
use App\Models\Sender;
use App\Models\Country;
use Livewire\Component;
use App\Models\Receiver;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Models\ReceiverBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Telegram\TeleNotifySenderAction;
use App\Notifications\Telegram\TeleNotifyReceiverAction;

class BankStatementLivewire extends Component
{
    use WithPagination;

    /** Tabs / filters */
    public string $tab = 'senders';        // 'senders' | 'receivers'
    public string $q = '';
    public string $status = '';            // ''|Pending|Executed|Rejected (works for both tabs now)
    public string $country = '';           // (senders) en_name saved in DB
    public ?int   $registerId = null;      // admin-only filter
    public int    $perPage = 10;

    // Enhanced filters
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $amountFrom = '';
    public string $amountTo = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    /** Data for selects */
    public array $availableCountries = [];
    public array $countryMap = [];
    public array $registerOptions = [];
    public array $statusOptions = []; // now supports receivers too

    public bool $isAdmin = false;
    public bool $showAdvancedFilters = false;

    protected $queryString = [
        'tab'          => ['except' => 'senders'],
        'q'            => ['except' => ''],
        'status'       => ['except' => ''],
        'country'      => ['except' => ''],
        'registerId'   => ['except' => null],
        'dateFrom'     => ['except' => ''],
        'dateTo'       => ['except' => ''],
        'amountFrom'   => ['except' => ''],
        'amountTo'     => ['except' => ''],
        'sortBy'       => ['except' => 'created_at'],
        'sortDirection'=> ['except' => 'desc'],
        'page'         => ['except' => 1],
        'perPage'      => ['except' => 10],
    ];
    protected $listeners = ['actions:refresh' => '$refresh'];
    protected function rules(): array
    {
        return [
            'newMtcn'       => ['required','digits:10'],
            'newFirstName'  => ['required','string','max:100'],
            'newLastName'   => ['required','string','max:100'],
        ];
    }

    public function mount(): void
    {
        $this->isAdmin = ((int) auth()->user()->role) === 1;
        $this->initializeData();
    }

    protected function initializeData(): void
    {
        // Countries for sender country filter
        $displayCol = match (app()->getLocale()) {
            'ar' => 'ar_name',
            'ku' => 'ku_name',
            default => 'en_name',
        };

        $countries = Country::orderBy('en_name')
            ->get(['id','en_name','ar_name','ku_name','iso_code','flag_path']);

        $this->availableCountries = $countries->map(function ($c) use ($displayCol) {
            return [
                'id'        => $c->id,
                'en_name'   => $c->en_name,
                'label'     => $c->$displayCol,
                'iso_code'  => strtoupper($c->iso_code),
                'ar_name'   => $c->ar_name,
                'ku_name'   => $c->ku_name,
                'flag_path' => $c->flag_path,
            ];
        })->toArray();

        $this->countryMap = $countries
            ->mapWithKeys(fn($c) => [$c->en_name => $c->$displayCol])
            ->toArray();

        // Admin can filter by Register (role=2)
        if ($this->isAdmin) {
            $this->registerOptions = User::query()
                ->where('role', 2)
                ->orderBy('name')
                ->get(['id','name'])
                ->map(fn($u) => ['id' => $u->id, 'name' => $u->name])
                ->toArray();
        }

        $this->loadStatusCounts();
    }

    protected function loadStatusCounts(): void
    {
        if ($this->tab === 'senders') {
            $base = $this->baseSenderQuery();
            $this->statusOptions = [
                ['value' => '',         'label' => __('All Statuses'), 'count' => (clone $base)->count()],
                ['value' => 'Pending',  'label' => __('Pending'),      'count' => (clone $base)->where('status','Pending')->count()],
                ['value' => 'Executed', 'label' => __('Executed'),     'count' => (clone $base)->where('status','Executed')->count()],
                ['value' => 'Rejected', 'label' => __('Rejected'),     'count' => (clone $base)->where('status','Rejected')->count()],
            ];
        } else {
            $base = $this->baseReceiverQuery();
            $this->statusOptions = [
                ['value' => '',         'label' => __('All Statuses'), 'count' => (clone $base)->count()],
                ['value' => 'Pending',  'label' => __('Pending'),      'count' => (clone $base)->where('status','Pending')->count()],
                ['value' => 'Executed', 'label' => __('Executed'),     'count' => (clone $base)->where('status','Executed')->count()],
                ['value' => 'Rejected', 'label' => __('Rejected'),     'count' => (clone $base)->where('status','Rejected')->count()],
            ];
        }
    }

    /** Tab switch */
    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['senders','receivers'], true) ? $tab : 'senders';

        // Reset filters that don’t apply when switching tabs
        if ($this->tab === 'receivers') {
            $this->country = '';
        }
        $this->status = ''; // start with all
        $this->resetPage();
        $this->loadStatusCounts();

        $this->dispatchBrowserEvent('tab-changed', ['tab' => $this->tab]);
        $this->dispatchBrowserEvent('filter-cleared');
    }

    /** Toggle advanced filters */
    public function toggleAdvancedFilters(): void
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    /** Sorting */
    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    /** Reset pagination on filter changes */
    public function updatingQ()          { $this->resetPage(); }
    public function updatingStatus()     { $this->resetPage(); }
    public function updatingCountry()    { $this->resetPage(); }
    public function updatingRegisterId() { $this->resetPage(); }
    public function updatingPerPage()    { $this->resetPage(); }
    public function updatingDateFrom()   { $this->resetPage(); }
    public function updatingDateTo()     { $this->resetPage(); }
    public function updatingAmountFrom() { $this->resetPage(); }
    public function updatingAmountTo()   { $this->resetPage(); }

    /** Quick date filters */
    public function setDateFilter(string $period): void
    {
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $this->dateFrom = $now->format('Y-m-d');
                $this->dateTo   = $now->format('Y-m-d');
                break;
            case 'yesterday':
                $y = $now->copy()->subDay();
                $this->dateFrom = $y->format('Y-m-d');
                $this->dateTo   = $y->format('Y-m-d');
                break;
            case 'last_7_days':
                $this->dateFrom = $now->copy()->subDays(7)->format('Y-m-d');
                $this->dateTo   = Carbon::now()->format('Y-m-d');
                break;
            case 'last_30_days':
                $this->dateFrom = $now->copy()->subDays(30)->format('Y-m-d');
                $this->dateTo   = Carbon::now()->format('Y-m-d');
                break;
            case 'this_month':
                $this->dateFrom = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->dateTo   = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $lm = $now->copy()->subMonth();
                $this->dateFrom = $lm->startOfMonth()->format('Y-m-d');
                $this->dateTo   = $lm->endOfMonth()->format('Y-m-d');
                break;
        }

        $this->resetPage();
    }

    /** Helpers */
    public function formatMtcn(?string $v): string
    {
        $v = (string) $v;
        return preg_match('/^\d{10}$/', $v)
            ? substr($v,0,3).'-'.substr($v,3,3).'-'.substr($v,6,4)
            : $v;
    }

    protected function escapeLike(string $t): string
    {
        return '%'.str_replace(['%','_'], ['\%','\_'], trim($t)).'%';
    }

    /** Base queries */
    protected function baseSenderQuery()
    {
        $q = Sender::query()->with('user');
        if (!$this->isAdmin) $q->where('user_id', auth()->id());
        return $q;
    }

    protected function baseReceiverQuery()
    {
        $q = Receiver::query()->with('user');
        if (!$this->isAdmin) $q->where('user_id', auth()->id());
        return $q;
    }

    /** Rows builders */
    protected function senderRows()
    {
        $q = $this->baseSenderQuery();

        // Filters
        if ($this->status !== '')  $q->where('status', $this->status);
        if ($this->country !== '') $q->where('country', $this->country);
        if ($this->isAdmin && $this->registerId) $q->where('user_id', $this->registerId);

        // Date range
        if ($this->dateFrom !== '') $q->whereDate('created_at', '>=', Carbon::parse($this->dateFrom));
        if ($this->dateTo !== '')   $q->whereDate('created_at', '<=', Carbon::parse($this->dateTo));

        // Amount ($)
        if ($this->amountFrom !== '') $q->where('amount', '>=', (float) $this->amountFrom);
        if ($this->amountTo   !== '') $q->where('amount', '<=', (float) $this->amountTo);

        // Search
        if ($this->q !== '') {
            $term   = $this->escapeLike($this->q);
            $digits = preg_replace('/\D+/', '', $this->q);

            $q->where(function ($w) use ($term, $digits) {
                $w->where('mtcn', 'like', $term);
                if ($digits !== '') $w->orWhere('mtcn', 'like', '%'.$digits.'%');

                $w->orWhere('first_name', 'like', $term)
                  ->orWhere('last_name',  'like', $term)
                  ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", [$term])
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('r_first_name', 'like', $term)
                  ->orWhere('r_last_name',  'like', $term)
                  ->orWhereRaw("CONCAT(COALESCE(r_first_name,''),' ',COALESCE(r_last_name,'')) LIKE ?", [$term])
                  ->orWhere('r_phone', 'like', $term);
            });
        }

        // Sorting
        $q->orderBy($this->sortBy, $this->sortDirection);

        return $q->paginate($this->perPage);
    }

    protected function receiverRows()
    {
        $q = $this->baseReceiverQuery();

        // NEW: status filter for receivers
        if ($this->status !== '') $q->where('status', $this->status);

        if ($this->isAdmin && $this->registerId) $q->where('user_id', $this->registerId);

        // Date range
        if ($this->dateFrom !== '') $q->whereDate('created_at', '>=', Carbon::parse($this->dateFrom));
        if ($this->dateTo   !== '') $q->whereDate('created_at', '<=', Carbon::parse($this->dateTo));

        // Amount (IQD)
        if ($this->amountFrom !== '') $q->where('amount_iqd', '>=', (float) $this->amountFrom);
        if ($this->amountTo   !== '') $q->where('amount_iqd', '<=', (float) $this->amountTo);

        // Search (name/phone/address)
        if ($this->q !== '') {
            $term = $this->escapeLike($this->q);
            $q->where(function ($w) use ($term) {
                $w->where('first_name','like',$term)
                  ->orWhere('last_name','like',$term)
                  ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", [$term])
                  ->orWhere('phone','like',$term)
                  ->orWhere('address','like',$term);
            });
        }

        // Sorting
        $q->orderBy($this->sortBy, $this->sortDirection);

        return $q->paginate($this->perPage);
    }

    /** Clear filters */
    public function clearFilters(): void
    {
        $this->q          = '';
        $this->status     = '';
        $this->country    = '';
        $this->registerId = null;
        $this->dateFrom   = '';
        $this->dateTo     = '';
        $this->amountFrom = '';
        $this->amountTo   = '';
        $this->sortBy     = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage    = 10;

        $this->resetPage();
        $this->loadStatusCounts();
        $this->dispatchBrowserEvent('filter-cleared');
    }

    protected function getActiveFilters(): array
    {
        return array_filter([
            'q' => $this->q,
            'status' => $this->status,
            'country' => $this->country,
            'registerId' => $this->registerId,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'amountFrom' => $this->amountFrom,
            'amountTo' => $this->amountTo,
        ]);
    }

    /** Lifetime statistics (role-scoped) */
    public function getStatistics(): array
    {
        $query = $this->tab === 'senders' ? $this->baseSenderQuery() : $this->baseReceiverQuery();
        
        $stats = [
            'total'      => $query->count(),
            'today'      => (clone $query)->whereDate('created_at', \Illuminate\Support\Carbon::today())->count(),
            'this_week'  => (clone $query)->whereBetween('created_at', [\Illuminate\Support\Carbon::now()->startOfWeek(), \Illuminate\Support\Carbon::now()->endOfWeek()])->count(),
            'this_month' => (clone $query)->whereMonth('created_at', \Illuminate\Support\Carbon::now()->month)->count(),
        ];

        if ($this->tab === 'senders') {
            $stats['total_amount'] = (clone $query)->sum('amount'); // USD
            $stats['pending']  = (clone $query)->where('status', 'Pending')->count();
            $stats['executed'] = (clone $query)->where('status', 'Executed')->count();
            $stats['rejected'] = (clone $query)->where('status', 'Rejected')->count();
        } else {
            // Receivers: sum IQD
            $amountCol = Schema::hasColumn('receivers', 'amount_iqd') ? 'amount_iqd'
                    : (Schema::hasColumn('receivers', 'total_iqd') ? 'total_iqd' : 'amount'); // fallback
            $stats['total_amount'] = (clone $query)->sum($amountCol); // IQD
            // If you want status counts for receivers too (now that status exists):
            $stats['pending']  = (clone $query)->where('status', 'Pending')->count();
            $stats['executed'] = (clone $query)->where('status', 'Executed')->count();
            $stats['rejected'] = (clone $query)->where('status', 'Rejected')->count();
        }

        return $stats;
    }

    public function render()
    {
        $rows = $this->tab === 'senders' ? $this->senderRows() : $this->receiverRows();
        $statistics = $this->getStatistics();

        return view('components.forms.bankstatement-table', [
            'rows'       => $rows,
            'totalCount' => $statistics['total'],
            'statistics' => $statistics,
        ]);
    }
//     public ?int $execId = null;

//     public string $oldMtcn = '';
//     public string $newMtcn = '';

//     public string $oldFirstName = '';
//     public string $newFirstName = '';

//     public string $oldLastName = '';
//     public string $newLastName = '';
//     public ?float $execTotal = null;
//     public ?string $execSenderName = null;
//     public ?string $execReceiverName = null;
//     public function askExecute(int $id): void
// {
//     if (!$this->isAdmin) abort(403);

//     $s = Sender::query()
//         ->where('id', $id)
//         ->where('status', 'Pending')
//         ->firstOrFail();

//     $this->execId = $s->id;

//     $this->oldMtcn      = (string) $s->mtcn;
//     $this->oldFirstName = (string) $s->first_name;
//     $this->oldLastName  = (string) $s->last_name;

//     $this->newMtcn      = (string) $s->mtcn;
//     $this->newFirstName = mb_strtoupper((string) $s->first_name, 'UTF-8');
//     $this->newLastName  = mb_strtoupper((string) $s->last_name, 'UTF-8');

//     $this->execTotal        = (float) $s->total;
//     $this->execSenderName   = trim(($s->first_name ?? '').' '.($s->last_name ?? '')) ?: null;
//     $this->execReceiverName = trim(($s->r_first_name ?? '').' '.($s->r_last_name ?? '')) ?: null;

//     $this->dispatchBrowserEvent('modal:open', ['id' => 'executionProcess']);
// }


//     /** Close modal */
//     public function closeModal(): void
//     {
//         $this->execId = null;
//         $this->resetValidation();
//         $this->dispatchBrowserEvent('modal:close', ['id' => 'executionProcess']);
//     }

// public function markExecutedConfirmed(): void
// {
//     if (!$this->isAdmin) abort(403);
//     $this->validate();

//     DB::beginTransaction();
//     try {
//         // Lock the row in Pending state
//         $sender = Sender::query()
//             ->where('id', $this->execId)
//             ->where('status', 'Pending')
//             ->lockForUpdate()
//             ->first();
//             if (!$sender) {
//                 DB::rollBack();
//             $this->dispatchBrowserEvent('alert', [
//                 'type' => 'warning',
//                 'message' => __('Not allowed or already processed'),
//             ]);
//             $this->closeModal();
//             return;
//         }

//         // Apply edits
//         $sender->update([
//             'mtcn'       => $this->newMtcn,
//             'first_name' => $this->newFirstName,
//             'last_name'  => $this->newLastName,
//         ]);
        
//         // Canonical status change (Pending -> Executed)
//         $this->internalChangeStatus($sender, 'Executed');

//         DB::commit();

//         $this->dispatchBrowserEvent('alert', [
//             'type' => 'success',
//             'message' => __('Updated & marked as Executed'),
//         ]);
//     } catch (\Throwable $e) {
//         DB::rollBack();
//         dd($e);
//         // Log::error('Execution confirm failed', ['error' => $e->getMessage()]);
//         $this->dispatchBrowserEvent('alert', [
//             'type' => 'warning',
//             'message' => __('Operation failed'),
//         ]);
//     }

//     $this->closeModal();
// }

// public function markPending(int $id): void
// {
//     if (!$this->isAdmin) abort(403);

//     // Allow Executed/Rejected -> Pending
//     $sender = Sender::query()
//         ->where('id', $id)
//         ->whereIn('status', ['Executed','Rejected'])
//         ->first();

//     if (!$sender) {
//         $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
//         return;
//     }
//     $this->internalChangeStatus($sender, 'Pending');
// }

// public function markRejected(int $id): void
// {
//     if (!$this->isAdmin) abort(403);

//     // Allow Pending/Executed -> Rejected (if that’s your policy)
//     $sender = Sender::query()
//         ->where('id', $id)
//         ->whereIn('status', ['Pending','Executed'])
//         ->first();

//     if (!$sender) {
//         $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
//         return;
//     }
//     $this->internalChangeStatus($sender, 'Rejected');
// }


// protected function internalChangeStatus(Sender $sender, string $to): void
// {
//     if (!in_array($to, ['Pending','Executed','Rejected'], true)) {
//         $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Invalid status')]);
//         return;
//     }

//     $from = (string) $sender->status;
//     if ($from === $to) {
//         $this->dispatchBrowserEvent('alert', ['type'=>'info','message'=>__('No change')]);
//         return;
//     }

//     // Update status
//     $ok = $sender->update(['status' => $to]);

//     if ($ok) {
//         // success toast
//         $this->dispatchBrowserEvent('alert', [
//             'type'=>'success',
//             'message'=>__('Marked :status', ['status'=>$to])
//         ]);

//         // If current page becomes empty after removal, move back a page
//         if (method_exists($this, 'rows') && $this->rows()->isEmpty() && $this->page > 1) {
//             $this->previousPage();
//         }

//         // Telegram (wrap in try so UI never dies)
//         try {
//             Notification::route('toTelegram', null)->notify(new TeleNotifySenderAction(
//                 $sender->id,
//                 $sender->mtcn,
//                 trim(($sender->first_name ?? '').' '.($sender->last_name ?? '')),
//                 $sender->total,
//                 $from,
//                 $to,
//                 auth()->user()->name ?? 'system'
//             ));
//             $this->dispatchBrowserEvent('alert', [
//                 'type' => 'success',
//                 'message' => __('Push Activated'),
//             ]);
//         } catch (\Throwable $e) {
//             // \Log::warning('Telegram notify failed', ['e' => $e->getMessage()]);
//             $this->dispatchBrowserEvent('alert', [
//                 'type' => 'warning',
//                 'message' => __('Did not saved in cloud!'),
//             ]);
//         }

//         // WhatsApp only when moving into Executed
//         if ($to === 'Executed') {
//             try {
//                 $phoneId  = config('services.whatsapp.phone_id');
//                 $token    = config('services.whatsapp.token');
//                 $toNumber = config('services.whatsapp.test_to');  // in E.164 without '+'
//                 $template = config('services.whatsapp.template_sender');
//                 $lang     = config('services.whatsapp.lang', 'en'); // <-- use en, not en_US

//                 $customerName = trim(($sender->first_name ?? '') . ' ' . ($sender->last_name ?? '')) ?: 'Customer';
//                 $mtcn         = (string) $sender->mtcn;

//                 $payload = [
//                     'messaging_product' => 'whatsapp',
//                     'to' => $toNumber,
//                     'type' => 'template',
//                     'template' => [
//                         'name' => $template,
//                         'language' => ['code' => $lang],   // <-- match the template locale
//                         'components' => [[
//                             'type' => 'body',
//                             'parameters' => [
//                                 ['type' => 'text', 'parameter_name' => 'text', 'text' => $customerName],  // {{1}}
//                                 ['type' => 'text', 'parameter_name' => 'mtcn', 'text' => 'mtcn-' . $mtcn] // {{2}}
//                             ],
//                         ]],
//                     ],
//                 ];

//                 $resp = Http::withToken($token)
//                     ->acceptJson()
//                     ->asJson()
//                     ->post("https://graph.facebook.com/v22.0/{$phoneId}/messages", $payload);

//                 if (!$resp->successful()) {
//                     Log::error('WhatsApp API error', ['status' => $resp->status(), 'body' => $resp->body()]);
//                     $this->dispatchBrowserEvent('alert', [
//                         'type' => 'warning',
//                         'message' => __('WhatsApp push failed (:code)', ['code' => $resp->status()]),
//                     ]);
//                 } else {
//                     $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('WhatsApp push sent')]);
//                 }
//             } catch (\Throwable $e) {
//                 Log::error('WhatsApp push exception', ['error' => $e->getMessage()]);
//                 $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('WhatsApp push failed')]);
//             }
//         }
//     } else {
//         $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
//     }
// }

// /** ------------------------------
//  *  RECEIVER STATUS TRANSITIONS
//  *  ------------------------------ */

// // Public entry points (called from Blade)
// public function rMarkExecuted(int $receiverId): void
// {
//     if (!$this->isAdmin) abort(403);

//     $receiver = Receiver::query()
//         ->where('id', $receiverId)
//         ->where('status', 'Pending')->orWhere('status', 'Rejected')
//         ->when(!$this->isAdmin, fn($q) => $q->where('user_id', auth()->id()))
//         ->first();

//     if (!$receiver) {
//         dd($receiver);
//         $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
//         return;
//     }
//     $this->internalChangeStatusReceiver($receiver, 'Executed');
// }

// public function rMarkPending(int $receiverId): void
// {
//     if (!$this->isAdmin) abort(403);

//     $receiver = Receiver::query()
//         ->where('id', $receiverId)
//         ->whereIn('status', ['Executed','Rejected'])
//         ->when(!$this->isAdmin, fn($q) => $q->where('user_id', auth()->id()))
//         ->first();
//         if (!$receiver) {
//             $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
//         return;
//     }
//     $this->internalChangeStatusReceiver($receiver, 'Pending');
// }

// public function rMarkRejected(int $receiverId): void
// {
//     if (!$this->isAdmin) abort(403);

//     $receiver = Receiver::query()
//         ->where('id', $receiverId)
//         ->whereIn('status', ['Pending','Executed'])
//         ->when(!$this->isAdmin, fn($q) => $q->where('user_id', auth()->id()))
//         ->first();

//     if (!$receiver) {
//         $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
//         return;
//     }
//     $this->internalChangeStatusReceiver($receiver, 'Rejected');
// }

// /** Core receiver changer (kept separate from Sender) */
// protected function internalChangeStatusReceiver(Receiver $receiver, string $to): void
// {
//     if (!in_array($to, ['Pending','Executed','Rejected'], true)) {
//         $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Invalid status')]);
//         return;
//     }
// // dd($receiver, $to);
//     $from = (string) $receiver->status;
//     if ($from === $to) {
//         $this->dispatchBrowserEvent('alert', ['type'=>'info','message'=>__('No change')]);
//         return;
//     }

//     DB::beginTransaction();
//     try {
//         // Lock latest row state
//         $receiver = Receiver::query()->whereKey($receiver->id)->lockForUpdate()->first();
//         if (!$receiver) {
//             DB::rollBack();
//             $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
//             return;
//         }

//         $from = (string) $receiver->status;

//         // 1) Update status
//         $receiver->update(['status' => $to]);

//         // 2) Ledger effects (IQD) — credit on Executed, optional reverse when leaving Executed
//         if ($from !== 'Executed' && $to === 'Executed') {
//             ReceiverBalance::create([
//                 'user_id'     => $receiver->user_id,
//                 'receiver_id' => $receiver->id,
//                 'amount'      => (int) $receiver->amount_iqd, // IQD
//                 'status'      => 'Incoming',
//                 'note'        => 'Receiver executed',
//             ]);
//         } elseif ($from === 'Executed' && $to !== 'Executed') {
//             // Optional: create a reversing entry
//             ReceiverBalance::create([
//                 'user_id'     => $receiver->user_id,
//                 'receiver_id' => $receiver->id,
//                 'amount'      => $receiver->amount_iqd,
//                 'status'      => 'Outgoing',
//                 'note'        => "Receiver moved from Executed to {$to}",
//             ]);
//         }

//         DB::commit();

//         $this->dispatchBrowserEvent('alert', [
//             'type'=>'success',
//             'message'=>__('Marked :status', ['status'=>$to]),
//         ]);

//         // Keep pagination sensible
//         if ($this->tab === 'receivers') $this->resetPage();

//         // 3) Telegram notify (all transitions)
//         try {
//             Notification::route('toTelegram', null)->notify(new TeleNotifyReceiverAction(
//                 $receiver->id,
//                 $receiver->mtcn,
//                 trim(($receiver->first_name ?? '').' '.($receiver->last_name ?? '')),
//                 (float) $receiver->amount_iqd,
//                 $from,
//                 $to,
//                 auth()->user()->name ?? 'system'
//             ));
//         } catch (\Throwable $e) {
//             Log::warning('TeleNotifyReceiverAction failed', ['e'=>$e->getMessage()]);
//         }

//         // 4) WhatsApp ONLY for Pending → Executed
//         if ($from === 'Pending' && $to === 'Executed') {
//             $this->sendWhatsAppReceiverExecuted($receiver);
//         }

//     } catch (\Throwable $e) {
//         DB::rollBack();
//         dd($e);
//         Log::error('Receiver status change failed', ['error'=>$e->getMessage()]);
//         $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Something went wrong!')]);
//     }
// }

// /** WhatsApp for Receivers on execution */
// protected function sendWhatsAppReceiverExecuted(Receiver $receiver): void
// {
//     try {
//         $phoneId  = config('services.whatsapp.phone_id');
//         $token    = config('services.whatsapp.token');
//         $toNumber = config('services.whatsapp.test_to');
//         $template = config('services.whatsapp.template_receiver'); // e.g. msg_v3_receiver
//         $lang     = config('services.whatsapp.lang', 'en');

//         if (!$phoneId || !$token || !$toNumber || !$template) {
//             Log::warning('WA Receiver: missing config', compact('phoneId','toNumber','template','lang'));
//             return;
//         }

//         $customerName = trim(($receiver->first_name ?? '').' '.($receiver->last_name ?? '')) ?: 'Customer';
//         $mtcn         = (string) $receiver->mtcn;

//         $payload = [
//             'messaging_product' => 'whatsapp',
//             'to' => $toNumber,
//             'type' => 'template',
//             'template' => [
//                 'name' => $template,
//                 'language' => ['code' => $lang],
//                 'components' => [[
//                     'type' => 'body',
//                     'parameters' => [
//                         ['type' => 'text', 'parameter_name' => 'text', 'text' => $customerName],     // {{text}}
//                         ['type' => 'text', 'parameter_name' => 'mtcn', 'text' => 'mtcn-'.$mtcn],      // {{mtcn}}
//                     ],
//                 ]],
//             ],
//         ];

//         $resp = Http::withToken($token)->acceptJson()->asJson()
//             ->post("https://graph.facebook.com/v22.0/{$phoneId}/messages", $payload);

//         if (!$resp->successful()) {
//             Log::error('WhatsApp API error (receiver)', ['status'=>$resp->status(), 'body'=>$resp->body()]);
//             $this->dispatchBrowserEvent('alert', [
//                 'type' => 'warning',
//                 'message' => __('WhatsApp push failed (:code)', ['code' => $resp->status()]),
//             ]);
//         } else {
//             $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('WhatsApp push sent')]);
//         }
//     } catch (\Throwable $e) {
//         Log::error('WhatsApp push exception (receiver)', ['error' => $e->getMessage()]);
//         $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('WhatsApp push failed')]);
//     }
// }


}
