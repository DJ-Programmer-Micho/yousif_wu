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

    <!-- Charts & Sidebar -->
    <div class="row dashboard-shell">
      <div class="col-xl-8 order-2 order-xl-1 dashboard-main">
        <div class="chart-container trend-hero-card mb-4">
          <div class="chart-header d-flex align-items-start justify-content-between flex-wrap">
            <div class="d-flex align-items-center gap-3 mb-3 mb-lg-0">
              <div class="kpi-icon primary" style="width: 44px; height: 44px; font-size: 18px;"><i class="fas fa-chart-line"></i></div>
              <div>
                <h6 class="chart-title">{{ __('Transactions Trend') }}</h6>
                <p class="chart-subtitle">
                  @if($trendGrouping === 'monthly')
                    {{ __('Curved monthly activity for the last') }} {{ $monthsBack }} {{ __('months') }}
                  @else
                    {{ __('Curved yearly activity grouped by calendar year') }}
                  @endif
                </p>
              </div>
            </div>

            <div class="trend-toolbar">
              <span class="badge badge-soft-primary"><i class="fas fa-wave-square mr-1"></i>{{ __('Curved Area') }}</span>
              <div class="trend-switch">
                <button type="button" class="trend-switch-btn {{ $trendGrouping === 'monthly' ? 'active' : '' }}" wire:click="$set('trendGrouping', 'monthly')">
                  {{ __('Monthly') }}
                </button>
                <button type="button" class="trend-switch-btn {{ $trendGrouping === 'yearly' ? 'active' : '' }}" wire:click="$set('trendGrouping', 'yearly')">
                  {{ __('Yearly') }}
                </button>
              </div>
            </div>
          </div>

          <div class="p-3">
            @if($loadingStates['charts'])
              <div class="loading-skeleton" style="height: 320px; border-radius: 18px;"></div>
            @else
              <div id="sendersAmountChart" class="ct-chart chart-animate trend-chart" style="height: 320px;" wire:ignore></div>
            @endif
          </div>

          <div class="trend-insight-bar px-3 pb-3">
            <div class="trend-insight-pill">
              <span>{{ __('Transactions This Period') }}</span>
              <strong>{{ number_format($kpis['senders_total']['current']) }}</strong>
            </div>
            <div class="trend-insight-pill">
              <span>{{ __('Send Volume') }}</span>
              <strong>${{ number_format($kpis['amount_senders']['current'], 2) }}</strong>
            </div>
            <div class="trend-insight-pill">
              <span>{{ __('Active Receivers') }}</span>
              <strong>{{ number_format($kpis['receivers_total']['current']) }}</strong>
            </div>
          </div>
        </div>

        <div class="chart-container analytics-card analytics-card-receiver mb-4">
          <div class="chart-header d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center gap-3 mb-3 mb-lg-0">
              <div class="kpi-icon success" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-chart-area"></i></div>
              <div>
                <h6 class="chart-title">{{ __('Receivers Amount Trend') }}</h6>
                <p class="chart-subtitle">
                  @if($receiversTrendGrouping === 'monthly')
                    {{ __('Last') }} {{ $monthsBack }} {{ __('months in IQD') }}
                  @else
                    {{ __('Grouped by calendar year in IQD') }}
                  @endif
                </p>
              </div>
            </div>
            <div class="analytics-chip-group">
              <span class="badge badge-soft-success"><i class="fas fa-circle mr-1"></i> {{ __('Amount (IQD)') }}</span>
              <span class="analytics-value-pill">{{ number_format($kpis['amount_receivers']['current'], 0) }}</span>
              <div class="trend-switch">
                <button type="button" class="trend-switch-btn {{ $receiversTrendGrouping === 'monthly' ? 'active' : '' }}" wire:click="$set('receiversTrendGrouping', 'monthly')">
                  {{ __('Monthly') }}
                </button>
                <button type="button" class="trend-switch-btn {{ $receiversTrendGrouping === 'yearly' ? 'active' : '' }}" wire:click="$set('receiversTrendGrouping', 'yearly')">
                  {{ __('Yearly') }}
                </button>
              </div>
            </div>
          </div>
          <div class="p-3">
            @if($loadingStates['charts'])
              <div class="loading-skeleton" style="height: 280px; border-radius: 8px;"></div>
            @else
              <div id="receiversAmountChart" class="ct-chart chart-animate" style="height: 280px;" wire:ignore></div>
            @endif
          </div>
          <div class="analytics-footer px-3 pb-3">
            <div class="analytics-footer-pill">
              <span>{{ __('Current') }}</span>
              <strong>{{ number_format($kpis['amount_receivers']['current'], 0) }} IQD</strong>
            </div>
            <div class="analytics-footer-pill">
              <span>{{ __('Previous') }}</span>
              <strong>{{ number_format($kpis['amount_receivers']['previous'], 0) }} IQD</strong>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 col-xl-4 mb-4">
            <div class="chart-container analytics-pie-card analytics-pie-card-primary h-100">
              <div class="chart-header d-flex align-items-center gap-3">
                <div class="kpi-icon primary" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-user-tie"></i></div>
                <div>
                  <h6 class="chart-title">{{ __('Top 5 Senders') }}</h6>
                  <p class="chart-subtitle">{{ __('By amount sent') }}</p>
                </div>
                <span class="analytics-value-pill ml-auto">{{ number_format($pieTopSenders['total'] ?? 0, 0) }}</span>
              </div>
              <div class="p-3 position-relative">
                @if($loadingStates['pies'])
                  <div class="loading-skeleton" style="height: 220px; border-radius: 8px;"></div>
                @else
                  <div class="analytics-pie-stage analytics-pie-stage-primary">
                    <div class="analytics-pie-stage-glow"></div>
                    <div id="pieTopSenders" class="ct-chart chart-animate analytics-pie-chart w-100 h-100" wire:ignore></div>
                    <div class="pie-center">
                      <p class="pie-center-title">{{ __('Total Sent') }}</p>
                      <p class="pie-center-value" id="pieTopSendersTotal">{{ number_format($pieTopSenders['total'] ?? 0, 0) }}</p>
                    </div>
                  </div>

                  <div class="legend-list">
                    @foreach(($pieTopSenders['items'] ?? []) as $i => $it)
                      @php $color = $pieTopSenders['colors'][$i] ?? '#64748b'; @endphp
                      <div class="legend-item legend-item-card">
                        <div class="legend-left">
                          <span class="legend-rank">{{ $loop->iteration }}</span>
                          <span class="legend-dot" style="background: {{ $color }}"></span>
                          <div class="legend-copy">
                            <span class="legend-label">{{ $it['label'] }}</span>
                            <small class="legend-meta">{{ $it['pct'] }}% {{ __('share') }}</small>
                          </div>
                        </div>
                        <div class="legend-right">{{ number_format($it['amount'], 0) }}</div>
                      </div>
                      <div class="progress progress-soft"><div class="progress-bar" style="width: {{ $it['pct'] }}%; background: {{ $color }}"></div></div>
                    @endforeach
                    @if(empty($pieTopSenders['items'])) <div class="text-muted small text-center py-2">{{ __('No data') }}</div> @endif
                  </div>
                @endif
              </div>
            </div>
          </div>

          <div class="col-md-6 col-xl-4 mb-4">
            <div class="chart-container analytics-pie-card analytics-pie-card-success h-100">
              <div class="chart-header d-flex align-items-center gap-3">
                <div class="kpi-icon success" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-user-shield"></i></div>
                <div>
                  <h6 class="chart-title">{{ __('Top 5 Receivers') }}</h6>
                  <p class="chart-subtitle">{{ __('By amount received') }}</p>
                </div>
                <span class="analytics-value-pill ml-auto">{{ number_format($pieTopReceivers['total'] ?? 0, 0) }}</span>
              </div>
              <div class="p-3 position-relative">
                @if($loadingStates['pies'])
                  <div class="loading-skeleton" style="height: 220px; border-radius: 8px;"></div>
                @else
                  <div class="analytics-pie-stage analytics-pie-stage-success">
                    <div class="analytics-pie-stage-glow"></div>
                    <div id="pieTopReceivers" class="ct-chart chart-animate analytics-pie-chart" style="height: 220px;" wire:ignore></div>
                    <div class="pie-center">
                      <p class="pie-center-title">{{ __('Total Received') }}</p>
                      <p class="pie-center-value" id="pieTopReceiversTotal">{{ number_format($pieTopReceivers['total'] ?? 0, 0) }}</p>
                    </div>
                  </div>

                  <div class="legend-list">
                    @foreach(($pieTopReceivers['items'] ?? []) as $i => $it)
                      @php $color = $pieTopReceivers['colors'][$i] ?? '#64748b'; @endphp
                      <div class="legend-item legend-item-card">
                        <div class="legend-left">
                          <span class="legend-rank">{{ $loop->iteration }}</span>
                          <span class="legend-dot" style="background: {{ $color }}"></span>
                          <div class="legend-copy">
                            <span class="legend-label">{{ $it['label'] }}</span>
                            <small class="legend-meta">{{ $it['pct'] }}% {{ __('share') }}</small>
                          </div>
                        </div>
                        <div class="legend-right">{{ number_format($it['amount'], 0) }}</div>
                      </div>
                      <div class="progress progress-soft"><div class="progress-bar" style="width: {{ $it['pct'] }}%; background: {{ $color }}"></div></div>
                    @endforeach
                    @if(empty($pieTopReceivers['items'])) <div class="text-muted small text-center py-2">{{ __('No data') }}</div> @endif
                  </div>
                @endif
              </div>
            </div>
          </div>

          <div class="col-md-6 col-xl-4 mb-4">
            <div class="chart-container analytics-pie-card analytics-pie-card-warning h-100">
              <div class="chart-header d-flex align-items-center gap-3">
                <div class="kpi-icon warning" style="width: 40px; height: 40px; font-size: 16px;"><i class="fas fa-globe"></i></div>
                <div>
                  <h6 class="chart-title">{{ __('Top 5 Countries') }}</h6>
                  <p class="chart-subtitle">{{ __('By total sent amount') }}</p>
                </div>
                <span class="analytics-value-pill ml-auto">{{ number_format($pieTopCountries['total'] ?? 0, 0) }}</span>
              </div>
              <div class="p-3 position-relative">
                @if($loadingStates['pies'])
                  <div class="loading-skeleton" style="height: 220px; border-radius: 8px;"></div>
                @else
                  <div class="analytics-pie-stage analytics-pie-stage-warning">
                    <div class="analytics-pie-stage-glow"></div>
                    <div id="pieTopCountries" class="ct-chart chart-animate analytics-pie-chart" style="height: 220px;" wire:ignore></div>
                    <div class="pie-center">
                      <p class="pie-center-title">{{ __('Country Mix') }}</p>
                      <p class="pie-center-value" id="pieTopCountriesTotal">{{ number_format($pieTopCountries['total'] ?? 0, 0) }}</p>
                    </div>
                  </div>

                  <div class="legend-list">
                    @foreach(($pieTopCountries['items'] ?? []) as $i => $it)
                      @php $color = $pieTopCountries['colors'][$i] ?? '#64748b'; @endphp
                      <div class="legend-item legend-item-card">
                        <div class="legend-left">
                          <span class="legend-rank">{{ $loop->iteration }}</span>
                          <span class="legend-dot" style="background: {{ $color }}"></span>
                          <div class="legend-copy">
                            <span class="legend-label">{{ $it['label'] }}</span>
                            <small class="legend-meta">{{ $it['pct'] }}% {{ __('share') }}</small>
                          </div>
                        </div>
                        <div class="legend-right">{{ number_format($it['amount'], 0) }}</div>
                      </div>
                      <div class="progress progress-soft"><div class="progress-bar" style="width: {{ $it['pct'] }}%; background: {{ $color }}"></div></div>
                    @endforeach
                    @if(empty($pieTopCountries['items'])) <div class="text-muted small text-center py-2">{{ __('No data') }}</div> @endif
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 order-1 order-xl-2 dashboard-aside mb-4 mb-xl-0">
        @livewire('general.quick-send-money-sidebar-livewire')
      </div>
    </div>
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

    function getChartTooltip() {
      let tt = document.querySelector('.chartist-tooltip');
      if (!tt) {
        tt = document.createElement('div');
        tt.className = 'chartist-tooltip';
        document.body.appendChild(tt);
      }
      return tt;
    }

    function showChartTooltip(x, y, html) {
      const tt = getChartTooltip();
      tt.innerHTML = html;
      tt.style.left = x + 'px';
      tt.style.top = y + 'px';
      tt.style.display = 'block';
    }

    function hideChartTooltip() {
      const tt = document.querySelector('.chartist-tooltip');
      if (tt) tt.style.display = 'none';
    }

    function formatChartValue(value, decimals = 0) {
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: decimals
      }).format(value);
    }

    // Line Chart (modern gradient, smooth)
    function drawLineChart(elementId, data) {
      const el = document.getElementById(elementId);
      if (!el || !data.labels || !data.series) return;

      el.innerHTML = '';
      if (el.__chart) { el.__chart.detach(); el.__chart = null; }

      const gradient = (data.gradient || {from:'#6366f1', to:'#8b5cf6'});
      const strokeColor = (data.colors && data.colors[0]) || gradient.from;
      const tooltipLabel = data.tooltipLabel || 'Value';
      const valuePrefix = data.valuePrefix || '';
      const valueSuffix = data.valueSuffix || '';
      const valueDecimals = Number.isFinite(data.valueDecimals) ? data.valueDecimals : 0;
      const pointSeries = Array.isArray(data.series?.[0]) ? data.series[0] : [];

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

      const areaGradId = elementId + '-area-grad';
      const lineGradId = elementId + '-line-grad';

      el.__chart.on('created', ctx => {
        const defs = ctx.svg.elem('defs');

        const ag = defs.elem('linearGradient', { id: areaGradId, x1: 0, y1: 0, x2: 0, y2: 1 });
        ag.elem('stop', { offset: 0, 'stop-color': gradient.from, 'stop-opacity': 0.45 });
        ag.elem('stop', { offset: 1, 'stop-color': gradient.to,   'stop-opacity': 0.05 });

        const lg = defs.elem('linearGradient', { id: lineGradId, x1: 0, y1: 0, x2: 1, y2: 0 });
        lg.elem('stop', { offset: 0, 'stop-color': gradient.from });
        lg.elem('stop', { offset: 1, 'stop-color': gradient.to });
      });

      el.__chart.on('draw', d => {
        if (d.type === 'line') {
          d.element.attr({ style: `stroke: url(#${lineGradId}); stroke-width:3px;` });
          return;
        }

        if (d.type === 'area') {
          d.element.attr({ style: `fill: url(#${areaGradId});` });
          return;
        }

        if (d.type !== 'point') return;

        d.element.attr({
          style: `stroke: ${strokeColor}; stroke-width:10px; stroke-linecap:round; pointer-events:all; cursor:pointer;`
        });

        const node = d.element._node;
        const label = data.labels?.[d.index] ?? '';
        const value = Number(pointSeries[d.index] ?? d.value?.y ?? 0);
        const valueText = `${valuePrefix}${formatChartValue(value, valueDecimals)}${valueSuffix}`;
        const html = `
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${strokeColor}"></span>
            <strong style="font-size:12px;">${tooltipLabel}</strong>
          </div>
          <div style="opacity:.72;font-size:11px;margin-bottom:3px;">${label}</div>
          <div style="font-weight:800;font-size:13px;">${valueText}</div>
        `;

        const move = e => showChartTooltip(e.clientX, e.clientY, html);
        node.addEventListener('mouseenter', move);
        node.addEventListener('mousemove', move);
        node.addEventListener('mouseleave', hideChartTooltip);
        node.addEventListener('touchstart', e => {
          const t = e.touches[0];
          if (t) showChartTooltip(t.clientX, t.clientY, html);
        }, { passive: true });
        node.addEventListener('touchmove', e => {
          const t = e.touches[0];
          if (t) showChartTooltip(t.clientX, t.clientY, html);
        }, { passive: true });
        node.addEventListener('touchend', hideChartTooltip);
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
    const donutW = 24;

    el.innerHTML = '';
    if (el.__chart) { el.__chart.detach(); el.__chart = null; }

    el.__chart = new Chartist.Pie('#'+elementId, data, {
      donut: true, donutWidth: donutW, showLabel: false,
      height: '248px', startAngle: 270, chartPadding: 14
    });

    const showTT = (x,y,html)=> showChartTooltip(x, y, html);
    const hideTT = ()=> hideChartTooltip();

    // style slices + interactive tooltip
    const total = data.series.reduce((s,v)=>s+v,0);
    el.__chart.on('draw', d => {
      if (d.type !== 'slice') return;

      const color = colors[d.index] || '#cbd5e1';
      d.element.attr({
        style: `fill:none; stroke:${color}; stroke-width:${donutW}px; stroke-linecap:round; pointer-events:stroke;`
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
