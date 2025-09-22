<?php

namespace App\Http\Livewire\General;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class DashboardLivewire extends Component
{
    // Filters
    public string $dateFilter = 'this_month';
    public int $monthsBack = 12;
    public bool $autoRefresh = false;

    // Tables present?
    public bool $hasSenders = false;
    public bool $hasReceivers = false;

    // Role flags
    public bool $isAdmin = false;
    public ?int $userId = null;

    public float $lifetimeSenderBalanceUSD = 0.0;
    public int   $lifetimeReceiverBalanceIQD = 0;

    // Loading states
    public bool $isLoading = false;
    public array $loadingStates = [
        'kpis'   => false,
        'charts' => false,
        'pies'   => false,
    ];

    // KPIs with trends (period vs previous period)
    public array $kpis = [
        'senders_total'    => ['current' => 0, 'previous' => 0, 'trend' => 0],
        'receivers_total'  => ['current' => 0, 'previous' => 0, 'trend' => 0],
        'amount_senders'   => ['current' => 0.0, 'previous' => 0.0, 'trend' => 0],
        'amount_receivers' => ['current' => 0.0, 'previous' => 0.0, 'trend' => 0],
    ];

    // Charts
    public array $sendersAmountChart   = ['labels' => [], 'series' => [[]], 'colors' => ['#6366f1'], 'gradient' => ['from' => '#6366f1', 'to' => '#8b5cf6']];
    public array $receiversAmountChart = ['labels' => [], 'series' => [[]], 'colors' => ['#10b981'], 'gradient' => ['from' => '#10b981', 'to' => '#059669']];

    // Pies (+ legend items + totals)
    public array $pieTopSenders   = ['labels' => [], 'series' => [], 'colors' => [], 'items' => [], 'total' => 0];
    public array $pieTopReceivers = ['labels' => [], 'series' => [], 'colors' => [], 'items' => [], 'total' => 0];
    public array $pieTopCountries = ['labels' => [], 'series' => [], 'colors' => [], 'items' => [], 'total' => 0];

    protected $listeners = [
        'refreshDashboard'   => 'refreshData',
        'toggleAutoRefresh'  => 'toggleAutoRefresh',
    ];

    public function mount(): void
    {
        $this->hasSenders   = Schema::hasTable('senders');
        $this->hasReceivers = Schema::hasTable('receivers');

        $user = auth()->user();
        $this->userId  = $user?->id;
        $this->isAdmin = (int)($user->role ?? 1) === 1; // role 1 = Admin, 2 = Register

        $this->refreshData();
    }

    public function updatedDateFilter(): void  { $this->refreshData(); }
    public function updatedMonthsBack(): void  { $this->refreshData(); }

    public function toggleAutoRefresh(): void
    {
        $this->autoRefresh = !$this->autoRefresh;
        $this->dispatchBrowserEvent($this->autoRefresh ? 'start-auto-refresh' : 'stop-auto-refresh');
    }

