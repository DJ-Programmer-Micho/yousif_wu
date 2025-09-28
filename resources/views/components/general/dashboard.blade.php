<div class="dashboard-container">
  <!-- Toasts -->
  <div class="toast-container" id="toastContainer"></div>

  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="mb-2">
            <h1 class="h3 mb-1 font-weight-bold">{{ __('Financial Dashboard') }}</h1>
            <p class="text-muted mb-0">{{ __('Monitor your transfers and transactions in real-time') }}</p>
          </div>
          <div class="d-flex gap-2">
            <button class="modern-btn outline" wire:click="toggleAutoRefresh">
              <i class="fas fa-sync-alt {{ $autoRefresh ? 'fa-spin' : '' }}"></i>
              {{ __('Auto Refresh') }} {{ $autoRefresh ? 'ON' : 'OFF' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="filter-section">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
              <div class="kpi-icon primary mx-1" style="width: 40px; height: 40px; font-size: 16px;">
                <i class="fas fa-sliders-h"></i>
              </div>
              <div>
                <h6 class="mb-0 font-weight-bold">{{ __('Filters & Controls') }}</h6>
                <small class="text-muted">{{ __('Customize your dashboard view') }}</small>
              </div>
            </div>

            <div class="d-flex flex-wrap gap-3">
              <select class="modern-select" wire:model="dateFilter">
                <option value="today">{{ __('Today') }}</option>
                <option value="yesterday">{{ __('Yesterday') }}</option>
                <option value="this_week">{{ __('This Week') }}</option>
                <option value="last_week">{{ __('Last Week') }}</option>
                <option value="this_month">{{ __('This Month') }}</option>
                <option value="last_month">{{ __('Last Month') }}</option>
                <option value="this_quarter">{{ __('This Quarter') }}</option>
                <option value="last_quarter">{{ __('Last Quarter') }}</option>
                <option value="this_year">{{ __('This Year') }}</option>
                <option value="last_year">{{ __('Last Year') }}</option>
              </select>

              <select class="modern-select" wire:model="monthsBack">
                @foreach([3,6,9,12,18,24] as $m)
                  <option value="{{ $m }}">{{ $m }} {{ __('months') }}</option>
                @endforeach
              </select>

              <button class="modern-btn primary" wire:click="refreshData" {{ $isLoading ? 'disabled' : '' }}>
                @if($isLoading)
                  <div class="loading-spinner"></div>
                @else
                  <i class="fas fa-sync-alt"></i>
                @endif
                {{ __('Refresh') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
@livewire('general.announcement-show-livewire', ['limit' => 6, 'role' => 'Register'])

<div class="card-group-modern-2 mb-4">
  <div class="modern-header modern-header-bg1 p-3">
    <div class="d-flex align-items-start">
      <div class="mr-auto">
        <div class="small">
          {{ __('Balance Amount Senders (USD)') }}
          @if($isAdmin)
            <span class="badge badge-light ml-1">{{ __('All Registers') }}</span>
          @else
            <span class="badge badge-light ml-1">{{ __('Your Balance') }}</span>
          @endif
        </div>
        <div class="h3 kpi-value mb-1">
          <small>$</small> {{ number_format($lifetimeSenderBalanceUSD, 2) }}
        </div>
      </div>
    </div>
  </div>

  <div class="modern-header modern-header-bg2 p-3">
    <div class="d-flex align-items-start">
      <div class="mr-auto">
        <div class="small">
          {{ __('Balance Amount Receiver (IQD)') }}
          @if($isAdmin)
            <span class="badge badge-light ml-1">{{ __('All Registers') }}</span>
          @else
            <span class="badge badge-light ml-1">{{ __('Your Balance') }}</span>
          @endif
        </div>
        <div class="h3 kpi-value mb-1">
          <small>IQD</small> {{ number_format($lifetimeReceiverBalanceIQD) }}
        </div>
      </div>
    </div>
  </div>
</div>

    <!-- KPI Cards -->
    <div class="card-group-modern mb-4">
      <div class="neo-card p-3">
        <div class="d-flex align-items-start">
          <div class="mr-auto">
            <div class="muted small">{{ __('Total Senders') }}</div>
            <div class="h3 kpi-value mb-1">{{ number_format($kpis['senders_total']['current']) }}</div>
            <div class="muted tiny">{{ __('Prev:') }} {{ number_format($kpis['senders_total']['previous']) }}</div>
          </div>
          @php $t=$kpis['senders_total']['trend']; @endphp
          <span class="trend-badge {{ $t>=0 ? 'up' : 'down' }}">
            <i class="fas fa-{{ $t>=0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ abs($t) }}%
          </span>
        </div>
      </div>

      <div class="neo-card p-3">
        <div class="d-flex align-items-start">
          <div class="mr-auto">
            <div class="muted small">{{ __('Total Receivers') }}</div>
            <div class="h3 kpi-value mb-1">{{ number_format($kpis['receivers_total']['current']) }}</div>
            <div class="muted tiny">{{ __('Prev:') }} {{ number_format($kpis['receivers_total']['previous']) }}</div>
          </div>
          @php $t=$kpis['receivers_total']['trend']; @endphp
          <span class="trend-badge {{ $t>=0 ? 'up' : 'down' }}">
            <i class="fas fa-{{ $t>=0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ abs($t) }}%
          </span>
        </div>
      </div>

      <div class="neo-card p-3">
        <div class="d-flex align-items-start">
          <div class="mr-auto">
            <div class="muted small">{{ __('Amount Senders') }}</div>
            <div class="h3 kpi-value mb-1"><small class="muted">$</small> {{ number_format($kpis['amount_senders']['current'], 2) }}</div>
            <div class="muted tiny">{{ __('Prev:') }} {{ number_format($kpis['amount_senders']['previous'], 2) }}</div>
          </div>
          @php $t=$kpis['amount_senders']['trend']; @endphp
          <span class="trend-badge {{ $t>=0 ? 'up' : 'down' }}">
            <i class="fas fa-{{ $t>=0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ abs($t) }}%
          </span>
        </div>
      </div>

      <div class="neo-card p-3">
        <div class="d-flex align-items-start">
          <div class="mr-auto">
            <div class="muted small">{{ __('Amount Receiver (IQD)') }}</div>
            <div class="h3 kpi-value mb-1"><small class="muted">{{ __('IQD') }}</small> {{ number_format($kpis['amount_receivers']['current'], 2) }}</div>
            <div class="muted tiny">{{ __('Prev:') }} {{ number_format($kpis['amount_receivers']['previous'], 2) }}</div>
          </div>
          @php $t=$kpis['amount_receivers']['trend']; @endphp
          <span class="trend-badge {{ $t>=0 ? 'up' : 'down' }}">
            <i class="fas fa-{{ $t>=0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ abs($t) }}%
          </span>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row">
      <div class="col-lg-8">
        <!-- Senders Amount -->
        <div class="chart-container mb-4">
          <div class="chart-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
              <div class="kpi-icon primary" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-chart-line"></i></div>
              <div>
                <h6 class="chart-title">{{ __('Senders Amount Trend') }}</h6>
                <p class="chart-subtitle">{{ __('Last') }} {{ $monthsBack }} {{ __('months performance') }}</p>
              </div>
            </div>
            <span class="badge badge-soft-primary"><i class="fas fa-circle mr-1"></i> {{ __('Amount ($)') }}</span>
          </div>
          <div class="p-3">
            @if($loadingStates['charts'])
              <div class="loading-skeleton" style="height: 280px; border-radius: 8px;"></div>
            @else
              <div id="sendersAmountChart" class="ct-chart chart-animate" style="height: 280px;" wire:ignore></div>
            @endif
          </div>
        </div>

        <!-- Receivers Amount -->
        <div class="chart-container mb-4">
          <div class="chart-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
              <div class="kpi-icon success" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-chart-line"></i></div>
              <div>
                <h6 class="chart-title">{{ __('Receivers Amount Trend') }}</h6>
                <p class="chart-subtitle">{{ __('Last') }} {{ $monthsBack }} {{ __('months in IQD') }}</p>
              </div>
            </div>
            <span class="badge badge-soft-success"><i class="fas fa-circle mr-1"></i> {{ __('Amount (IQD)') }}</span>
          </div>
          <div class="p-3">
            @if($loadingStates['charts'])
              <div class="loading-skeleton" style="height: 280px; border-radius: 8px;"></div>
            @else
              <div id="receiversAmountChart" class="ct-chart chart-animate" style="height: 280px;" wire:ignore></div>
            @endif
          </div>
        </div>
<!-- TradingView Widget BEGIN -->
{{-- <div class="chart-container mb-4"> --}}
{{-- <div class="tradingview-widget-container" style="max-height:600px;width:100%;">
  <div class="tradingview-widget-container__widget" style="max-height:600px;width:100%"></div>
  
  <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
  {
  "allow_symbol_change": true,
  "calendar": false,
  "details": false,
  "hide_side_toolbar": true,
  "hide_top_toolbar": false,
  "hide_legend": false,
  "hide_volume": false,
  "hotlist": false,
  "interval": "D",
  "locale": "en",
  "save_image": true,
  "style": "1",
  "symbol": "OANDA:XAUUSD",
  "theme": "light",
  "timezone": "Etc/UTC",
  "backgroundColor": "#ffffff",
  "gridColor": "rgba(46, 46, 46, 0.06)",
  "watchlist": [],
  "withdateranges": false,
  "compareSymbols": [],
  "studies": [],
  "autosize": true
}
  </script>
</div> --}}
{{-- </div> --}}
<!-- TradingView Widget END -->
{{-- 
    <div class="row mb-5">
        <div class="col-md-4">
            <!-- TradingView Widget BEGIN -->
            <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
            
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-single-quote.js" async>
            {
            "symbol": "MARKETSCOM:OIL",
            "colorTheme": "light",
            "isTransparent": false,
            "locale": "en",
            "width": "100%"
            }
            </script>
            </div>
            <!-- TradingView Widget END -->
        </div>
        <div class="col-md-4">
            <!-- TradingView Widget BEGIN -->

            <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
            
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-single-quote.js" async>
            {
            "symbol": "FX_IDC:EURIQD",
            "colorTheme": "light",
            "isTransparent": false,
            "locale": "en",
            "width": "100%"
            }
            </script>
            </div>
            <!-- TradingView Widget END -->
        </div>
        <div class="col-md-4">
            <!-- TradingView Widget BEGIN -->
            <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
            
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-single-quote.js" async>
            {
            "symbol": "FX_IDC:IQDUSD",
            "colorTheme": "light",
            "isTransparent": false,
            "locale": "en",
            "width": "100%"
            }
            </script>
            </div>
            <!-- TradingView Widget END -->
        </div>
    </div> --}}


      </div>

      

      <!-- Pies + bullet legends -->
      <div class="col-lg-4">
        <!-- Top Senders -->
        <div class="chart-container mb-4">
          <div class="chart-header d-flex align-items-center gap-3">
            <div class="kpi-icon primary" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-user-tie"></i></div>
            <div>
              <h6 class="chart-title">{{ __('Top 5 Senders') }}</h6>
              <p class="chart-subtitle">By amount sent</p>
            </div>
          </div>
          <div class="p-3 position-relative">
            @if($loadingStates['pies'])
              <div class="loading-skeleton" style="height: 220px; border-radius: 8px;"></div>
            @else
              <div id="pieTopSenders" class="ct-chart chart-animate w-100 h-100" wire:ignore></div>
              <div class="pie-center">
                <p class="pie-center-title">{{ __('Total') }}</p>
                <p class="pie-center-value" id="pieTopSendersTotal">{{ number_format($pieTopSenders['total'] ?? 0, 0) }}</p>
              </div>

              <div class="legend-list">
                @foreach(($pieTopSenders['items'] ?? []) as $i => $it)
                  @php $color = $pieTopSenders['colors'][$i] ?? '#64748b'; @endphp
                  <div class="legend-item">
                    <div class="legend-left">
                      <span class="legend-dot" style="background: {{ $color }}"></span>
                      <span class="legend-label">{{ $it['label'] }}</span>
                    </div>
                    <div class="legend-right">{{ number_format($it['amount'], 0) }} <small class="text-muted">{{ $it['pct'] }}%</small></div>
                  </div>
                  <div class="progress"><div class="progress-bar" style="width: {{ $it['pct'] }}%; background: {{ $color }}"></div></div>
                @endforeach
                @if(empty($pieTopSenders['items'])) <div class="text-muted small text-center py-2">{{ __('No data') }}</div> @endif
              </div>
            @endif
          </div>
        </div>

        <!-- Top Receivers -->
        <div class="chart-container mb-4">
          <div class="chart-header d-flex align-items-center gap-3">
            <div class="kpi-icon success" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-user-shield"></i></div>
            <div>
              <h6 class="chart-title">{{ __('Top 5 Receivers') }}</h6>
              <p class="chart-subtitle">{{ __('By amount received') }}</p>
            </div>
          </div>
          <div class="p-3 position-relative">
            @if($loadingStates['pies'])
              <div class="loading-skeleton" style="height: 220px; border-radius: 8px;"></div>
            @else
              <div id="pieTopReceivers" class="ct-chart chart-animate" style="height: 220px;" wire:ignore></div>
              <div class="pie-center">
                <p class="pie-center-title">{{ __('Total') }}</p>
                <p class="pie-center-value" id="pieTopReceiversTotal">{{ number_format($pieTopReceivers['total'] ?? 0, 0) }}</p>
              </div>

              <div class="legend-list">
                @foreach(($pieTopReceivers['items'] ?? []) as $i => $it)
                  @php $color = $pieTopReceivers['colors'][$i] ?? '#64748b'; @endphp
                  <div class="legend-item">
                    <div class="legend-left">
                      <span class="legend-dot" style="background: {{ $color }}"></span>
                      <span class="legend-label">{{ $it['label'] }}</span>
                    </div>
                    <div class="legend-right">{{ number_format($it['amount'], 0) }} <small class="text-muted">{{ $it['pct'] }}%</small></div>
                  </div>
                  <div class="progress"><div class="progress-bar" style="width: {{ $it['pct'] }}%; background: {{ $color }}"></div></div>
                @endforeach
                @if(empty($pieTopReceivers['items'])) <div class="text-muted small text-center py-2">No data</div> @endif
              </div>
            @endif
          </div>
        </div>

        
        <!-- Top Countries -->
        <div class="chart-container">
          <div class="chart-header d-flex align-items-center gap-3">
            <div class="kpi-icon warning" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-globe"></i></div>
            <div>
              <h6 class="chart-title">{{ __('Top 5 Countries') }}</h6>
              <p class="chart-subtitle">{{ __('By total sent amount') }}</p>
            </div>
          </div>
          <div class="p-3 position-relative">
            @if($loadingStates['pies'])
              <div class="loading-skeleton" style="height: 220px; border-radius: 8px;"></div>
            @else
              <div id="pieTopCountries" class="ct-chart chart-animate" style="height: 220px;" wire:ignore></div>
              <div class="pie-center">
                <p class="pie-center-title">{{ __('Total') }}</p>
                <p class="pie-center-value" id="pieTopCountriesTotal">{{ number_format($pieTopCountries['total'] ?? 0, 0) }}</p>
              </div>

              <div class="legend-list">
                @foreach(($pieTopCountries['items'] ?? []) as $i => $it)
                  @php $color = $pieTopCountries['colors'][$i] ?? '#64748b'; @endphp
                  <div class="legend-item">
                    <div class="legend-left">
                      <span class="legend-dot" style="background: {{ $color }}"></span>
                      <span class="legend-label">{{ $it['label'] }}</span>
                    </div>
                    <div class="legend-right">{{ number_format($it['amount'], 0) }} <small class="text-muted">{{ $it['pct'] }}%</small></div>
                  </div>
                  <div class="progress"><div class="progress-bar" style="width: {{ $it['pct'] }}%; background: {{ $color }}"></div></div>
                @endforeach
                @if(empty($pieTopCountries['items'])) <div class="text-muted small text-center py-2">No data</div> @endif
              </div>
            @endif
          </div>
        </div>
      </div>
    </div> <!-- row -->
  </div> <!-- container-fluid -->
  {{-- Chartist + FA --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.js"></script>

  @push('scripts')
  <script>
    // Toasts
    function showToast(type, message) {
      const container = document.getElementById('toastContainer');
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;
      const icon = {success:'fas fa-check-circle', error:'fas fa-exclamation-circle', warning:'fas fa-exclamation-triangle', info:'fas fa-info-circle'};
      toast.innerHTML = `
        <i class="${icon[type]||icon.info}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;font-size:1.2rem;color:#6b7280;margin-left:auto;cursor:pointer">×</button>`;
      container.appendChild(toast);
      setTimeout(()=>toast.classList.add('show'),10);
      setTimeout(()=>{toast.classList.remove('show'); setTimeout(()=>toast.remove(),250)},5000);
    }

    // Line Chart (modern gradient, smooth)
    function drawLineChart(elementId, data) {
      const el = document.getElementById(elementId);
      if (!el || !data.labels || !data.series) return;

      el.innerHTML = '';
      if (el.__chart) { el.__chart.detach(); el.__chart = null; }

      const gradient = (data.gradient || {from:'#6366f1', to:'#8b5cf6'});
      const strokeColor = (data.colors && data.colors[0]) || gradient.from;

      const opts = {
        height: '280px',
        fullWidth: true,
        chartPadding: { top: 10, right: 28, bottom: 10, left: 10 },
        axisX: { showGrid: false },
        axisY: {
          low: 0,
          labelInterpolationFnc: v => new Intl.NumberFormat('en-US', { notation: 'compact', maximumFractionDigits: 1 }).format(v)
        },
        showPoint: true,
        showLine: true,
        showArea: true,
        lineSmooth: Chartist.Interpolation.cardinal({ tension: 0.4 })
      };

      el.__chart = new Chartist.Line('#' + elementId, {
        labels: data.labels || [],
        series: data.series || [[]]
      }, opts);

      el.__chart.on('created', ctx => {
        const defs = ctx.svg.elem('defs');
        const areaGradId = elementId + '-area-grad';
        const lineGradId = elementId + '-line-grad';

        const ag = defs.elem('linearGradient', { id: areaGradId, x1: 0, y1: 0, x2: 0, y2: 1 });
        ag.elem('stop', { offset: 0, 'stop-color': gradient.from, 'stop-opacity': 0.45 });
        ag.elem('stop', { offset: 1, 'stop-color': gradient.to,   'stop-opacity': 0.05 });

        const lg = defs.elem('linearGradient', { id: lineGradId, x1: 0, y1: 0, x2: 1, y2: 0 });
        lg.elem('stop', { offset: 0, 'stop-color': gradient.from });
        lg.elem('stop', { offset: 1, 'stop-color': gradient.to });

        el.__chart.on('draw', d => {
          if (d.type === 'line')  d.element.attr({ style: `stroke: url(#${lineGradId}); stroke-width:3px;` });
          if (d.type === 'area')  d.element.attr({ style: `fill: url(#${areaGradId});` });
          if (d.type === 'point') d.element.attr({ style: `stroke: ${strokeColor}; stroke-width:8px;` });
        });
      });
    }

    // Pie Chart (colors + center total + animation)
  function darken(hex, p = 0.12) {
    hex = hex.replace('#','');
    if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    const n = parseInt(hex,16);
    let r=(n>>16)&255, g=(n>>8)&255, b=n&255;
    r = Math.max(0, Math.round(r*(1-p)));
    g = Math.max(0, Math.round(g*(1-p)));
    b = Math.max(0, Math.round(b*(1-p)));
    return '#'+(1<<24 | (r<<16)|(g<<8)|b).toString(16).slice(1);
  }

  function drawPieChart(elementId, raw, totalElementId) {
    const el = document.getElementById(elementId);
    if (!el || !raw || !raw.labels || !raw.series) return;

    const data = {
      labels: raw.labels,
      series: (raw.series || []).map(v => +v || 0)
    };
    const colors = (raw.colors?.length ? raw.colors : ['#c7d2fe','#d8b4fe','#a5f3fc','#bbf7d0','#fde68a']);
    const donutW = 26;

    el.innerHTML = '';
    if (el.__chart) { el.__chart.detach(); el.__chart = null; }

    el.__chart = new Chartist.Pie('#'+elementId, data, {
      donut: true, donutWidth: donutW, showLabel: false,
      height: '240px', startAngle: 270, chartPadding: 12
    });

    // One tooltip for the page
    let tt = document.querySelector('.chartist-tooltip');
    if (!tt) { tt = document.createElement('div'); tt.className = 'chartist-tooltip'; document.body.appendChild(tt); }
    const showTT = (x,y,html)=>{ tt.innerHTML = html; tt.style.left=x+'px'; tt.style.top=y+'px'; tt.style.display='block'; };
    const hideTT = ()=>{ tt.style.display='none'; };

    // draw soft track BEHIND slices (and never block events)
    el.__chart.on('created', ctx => {
      const w = ctx.chartRect.width(), h = ctx.chartRect.height();
      const cx = ctx.chartRect.x1 + w/2, cy = ctx.chartRect.y1 + h/2;
      const r  = Math.min(w,h)/2;
      ctx.svg.elem('circle', {
        cx, cy, r: r - donutW/2,
        style: `fill:none; stroke:#eef2f7; stroke-width:${donutW}px; pointer-events:none;`
      });
    });

    // style slices + interactive tooltip
    const total = data.series.reduce((s,v)=>s+v,0);
    el.__chart.on('draw', d => {
      if (d.type !== 'slice') return;

      const color = colors[d.index] || '#cbd5e1';
      d.element.attr({
        style: `fill:none; stroke:${color}; stroke-width:${donutW}px; stroke-linecap:butt; pointer-events:stroke;`
      });

      // reveal animation
      const len = d.element._node.getTotalLength();
      d.element.attr({'stroke-dasharray': `${len}px ${len}px`, 'stroke-dashoffset': -len+'px'});
      d.element.animate({'stroke-dashoffset': {dur: 700, from: -len+'px', to:'0px', easing: Chartist.Svg.Easing.easeOutCubic, fill:'freeze'}}, false);

      // tooltip handlers
      const node  = d.element._node;
      const label = data.labels?.[d.index] ?? `#${d.index+1}`;
      const value = +data.series[d.index] || 0;
      const pct   = total>0 ? Math.round((value/total)*100) : 0;
      const html  = `
        <div style="display:flex;align-items:center;gap:8px;">
          <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${color}"></span>
          <div><div style="font-weight:600;">${label}</div>
          <div style="opacity:.9">${new Intl.NumberFormat('en-US').format(value)} <span style="opacity:.7">(${pct}%)</span></div></div>
        </div>`;

      const move = e => showTT(e.clientX, e.clientY, html);
      node.style.cursor = 'pointer';
      node.addEventListener('mouseenter', move);
      node.addEventListener('mousemove',  move);
      node.addEventListener('mouseleave', hideTT);
      node.addEventListener('touchstart', e=>{const t=e.touches[0]; showTT(t.clientX,t.clientY,html); },{passive:true});
      node.addEventListener('touchmove',  e=>{const t=e.touches[0]; showTT(t.clientX,t.clientY,html); },{passive:true});
      node.addEventListener('touchend', hideTT);
    });

    // center total text
    if (totalElementId) {
      const tEl = document.getElementById(totalElementId);
      if (tEl) tEl.textContent = new Intl.NumberFormat('en-US', {notation:'compact', maximumFractionDigits:1}).format(total);
    }
  }
    // Initial draw
    document.addEventListener('livewire:load', function () {
      const initial = {
        sendersAmount:   @json($sendersAmountChart),
        receiversAmount: @json($receiversAmountChart),
        pieTopSenders:   @json($pieTopSenders),
        pieTopReceivers: @json($pieTopReceivers),
        pieTopCountries: @json($pieTopCountries),
      };

      drawLineChart('sendersAmountChart',   initial.sendersAmount);
      drawLineChart('receiversAmountChart', initial.receiversAmount);
      drawPieChart('pieTopSenders',   initial.pieTopSenders,   'pieTopSendersTotal');
      drawPieChart('pieTopReceivers', initial.pieTopReceivers, 'pieTopReceiversTotal');
      drawPieChart('pieTopCountries', initial.pieTopCountries, 'pieTopCountriesTotal');
    });

    // Live updates from PHP
    window.addEventListener('charts:update', function (e) {
      const d = e.detail || {};
      if (d.sendersAmount)   drawLineChart('sendersAmountChart',   d.sendersAmount);
      if (d.receiversAmount) drawLineChart('receiversAmountChart', d.receiversAmount);
      if (d.pieTopSenders)   drawPieChart('pieTopSenders',   d.pieTopSenders,   'pieTopSendersTotal');
      if (d.pieTopReceivers) drawPieChart('pieTopReceivers', d.pieTopReceivers, 'pieTopReceiversTotal');
      if (d.pieTopCountries) drawPieChart('pieTopCountries', d.pieTopCountries, 'pieTopCountriesTotal');
    });

    // Auto refresh
    let autoRefreshInterval = null;
    window.addEventListener('start-auto-refresh', function () {
      if (autoRefreshInterval) clearInterval(autoRefreshInterval);
      autoRefreshInterval = setInterval(() => Livewire.emit('refreshDashboard'), 30000);
      showToast('info', 'Auto-refresh enabled (30s).');
    });
    window.addEventListener('stop-auto-refresh', function () {
      if (autoRefreshInterval) { clearInterval(autoRefreshInterval); autoRefreshInterval = null; }
      showToast('info', 'Auto-refresh disabled.');
    });

    // Toasts from server
    window.addEventListener('show-toast', e => showToast(e.detail.type, e.detail.message));

    // Keyboard shortcuts
    document.addEventListener('keydown', function (ev) {
      if ((ev.ctrlKey || ev.metaKey) && ev.key.toLowerCase() === 'r') { ev.preventDefault(); Livewire.emit('refreshDashboard'); showToast('info','Dashboard refreshed.'); }
      if ((ev.ctrlKey || ev.metaKey) && ev.key.toLowerCase() === 'a') { ev.preventDefault(); Livewire.emit('toggleAutoRefresh'); }
    });
  </script>
  @endpush
</div>
