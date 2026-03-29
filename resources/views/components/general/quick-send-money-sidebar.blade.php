<div class="quick-sidebar-stack">
  <div class="chart-container quick-send-card {{ $this->hasInsufficientBalance ? 'is-warning' : '' }}">
    <div class="chart-header quick-send-header">
      <div>
        <h6 class="chart-title mb-1">{{ __('Send Money') }}</h6>
        <p class="chart-subtitle mb-0">{{ __('Start the transfer here, then finish the full sender form.') }}</p>
      </div>
      <span class="badge badge-soft-primary">{{ __('Quick Form') }}</span>
    </div>

    <div class="p-3">
      <div class="quick-balance-card {{ $this->hasInsufficientBalance ? 'is-warning' : '' }}">
        <div>
          <div class="quick-balance-label">{{ $this->balanceTitle }}</div>
          <div class="quick-balance-value">{{ $this->balanceValue }}</div>
        </div>
        <div class="quick-balance-meta">
          @if($this->remainingAfterTransfer !== null && $total !== null)
            <span>{{ __('After transfer') }}</span>
            <strong>${{ number_format($this->remainingAfterTransfer, 2) }}</strong>
          @else
            <span>{{ __('Fees are added to the sender total.') }}</span>
          @endif
        </div>
      </div>

      {{-- <div class="quick-steps">
        <span class="quick-step is-active"><small>1</small> {{ __('Transfer details') }}</span>
        <span class="quick-step"><small>2</small> {{ __('Sender info') }}</span>
        <span class="quick-step"><small>3</small> {{ __('Recipient info') }}</span>
      </div> --}}

      <form wire:submit.prevent="submit">
        <div class="form-group">
          <label for="quick_send_amount" class="quick-label">{{ __('Amount (USD)') }}</label>
          <input
            id="quick_send_amount"
            type="number"
            step="0.01"
            min="{{ $minLimit ?? 0.01 }}"
            @if($maxLimit) max="{{ $maxLimit }}" @endif
            class="form-control quick-input @error('amount') is-invalid @enderror"
            wire:model.debounce.400ms="amount"
            placeholder="500.00"
          >
          @error('amount')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
          @if($minLimit || $maxLimit)
            <small class="text-muted d-block mt-2">
              @if($minLimit && $maxLimit)
                {{ __('Limit:') }} {{ number_format($minLimit, 2) }} - {{ number_format($maxLimit, 2) }} {{ __('USD') }}
              @elseif($minLimit)
                {{ __('Min:') }} {{ number_format($minLimit, 2) }} {{ __('USD') }}
              @elseif($maxLimit)
                {{ __('Max:') }} {{ number_format($maxLimit, 2) }} {{ __('USD') }}
              @endif
            </small>
          @endif
        </div>

        <div class="form-group">
          <label for="quickSendCountrySelect" class="quick-label">{{ __('Country') }}</label>
          <div class="quick-country-wrap @error('country_id') is-invalid @enderror" wire:ignore>
            <input type="hidden" id="quick_send_country_id_wire" wire:model="country_id">
            <select id="quickSendCountrySelect" class="form-control quick-input" data-placeholder="{{ __('Choose a country...') }}">
              <option value=""></option>
              @foreach($availableCountries as $country)
                <option
                  value="{{ $country['id'] }}"
                  data-flag="{{ app('cloudfrontflagsx2').'/'.$country['flag_path'] }}"
                  data-iso="{{ strtoupper($country['iso_code']) }}"
                  data-ar="{{ $country['ar_name'] }}"
                  data-ku="{{ $country['ku_name'] }}"
                >
                  {{ $country['en_name'] }}
                </option>
              @endforeach
            </select>
          </div>
          @error('country_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="quick-summary">
          <div class="quick-summary-row">
            <span>{{ __('Fee') }}</span>
            <strong>${{ number_format((float) $commission, 2) }}</strong>
          </div>
          <div class="quick-summary-row">
            <span>{{ __('Receiver Gets') }}</span>
            <strong>${{ number_format((float) ($receiverGets ?? 0), 2) }}</strong>
          </div>
          <div class="quick-summary-row is-emphasis">
            <span>{{ __('Total Payable') }}</span>
            <strong>${{ number_format((float) ($total ?? 0), 2) }}</strong>
          </div>
        </div>

        @if($this->hasInsufficientBalance)
          <div class="quick-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ __('This transfer is above the available register balance.') }}
          </div>
        @endif

        <button
          type="submit"
          class="btn btn-block quick-send-btn {{ $this->canSubmit ? '' : 'disabled' }}"
          wire:loading.attr="disabled"
          @if(!$this->canSubmit) disabled @endif
        >
          <span wire:loading.remove wire:target="submit">{{ __('Send Transfer') }}</span>
          <span wire:loading wire:target="submit">
            <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
            {{ __('Opening sender form...') }}
          </span>
        </button>

        <p class="quick-helper mb-0">{{ __('The dashboard preview uses the same fee rules as the sender form. Final customer details are completed on the next page.') }}</p>
      </form>
    </div>
  </div>

  <div class="chart-container exchange-card">
    <div class="chart-header d-flex align-items-center justify-content-between">
      <div>
        <h6 class="chart-title mb-1">{{ __('Exchange Rates $100') }}</h6>
        <p class="chart-subtitle mb-0">
          {{ $exchangeUpdatedAt ? __('Updated') . ': ' . $exchangeUpdatedAt : __('Live feed') }}
          @if($exchangeIsStale)
            <span class="ml-1 text-warning">{{ __('Cached') }}</span>
          @endif
        </p>
      </div>
      <div class="exchange-actions">
        <button
          type="button"
          class="badge badge-soft-primary exchange-action-btn border-0"
          wire:click="refreshExchangeRates"
          wire:loading.attr="disabled"
          wire:target="refreshExchangeRates"
        >
          <span wire:loading.remove wire:target="refreshExchangeRates">
            <i class="fas fa-sync-alt mr-1"></i>{{ __('Refresh') }}
          </span>
          <span wire:loading wire:target="refreshExchangeRates">
            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>{{ __('Refreshing') }}
          </span>
        </button>
        {{-- <a href="https://qamaralfajr.com/production/exchange_rates.php" target="_blank" rel="noopener noreferrer" class="badge badge-soft-success exchange-action-btn">
          {{ __('Source') }}
        </a> --}}
      </div>
    </div>

    <div class="p-3">
      @if($exchangeError)
        <div class="alert alert-warning py-2 px-3 small mb-3">{{ $exchangeError }}</div>
      @endif

      @if($exchangeTableHtml)
        <div class="exchange-table-shell">
          <div class="exchange-table-wrap">
            {!! $exchangeTableHtml !!}
          </div>
        </div>
      @elseif(!empty($exchangeRates))
        <div class="exchange-rate-list">
          <div class="exchange-rate-head">
            <span>{{ __('Currency') }}</span>
            <span>{{ __('Sell / Buy') }}</span>
          </div>

          @foreach($exchangeRates as $rate)
            <div class="exchange-rate-row">
              <div class="exchange-rate-main">
                <strong>{{ $rate['code'] }}</strong>
                <span>{{ $rate['name'] }}</span>
              </div>
              <div class="exchange-rate-values">
                <span>{{ rtrim(rtrim(number_format((float) $rate['sell'], 2, '.', ''), '0'), '.') }}</span>
                <small>/</small>
                <span>{{ rtrim(rtrim(number_format((float) $rate['buy'], 2, '.', ''), '0'), '.') }}</span>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div
          class="exchange-rate-fallback"
          data-exchange-fallback
          data-source-url="https://qamaralfajr.com/production/exchange_rates.php"
          data-trying-label="{{ __('Trying the live exchange feed...') }}"
          data-fallback-label="{{ __('Showing the live source inside the dashboard.') }}"
          data-table-label="{{ __('Showing the live table from the source.') }}"
          data-currency-label="{{ __('Currency') }}"
          data-values-label="{{ __('Sell / Buy') }}"
          data-empty-label="{{ __('Exchange rates are not available right now.') }}"
          wire:ignore
        >
          <div class="exchange-table-shell d-none" data-exchange-table-shell>
            <div class="exchange-rate-fallback-pill">
              <i class="fas fa-table mr-2"></i>
              <span>{{ __('Showing the live table from the source.') }}</span>
            </div>
            <div class="exchange-table-wrap" data-exchange-table-wrap></div>
          </div>

          <div class="exchange-rate-list is-loading" data-exchange-list>
            <div class="exchange-rate-head">
              <span>{{ __('Currency') }}</span>
              <span>{{ __('Sell / Buy') }}</span>
            </div>

            <div class="exchange-rate-fallback-state" data-exchange-status>
              <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
              <span>{{ __('Trying the live exchange feed...') }}</span>
            </div>
          </div>

          <div class="exchange-rate-iframe-shell d-none" data-exchange-iframe-shell>
            <div class="exchange-rate-fallback-pill">
              <i class="fas fa-satellite-dish mr-2"></i>
              <span>{{ __('Showing the live source inside the dashboard.') }}</span>
            </div>
            <iframe
              class="exchange-rate-iframe"
              title="{{ __('Exchange Rates Source') }}"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              data-exchange-iframe
            ></iframe>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css">
<style>
  .quick-country-wrap .select2-container{width:100% !important}
  .quick-country-wrap .select2-selection--single{
    min-height:52px !important;
    border-radius:16px !important;
    border:1px solid rgba(148,163,184,.22) !important;
    background:rgba(255,255,255,.88) !important;
    display:flex !important;
    align-items:center !important;
    padding:0 14px !important;
  }
  .quick-country-wrap .select2-selection__rendered{
    display:flex !important;
    align-items:center !important;
    line-height:1.2 !important;
    padding-left:0 !important;
    color:#111827 !important;
  }
  .quick-country-wrap .select2-selection__arrow{
    height:50px !important;
    right:10px !important;
  }
  .quick-country-wrap .select2-results__option img,
  .quick-country-wrap .select2-selection__rendered img{
    height:14px;
    width:auto;
    margin-right:8px;
    border-radius:2px;
  }
  .quick-country-wrap.is-invalid .select2-selection--single{
    border-color:#dc3545 !important;
    box-shadow:0 0 0 .2rem rgba(220,53,69,.1) !important;
  }
  .select2-container--bootstrap4 .select2-dropdown{
    border-radius:16px !important;
    border-color:rgba(148,163,184,.18) !important;
    overflow:hidden;
    box-shadow:0 18px 40px rgba(15,23,42,.14);
  }
  .select2-container--bootstrap4 .select2-results > .select2-results__options{
    max-height:280px !important;
    overflow-y:auto !important;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
  }
  .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected]{
    background:linear-gradient(135deg,#7c3aed,#ec4899) !important;
    color: #fff;
  }
  .exchange-rate-fallback-state{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    min-height:180px;
    padding:22px 18px;
    border-radius:18px;
    border:1px dashed rgba(124,58,237,.18);
    background:linear-gradient(180deg,rgba(248,250,252,.86),rgba(241,245,249,.78));
    color:#64748b;
    text-align:center;
    font-size:.9rem;
    line-height:1.5;
  }
  .exchange-rate-fallback-pill{
    display:flex;
    align-items:center;
    justify-content:center;
    padding:10px 14px;
    margin-bottom:12px;
    border-radius:14px;
    background:linear-gradient(135deg,rgba(124,58,237,.12),rgba(236,72,153,.12));
    color:#5b21b6;
    font-size:.84rem;
    font-weight:700;
    text-align:center;
  }
  .exchange-actions{
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
  }
  .exchange-action-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:34px;
    cursor:pointer;
    transition:all .2s ease;
  }
  .exchange-action-btn:disabled{
    opacity:.7;
    cursor:wait;
  }
  .exchange-table-shell{
    padding:2px;
    border-radius:22px;
    background:linear-gradient(135deg,rgba(124,58,237,.12),rgba(236,72,153,.1));
    box-shadow:0 16px 30px rgba(99,102,241,.12);
  }
  .exchange-table-wrap{
    overflow:auto;
    border-radius:20px;
    background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(248,250,252,.92));
    padding:12px;
    -webkit-overflow-scrolling:touch;
  }
  .exchange-table-wrap table{
    width:100% !important;
    min-width:100%;
    border-collapse:separate !important;
    border-spacing:0 10px !important;
  }
  .exchange-table-wrap th{
    padding:10px 8px !important;
    font-size:.82rem !important;
    line-height:1.4 !important;
    color:#475569;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.04em;
    white-space:nowrap;
  }
  .exchange-table-wrap td{
    padding:4px 8px !important;
    font-size:.98rem !important;
    font-weight:800 !important;
    color:#0f172a;
    vertical-align:middle !important;
    white-space:nowrap;
  }
  .exchange-table-wrap tbody tr{
    background:rgba(255,255,255,.84);
  }
  .exchange-table-wrap tbody td:first-child{
    border-top-left-radius:16px;
    border-bottom-left-radius:16px;
  }
  .exchange-table-wrap tbody td:last-child{
    border-top-right-radius:16px;
    border-bottom-right-radius:16px;
  }
  .exchange-table-wrap .btn,
  .exchange-table-wrap .btn-primary{
    width:100% !important;
    min-width:92px;
    border:none !important;
    border-radius:14px !important;
    padding:9px 10px !important;
    background:linear-gradient(135deg,#7c3aed,#ec4899) !important;
    color:#fff !important;
    font-size:1rem !important;
    font-weight:900 !important;
    box-shadow:0 12px 24px rgba(124,58,237,.2);
  }
  .exchange-table-wrap img{
    width:34px !important;
    max-width:34px !important;
    height:24px;
    object-fit:cover;
    border-radius:6px;
    box-shadow:0 6px 16px rgba(15,23,42,.12);
  }
  .exchange-rate-iframe-shell{
    padding:2px;
    border-radius:22px;
    background:linear-gradient(135deg,rgba(124,58,237,.12),rgba(236,72,153,.1));
    box-shadow:0 16px 30px rgba(99,102,241,.12);
  }
  .exchange-rate-iframe{
    display:block;
    width:100%;
    min-height:360px;
    border:0;
    border-radius:20px;
    background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(248,250,252,.92));
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<script>
(function () {
  const exchangeRateNames = {
    IQD: 'Iraqi Dinar',
    EUR: 'Euro',
    GBP: 'British Pound',
    TRY: 'Turkish Lira',
    IRR: 'Iranian Rial',
    NOK: 'Norwegian Krone',
    SEK: 'Swedish Krona',
    JOD: 'Jordanian Dinar',
    SAR: 'Saudi Riyal',
    AED: 'UAE Dirham',
    CAD: 'Canadian Dollar',
    AUD: 'Australian Dollar',
    CHF: 'Swiss Franc',
    DKK: 'Danish Krone',
    QAR: 'Qatari Riyal',
    KWD: 'Kuwaiti Dinar'
  };

  function strip(value) {
    if (value == null) return '';
    const text = String(value).toLowerCase();
    try {
      return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    } catch (_) {
      return text;
    }
  }

  function matcher(params, data) {
    const termRaw = (params.term || '').trim();
    if (termRaw === '') return data;

    const term = strip(termRaw);
    const text = strip(data.text || '');
    const element = data.element ? $(data.element) : null;
    const iso = strip(element?.data('iso'));
    const ar = strip(element?.data('ar'));
    const ku = strip(element?.data('ku'));

    return (text.includes(term) || iso.includes(term) || ar.includes(term) || ku.includes(term)) ? data : null;
  }

  function formatCountry(option) {
    if (!option.id) return option.text;
    const flag = $(option.element).data('flag');
    return flag ? $(`<span><img src="${flag}" alt=""> ${option.text}</span>`) : option.text;
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function decodeHtmlEntities(value) {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = value;
    return textarea.value;
  }

  function formatRateNumber(value) {
    const numeric = Number(value);
    if (!Number.isFinite(numeric)) return '';
    return numeric.toFixed(2).replace(/\.00$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
  }

  function parseExchangeRatesHtml(html) {
    const prepared = decodeHtmlEntities(
      String(html || '')
        .replace(/<img[^>]*>/giu, ' ')
        .replace(/<(br|\/p|\/div|\/li|\/tr|\/table|\/section|\/article|\/h[1-6])[^>]*>/giu, '\n')
        .replace(/<[^>]+>/g, ' ')
    ).replace(/\r\n?|\r/g, '\n');

    const lines = prepared
      .split('\n')
      .map((line) => line.replace(/\s+/g, ' ').trim())
      .filter(Boolean);

    const seen = {};

    return lines.reduce((rates, line) => {
      const match = line.match(/^(?<sell>\d+(?:\.\d+)?)\s+(?<buy>\d+(?:\.\d+)?)\s+(?<label>.+?)\s+(?<code>[A-Za-z]{3})$/u);
      if (!match || !match.groups) return rates;

      const code = String(match.groups.code || '').toUpperCase();
      if (!code || seen[code]) return rates;
      seen[code] = true;

      rates.push({
        code: code,
        label: String(match.groups.label || '').trim(),
        name: exchangeRateNames[code] || String(match.groups.label || '').trim() || code,
        sell: Number(match.groups.sell),
        buy: Number(match.groups.buy)
      });

      return rates;
    }, []);
  }

  function renderExchangeRates(listElement, rates, labels) {
    if (!listElement) return;

    const head = `
      <div class="exchange-rate-head">
        <span>${escapeHtml(labels.currency)}</span>
        <span>${escapeHtml(labels.values)}</span>
      </div>
    `;

    if (!Array.isArray(rates) || !rates.length) {
      listElement.innerHTML = `${head}<div class="exchange-rate-fallback-state"><span>${escapeHtml(labels.empty)}</span></div>`;
      return;
    }

    const rows = rates.map((rate) => `
      <div class="exchange-rate-row">
        <div class="exchange-rate-main">
          <strong>${escapeHtml(rate.code || '')}</strong>
          <span>${escapeHtml(rate.name || rate.label || rate.code || '')}</span>
        </div>
        <div class="exchange-rate-values">
          <span>${escapeHtml(formatRateNumber(rate.sell))}</span>
          <small>/</small>
          <span>${escapeHtml(formatRateNumber(rate.buy))}</span>
        </div>
      </div>
    `).join('');

    listElement.innerHTML = head + rows;
    listElement.classList.remove('is-loading');
  }

  function extractExchangeTableHtml(html, sourceUrl) {
    const parser = new DOMParser();
    const documentNode = parser.parseFromString(String(html || ''), 'text/html');
    const table = documentNode.querySelector('table');
    if (!table) return null;

    table.querySelectorAll('script, style').forEach((node) => node.remove());
    table.querySelectorAll('*').forEach((node) => {
      Array.from(node.attributes || []).forEach((attribute) => {
        if (/^on/i.test(attribute.name)) {
          node.removeAttribute(attribute.name);
        }
      });
    });
    table.querySelectorAll('img[src]').forEach((image) => {
      try {
        image.setAttribute('src', new URL(image.getAttribute('src'), sourceUrl).href);
      } catch (_) {}
    });

    return table.outerHTML || null;
  }

  function showExchangeTable(container, tableHtml) {
    if (!container || !tableHtml) return;

    const tableShell = container.querySelector('[data-exchange-table-shell]');
    const tableWrap = container.querySelector('[data-exchange-table-wrap]');
    const listElement = container.querySelector('[data-exchange-list]');
    const iframeShell = container.querySelector('[data-exchange-iframe-shell]');

    if (tableWrap) {
      tableWrap.innerHTML = tableHtml;
    }

    if (listElement) {
      listElement.classList.add('d-none');
    }

    if (iframeShell) {
      iframeShell.classList.add('d-none');
    }

    if (tableShell) {
      tableShell.classList.remove('d-none');
    }
  }

  function showExchangeIframe(container) {
    if (!container) return;

    const tableShell = container.querySelector('[data-exchange-table-shell]');
    const listElement = container.querySelector('[data-exchange-list]');
    const iframeShell = container.querySelector('[data-exchange-iframe-shell]');
    const iframe = container.querySelector('[data-exchange-iframe]');
    const sourceUrl = container.getAttribute('data-source-url') || '';

    if (tableShell) {
      tableShell.classList.add('d-none');
    }

    if (listElement) {
      listElement.classList.add('d-none');
    }

    if (iframe && iframe.getAttribute('src') !== sourceUrl) {
      iframe.setAttribute('src', sourceUrl);
    }

    if (iframeShell) {
      iframeShell.classList.remove('d-none');
    }
  }

  function initExchangeFallback() {
    const containers = document.querySelectorAll('[data-exchange-fallback]');
    if (!containers.length) return;

    const cache = window.__dashboardExchangeFallback = window.__dashboardExchangeFallback || {};

    containers.forEach((container) => {
      const listElement = container.querySelector('[data-exchange-list]');
      const labels = {
        currency: container.getAttribute('data-currency-label') || 'Currency',
        values: container.getAttribute('data-values-label') || 'Sell / Buy',
        empty: container.getAttribute('data-empty-label') || 'Exchange rates are not available right now.'
      };

      if (cache.mode === 'table' && cache.tableHtml) {
        showExchangeTable(container, cache.tableHtml);
        return;
      }

      if (cache.mode === 'rates' && Array.isArray(cache.rates) && cache.rates.length) {
        renderExchangeRates(listElement, cache.rates, labels);
        return;
      }

      if (cache.mode === 'iframe') {
        showExchangeIframe(container);
        return;
      }

      if (cache.promise) {
        cache.promise
          .then((result) => {
            if (result && result.mode === 'table') {
              showExchangeTable(container, result.tableHtml);
              return;
            }

            renderExchangeRates(listElement, result ? result.rates : [], labels);
          })
          .catch(() => showExchangeIframe(container));
        return;
      }

      const sourceUrl = container.getAttribute('data-source-url');

      cache.promise = fetch(sourceUrl, {
        method: 'GET',
        credentials: 'omit'
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error('Unable to fetch exchange rates.');
          }

          return response.text();
        })
        .then((html) => {
          const tableHtml = extractExchangeTableHtml(html, sourceUrl);
          if (tableHtml) {
            cache.mode = 'table';
            cache.tableHtml = tableHtml;
            return { mode: 'table', tableHtml: tableHtml };
          }

          const rates = parseExchangeRatesHtml(html);
          if (!rates.length) {
            throw new Error('No exchange rates found.');
          }

          cache.mode = 'rates';
          cache.rates = rates;
          return { mode: 'rates', rates: rates };
        })
        .catch((error) => {
          cache.mode = 'iframe';
          cache.error = error;
          throw error;
        })
        .finally(() => {
          delete cache.promise;
        });

      cache.promise
        .then((result) => {
          if (result && result.mode === 'table') {
            showExchangeTable(container, result.tableHtml);
            return;
          }

          renderExchangeRates(listElement, result ? result.rates : [], labels);
        })
        .catch(() => showExchangeIframe(container));
    });
  }

  function getComponentId() {
    const element = document.getElementById('quickSendCountrySelect');
    if (!element) return null;
    const root = element.closest('[wire\\:id]');
    return root ? root.getAttribute('wire:id') : null;
  }

  function initQuickCountry() {
    const $select = $('#quickSendCountrySelect');
    if (!$select.length) return;

    try {
      if ($select.data('select2')) {
        $select.select2('destroy');
      }
    } catch (e) {}

    $select.select2({
      theme: 'bootstrap4',
      width: '100%',
      placeholder: $select.data('placeholder') || 'Choose a country...',
      templateResult: formatCountry,
      templateSelection: formatCountry,
      matcher: matcher,
      allowClear: true,
      language: { noResults: () => 'No matches' }
    });

    $select.off('change.quick-send-country').on('change.quick-send-country', function () {
      const componentId = getComponentId();
      if (!componentId) return;
      window.Livewire.find(componentId).set('country_id', $(this).val() || null);
    });

    const componentId = getComponentId();
    let currentValue = $('#quick_send_country_id_wire').val() || '';
    if (componentId) {
      const component = window.Livewire.find(componentId);
      if (component && typeof component.get === 'function') {
        currentValue = component.get('country_id') || currentValue;
      }
    }
    $select.val(currentValue).trigger('change.select2');
  }

  document.addEventListener('livewire:load', function () {
    initQuickCountry();
    initExchangeFallback();
    window.addEventListener('exchange-rates-reload', function () {
      window.__dashboardExchangeFallback = {};
      initExchangeFallback();
    });
    Livewire.hook('message.processed', function () {
      if ($('#quickSendCountrySelect').length) {
        initQuickCountry();
      }
      initExchangeFallback();
    });
  });
})();
</script>
@endpush
