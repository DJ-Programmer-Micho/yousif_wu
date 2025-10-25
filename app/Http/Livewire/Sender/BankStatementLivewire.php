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
}