    public function refreshData(): void
    {
        $this->isLoading     = true;
        $this->loadingStates = ['kpis'=>true, 'charts'=>true, 'pies'=>true];

        try {
            $this->loadLifetimeBalances();

            [$from, $to]           = $this->dateRange();
            [$prevFrom, $prevTo]   = $this->previousDateRange();

            // KPIs
            $this->loadKpis($from, $to, $prevFrom, $prevTo);
            $this->loadingStates['kpis'] = false;

            // Line charts
            $this->loadSendersAmountChart();
            $this->loadReceiversAmountChart();
            $this->loadingStates['charts'] = false;

            // Pies
            $this->loadPieTopSenders($from, $to);
            $this->loadPieTopReceivers($from, $to);
            $this->loadPieTopCountries($from, $to);
            $this->loadingStates['pies'] = false;

            // Push chart payloads to the front-end
            $this->dispatchBrowserEvent('charts:update', [
                'sendersAmount'   => $this->sendersAmountChart,
                'receiversAmount' => $this->receiversAmountChart,
                'pieTopSenders'   => $this->pieTopSenders,
                'pieTopReceivers' => $this->pieTopReceivers,
                'pieTopCountries' => $this->pieTopCountries,
            ]);

            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'Dashboard updated successfully!',
            ]);

        } catch (\Throwable $e) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'Failed to update dashboard: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isLoading     = false;
            $this->loadingStates = ['kpis'=>false, 'charts'=>false, 'pies'=>false];
        }
    }

        protected function loadLifetimeBalances(): void
        {
            // ---- Sender (USD) — unchanged
            if (Schema::hasTable('sender_balances')) {
                $sb = DB::table('sender_balances as sb');
                if (!$this->isAdmin && $this->userId) {
                    $sb->where('sb.user_id', $this->userId);
                }
                $incoming = (float) (clone $sb)->where('sb.status', 'Incoming')->sum('sb.amount');
                $outgoing = (float) (clone $sb)->whereIn('sb.status', ['Outgoing','outcoming'])->sum('sb.amount');
                $this->lifetimeSenderBalanceUSD = round($incoming - $outgoing, 2);
            } else {
                $this->lifetimeSenderBalanceUSD = 0.0;
            }

            // ---- Receiver (IQD)
            $executedIncoming = 0;
            if (Schema::hasTable('receivers')) {
                $r = DB::table('receivers as r')->where('r.status', 'Executed');
                if (!$this->isAdmin && $this->userId) {
                    $r->where('r.user_id', $this->userId);
                }
                // Sum of executed receiver amounts
                $executedIncoming = (int) $r->sum('r.amount_iqd');
            }

            $incomingAdjustments = 0;
            $outgoingResets = 0;
            if (Schema::hasTable('receiver_balances')) {
                $rb = DB::table('receiver_balances as rb');
                if (!$this->isAdmin && $this->userId) {
                    $rb->where('rb.user_id', $this->userId);
                }

                // Manual incoming adjustments not tied to a specific receiver row
                $incomingAdjustments = (int) (clone $rb)
                    ->where('rb.status', 'Incoming')
                    ->whereNull('rb.receiver_id')   // avoid double counting executed receivers
                    ->sum('rb.amount');

                // Outgoing (admin resets) always subtract
                $outgoingResets = (int) (clone $rb)
                    ->where('rb.status', 'Outgoing')
                    ->sum('rb.amount');
            }
            $this->lifetimeReceiverBalanceIQD = max(0, $executedIncoming + $incomingAdjustments - $outgoingResets);
            // dd($executedIncoming, $incomingAdjustments, $outgoingResets);
        }
    protected function dateRange(): array
    {
        $now = Carbon::now();
        return match ($this->dateFilter) {
            'today'        => [$now->copy()->startOfDay(),     $now->copy()->endOfDay()],
            'yesterday'    => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'this_week'    => [$now->copy()->startOfWeek(),    $now->copy()->endOfWeek()],
            'last_week'    => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'this_month'   => [$now->copy()->startOfMonth(),   $now->copy()->endOfMonth()],
            'last_month'   => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_quarter' => [$now->copy()->firstOfQuarter(), $now->copy()->lastOfQuarter()],
            'last_quarter' => [($q=$now->copy()->subQuarter())->firstOfQuarter(), $q->lastOfQuarter()],
            'this_year'    => [$now->copy()->startOfYear(),    $now->copy()->endOfYear()],
            'last_year'    => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            default        => [$now->copy()->startOfMonth(),   $now->copy()->endOfMonth()],
        };
    }

    protected function previousDateRange(): array
    {
        // Same duration immediately before current period
        [$from, $to] = $this->dateRange();
        $diffDays    = $from->diffInDays($to) + 1;
        $prevTo      = $from->copy()->subDay()->endOfDay();
        $prevFrom    = $prevTo->copy()->subDays($diffDays-1)->startOfDay();
        return [$prevFrom, $prevTo];
    }

    protected function monthBuckets(): array
    {
        $end   = Carbon::now()->endOfMonth();
        $start = Carbon::now()->copy()->subMonths($this->monthsBack - 1)->startOfMonth();
        $labels = []; $keys = [];
        $cur = $start->copy();
        while ($cur->lte($end)) {
            $labels[] = $cur->format('M Y');
            $keys[]   = $cur->format('Y-m');
            $cur->addMonth();
        }
        return [$start, $end, $labels, $keys];
    }

    /** Role scope: admin sees all, register sees own user_id */
    protected function scopeByRole($qb, string $alias)
    {
        $qb->whereNull("$alias.deleted_at");
        if (!$this->isAdmin && $this->userId) {
            $qb->where("$alias.user_id", $this->userId);
        }
        return $qb;
    }

    protected function calculateTrend(float|int $current, float|int $previous): float|int
    {
        if ((float)$previous == 0.0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    // -------- KPIs (period vs previous period) --------
    protected function loadKpis(Carbon $from, Carbon $to, Carbon $prevFrom, Carbon $prevTo): void
    {
        // Counts (period)
        $sendersCurrent = $this->hasSenders
            ? (clone $this->scopeByRole(DB::table('senders as s'), 's'))->whereBetween('s.created_at', [$from, $to])->count()
            : 0;

        $receiversCurrent = $this->hasReceivers
            ? (clone $this->scopeByRole(DB::table('receivers as r'), 'r'))->whereBetween('r.created_at', [$from, $to])->count()
            : 0;

        // Counts (previous)
        $sendersPrev = $this->hasSenders
            ? (clone $this->scopeByRole(DB::table('senders as s'), 's'))->whereBetween('s.created_at', [$prevFrom, $prevTo])->count()
            : 0;

        $receiversPrev = $this->hasReceivers
            ? (clone $this->scopeByRole(DB::table('receivers as r'), 'r'))->whereBetween('r.created_at', [$prevFrom, $prevTo])->count()
            : 0;

        // Amounts (period)
        $sendersAmountCurrent = $this->hasSenders
            ? (float)(clone $this->scopeByRole(DB::table('senders as s'), 's'))
                ->whereBetween('s.created_at', [$from, $to])
                ->sum('s.amount')
            : 0.0;

        $sendersAmountPrev = $this->hasSenders
            ? (float)(clone $this->scopeByRole(DB::table('senders as s'), 's'))
                ->whereBetween('s.created_at', [$prevFrom, $prevTo])
                ->sum('s.amount')
            : 0.0;

        // Receivers amounts (IQD preferred)
        $receiversAmountCurrent = 0.0;
        $receiversAmountPrev    = 0.0;

        if ($this->hasReceivers) {
            $col = $this->detectReceiverAmountColumn();
            if ($col) {
                $receiversAmountCurrent = (float)(clone $this->scopeByRole(DB::table('receivers as r'), 'r'))
                    ->whereBetween('r.created_at', [$from, $to])
                    ->sum("r.$col");

                $receiversAmountPrev = (float)(clone $this->scopeByRole(DB::table('receivers as r'), 'r'))
                    ->whereBetween('r.created_at', [$prevFrom, $prevTo])
                    ->sum("r.$col");
            }
        }

        $this->kpis = [
            'senders_total' => [
                'current'  => $sendersCurrent,
                'previous' => $sendersPrev,
                'trend'    => $this->calculateTrend($sendersCurrent, $sendersPrev),
            ],
            'receivers_total' => [
                'current'  => $receiversCurrent,
                'previous' => $receiversPrev,
                'trend'    => $this->calculateTrend($receiversCurrent, $receiversPrev),
            ],
            'amount_senders' => [
                'current'  => round($sendersAmountCurrent, 2),
                'previous' => round($sendersAmountPrev, 2),
                'trend'    => $this->calculateTrend($sendersAmountCurrent, $sendersAmountPrev),
            ],
            'amount_receivers' => [
                'current'  => round($receiversAmountCurrent, 2),
                'previous' => round($receiversAmountPrev, 2),
                'trend'    => $this->calculateTrend($receiversAmountCurrent, $receiversAmountPrev),
            ],
        ];
    }

    protected function detectReceiverAmountColumn(): ?string
    {
        foreach (['amount_iqd','total_iqd','amount','total'] as $c) {
            if (Schema::hasColumn('receivers', $c)) return $c;
        }
        return null;
    }

    protected function detectReceiverNameColumns(): array
    {
        $first = Schema::hasColumn('receivers','first_name') ? 'first_name'
               : (Schema::hasColumn('receivers','fname') ? 'fname' : null);
        $last  = Schema::hasColumn('receivers','last_name') ? 'last_name'
               : (Schema::hasColumn('receivers','lname') ? 'lname' : null);
        return [$first, $last];
    }

    // -------- Line Charts (12 months, amounts) --------
    protected function loadSendersAmountChart(): void
    {
        if (!$this->hasSenders) {
            $this->sendersAmountChart = ['labels'=>[], 'series'=>[[]], 'colors'=>['#6366f1'], 'gradient'=>['from'=>'#6366f1','to'=>'#8b5cf6']];
            return;
        }

        [$start, $end, $labels, $keys] = $this->monthBuckets();

        $rows = (clone $this->scopeByRole(DB::table('senders as s'), 's'))
            ->whereBetween('s.created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(s.created_at, '%Y-%m') as ym, COALESCE(SUM(s.amount),0) as amt")
            ->groupBy('ym')
            ->pluck('amt', 'ym');

        $series = [ array_map(fn($k)=> (float)($rows[$k] ?? 0), $keys) ];

        $this->sendersAmountChart = [
            'labels'   => $labels,
            'series'   => $series,
            'colors'   => ['#6366f1'],
            'gradient' => ['from' => '#6366f1', 'to' => '#8b5cf6'],
        ];
    }

    protected function loadReceiversAmountChart(): void
    {
        if (!$this->hasReceivers) {
            $this->receiversAmountChart = ['labels'=>[], 'series'=>[[]], 'colors'=>['#10b981'], 'gradient'=>['from'=>'#10b981','to'=>'#059669']];
            return;
        }

        [$start, $end, $labels, $keys] = $this->monthBuckets();
        $col = $this->detectReceiverAmountColumn();
        if (!$col) {
            $this->receiversAmountChart = ['labels'=>$labels, 'series'=>[array_fill(0, count($labels), 0)], 'colors'=>['#10b981'], 'gradient'=>['from'=>'#10b981','to'=>'#059669']];
            return;
        }

        $rows = (clone $this->scopeByRole(DB::table('receivers as r'), 'r'))
            ->whereBetween('r.created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(r.created_at, '%Y-%m') as ym, COALESCE(SUM(r.$col),0) as amt")
            ->groupBy('ym')
            ->pluck('amt', 'ym');

        $series = [ array_map(fn($k)=> (float)($rows[$k] ?? 0), $keys) ];

        $this->receiversAmountChart = [
            'labels'   => $labels,
            'series'   => $series,
            'colors'   => ['#10b981'],
            'gradient' => ['from' => '#10b981', 'to' => '#059669'],
        ];
    }

    // -------- Pie Charts (Top 5) + legend items --------
protected function loadPieTopSenders(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): void
{
    if (!$this->hasSenders) { $this->pieTopSenders = ['labels'=>[], 'series'=>[], 'colors'=>[], 'items'=>[], 'total'=>0]; return; }

    $rows = (clone $this->scopeByRole(DB::table('senders as s'), 's'))
        ->whereBetween('s.created_at', [$from, $to])
        ->selectRaw("TRIM(CONCAT(s.first_name,' ',s.last_name)) as label, COALESCE(SUM(s.amount),0) as amt")
        ->groupBy('label')
        ->orderByDesc('amt')
        ->limit(5)
        ->get();

    $base = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b'];     // vivid
    $tinted = $this->tintPalette($base, 0.42);                        // softer!

    $series = $rows->pluck('amt')->map(fn($v)=>(float)$v)->all();
    $total  = array_sum($series) ?: 0;

    $items = $rows->map(function($r) use ($total){
        $amt = (float)$r->amt;
        return ['label'=>$r->label ?: '—', 'amount'=>$amt, 'pct'=>$total>0?round(($amt/$total)*100):0];
    })->values()->all();

    $this->pieTopSenders = [
        'labels' => $rows->pluck('label')->map(fn($v)=>$v ?: '—')->all(),
        'series' => $series,
        'colors' => array_slice($tinted, 0, count($series)),
        'items'  => $items,
        'total'  => $total,
    ];
}

protected function loadPieTopReceivers(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): void
{
    if (!$this->hasReceivers) { $this->pieTopReceivers = ['labels'=>[], 'series'=>[], 'colors'=>[], 'items'=>[], 'total'=>0]; return; }

    $col = $this->detectReceiverAmountColumn();
    if (!$col) { $this->pieTopReceivers = ['labels'=>[], 'series'=>[], 'colors'=>[], 'items'=>[], 'total'=>0]; return; }

    [$fn,$ln] = $this->detectReceiverNameColumns();
    $nameExpr = $fn && $ln
        ? "TRIM(CONCAT(r.$fn,' ',r.$ln))"
        : (Schema::hasColumn('receivers','r_phone') ? 'r.r_phone'
           : (Schema::hasColumn('receivers','phone') ? 'r.phone'
              : (Schema::hasColumn('receivers','mtcn') ? 'r.mtcn' : "'Receiver'")));

    $rows = (clone $this->scopeByRole(DB::table('receivers as r'), 'r'))
        ->whereBetween('r.created_at', [$from, $to])
        ->selectRaw("$nameExpr as label, COALESCE(SUM(r.$col),0) as amt")
        ->groupBy('label')
        ->orderByDesc('amt')
        ->limit(5)
        ->get();

    $base = ['#10b981','#06b6d4','#8b5cf6','#f59e0b','#ef4444'];
    $tinted = $this->tintPalette($base, 0.42);

    $series = $rows->pluck('amt')->map(fn($v)=>(float)$v)->all();
    $total  = array_sum($series) ?: 0;

    $items = $rows->map(function($r) use ($total){
        $amt = (float)$r->amt;
        return ['label'=>$r->label ?: '—', 'amount'=>$amt, 'pct'=>$total>0?round(($amt/$total)*100):0];
    })->values()->all();

    $this->pieTopReceivers = [
        'labels' => $rows->pluck('label')->map(fn($v)=>$v ?: '—')->all(),
        'series' => $series,
        'colors' => array_slice($tinted, 0, count($series)),
        'items'  => $items,
        'total'  => $total,
    ];
}

protected function loadPieTopCountries(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): void
{
    if (!$this->hasSenders) { $this->pieTopCountries = ['labels'=>[], 'series'=>[], 'colors'=>[], 'items'=>[], 'total'=>0]; return; }

    $canJoinCountries = Schema::hasTable('countries') && Schema::hasColumn('countries','en_name') && Schema::hasColumn('countries','en_name');

    if ($canJoinCountries) {
        $rows = (clone $this->scopeByRole(DB::table('senders as s'), 's'))
            ->whereBetween('s.created_at', [$from, $to])
            ->whereNotNull('s.country')
            ->leftJoin('countries as c', 'c.en_name', '=', DB::raw('UPPER(s.country)'))
            ->selectRaw("COALESCE(c.en_name, UPPER(s.country)) as label, COALESCE(SUM(s.amount),0) as amt")
            ->groupByRaw("COALESCE(c.en_name, UPPER(s.country))")
            ->orderByDesc('amt')
            ->limit(5)
            ->get();
    } else {
        $rows = (clone $this->scopeByRole(DB::table('senders as s'), 's'))
            ->whereBetween('s.created_at', [$from, $to])
            ->whereNotNull('s.country')
            ->selectRaw("UPPER(s.country) as label, COALESCE(SUM(s.amount),0) as amt")
            ->groupBy('label')
            ->orderByDesc('amt')
            ->limit(5)
            ->get();
    }

    $base = ['#f59e0b','#8b5cf6','#06b6d4','#10b981','#ef4444'];
    $tinted = $this->tintPalette($base, 0.42);

    $series = $rows->pluck('amt')->map(fn($v)=>(float)$v)->all();
    $total  = array_sum($series) ?: 0;

    $items = $rows->map(function($r) use ($total){
        $amt = (float)$r->amt;
        return ['label'=>$r->label ?: '—', 'amount'=>$amt, 'pct'=>$total>0?round(($amt/$total)*100):0];
    })->values()->all();

    $this->pieTopCountries = [
        'labels' => $rows->pluck('label')->all(),
        'series' => $series,
        'colors' => array_slice($tinted, 0, count($series)),
        'items'  => $items,
        'total'  => $total,
    ];
}


    public function render()
    {
        return view('components.general.dashboard');
    }

    /** Mix a hex color with white by a given ratio (0..1) for a pastel/tint look. */
    protected function tintHex(string $hex, float $ratio = 0.35): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $mix = function ($c) use ($ratio) {
            return (int) round((1 - $ratio) * $c + $ratio * 255);
        };

        return sprintf('#%02x%02x%02x', $mix($r), $mix($g), $mix($b));
    }

    protected function tintPalette(array $colors, float $ratio = 0.35): array
    {
        return array_map(fn($h) => $this->tintHex($h, $ratio), $colors);
    }

}
