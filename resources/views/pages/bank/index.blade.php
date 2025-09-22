@extends('layouts.app')
@push('css')
<link rel="stylesheet" href="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --card-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    --hover-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --animation-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.modern-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    transition: var(--transition);
}

.modern-card:hover {
    box-shadow: var(--hover-shadow);
    transform: translateY(-2px);
}

.modern-header {
    background: var(--primary-gradient);
    position: relative;
    padding: 2rem 1.5rem;
    overflow: hidden;
}


/* Enhanced Input Styles */
.modern-input, .modern-select {
    border: 2px solid rgba(102, 126, 234, 0.1);
    background: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    padding: 12px 16px;
    transition: var(--transition);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.modern-input:focus, .modern-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
    background: #ffffff;
    outline: none;
    transform: translateY(-1px);
}

/* Enhanced Input Group Styles */
.input-group .input-group-text {
    border: 2px solid rgba(102, 126, 234, 0.1);
    border-right: none;
    background: rgba(102, 126, 234, 0.05);
}

.input-group .modern-input {
    border-left: none;
}

.input-group:focus-within .input-group-text {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}

/* Enhanced Badge Styles */
.badge-modern {
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.3px;
    border: none;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
}

.badge-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.badge-modern::before {
    content: '';
    position: absolute;
    inset: 0;
    background: inherit;
    filter: brightness(0.9);
    opacity: 0.1;
}

/* Enhanced Avatar Styles */
.avatar-modern {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    margin-right: 12px;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
}

.avatar-modern:hover {
    transform: scale(1.05);
}

/* Enhanced Stats Cards */
.stats-card-small {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 12px 8px;
    transition: var(--transition);
}

.stats-card-small:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

/* Enhanced Navigation Pills */
.nav-link {
    z-index: -9999;
}

.nav-pills .nav-link {
    border-radius: 12px;
    padding: 10px 20px;
    transition: var(--transition);
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-weight: 500;
}

.nav-pills .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-1px);
}

.nav-pills .nav-link.active {
    background: rgba(255, 255, 255, 0.9);
    color: #667eea;
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

.nav-pills .nav-link.active .badge {
    background: #667eea !important;
    color: white !important;
}

/* Enhanced Amount Display */
.amount-display-b {
    font-weight: 700;
    font-size: 1rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 4px rgba(102, 126, 234, 0.2);
}

.amount-display-r {
    font-weight: 700;
    font-size: 1rem;
    background: linear-gradient(135deg, #ea6666, #a24b4b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 4px rgba(234, 102, 102, 0.2);
}

.amount-display-g {
    font-weight: 700;
    font-size: 1rem;
    background: linear-gradient(135deg, #66ea7c, #4ba272);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 4px rgba(102, 234, 124, 0.2);
}

/* Enhanced Table Styles */
.table-row-hover {
    transition: var(--transition);
}

.table-row-hover:hover {
    background: rgba(102, 126, 234, 0.05);
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
}

.sortable {
    cursor: pointer;
    user-select: none;
    transition: var(--transition);
}

.sortable:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

/* Enhanced Advanced Filters */
.advanced-filters {
    background: rgba(102, 126, 234, 0.02);
    border-radius: 12px;
    padding: 1.5rem;
    animation: fadeInUp 0.3s var(--animation-bounce);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced Button Groups */
.btn-group .btn {
    border-radius: 8px;
    margin: 2px;
    transition: var(--transition);
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Enhanced Dropdown Styles */
.dropdown-menu {
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    padding: 8px;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.dropdown-item {
    border-radius: 8px;
    transition: var(--transition);
    padding: 8px 12px;
}

.dropdown-item:hover {
    background: var(--primary-gradient);
    color: white;
    transform: translateX(4px);
}

/* Enhanced Loading Animation */
.spinner-border {
    animation: spinner-border 0.75s linear infinite, pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* Enhanced Quick Date Filter Buttons */
.btn-group .btn-outline-secondary {
    border-radius: 20px;
    margin: 2px;
    transition: var(--transition);
    font-size: 0.875rem;
}

.btn-group .btn-outline-secondary:hover {
    background: var(--primary-gradient);
    border-color: transparent;
    color: white;
    transform: translateY(-2px) scale(1.05);
}

/* Enhanced Select2 Integration */
.select2-container--bootstrap4 .select2-selection {
    border: 2px solid rgba(102, 126, 234, 0.1);
    border-radius: 12px;
    transition: var(--transition);
}

.select2-container--bootstrap4.select2-container--focus .select2-selection {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}

  .select2-results__option img { vertical-align: -2px; height: 12px; margin-right: 6px; }
  .select2-selection__rendered img { vertical-align: -2px; height: 12px; margin-right: 6px; }

    /* make results scroll within modal */
  .select2-container--bootstrap4 .select2-results > .select2-results__options {
    max-height: 280px !important;
    overflow-y: auto !important;
  }
  /* keep dropdown above modal content/backdrop, just in case */
  .select2-container--bootstrap4.select2-container--open .select2-dropdown {
    z-index: 2000; /* higher than .modal (1050), lower than toasts if any */
  }
/* Enhanced Results Scrolling */
.select2-container--bootstrap4 .select2-results > .select2-results__options {
    max-height: 280px !important;
    overflow-y: auto !important;
    scrollbar-width: thin;
    scrollbar-color: #667eea transparent;
}

.select2-container--bootstrap4 .select2-results > .select2-results__options::-webkit-scrollbar {
    width: 6px;
}

.select2-container--bootstrap4 .select2-results > .select2-results__options::-webkit-scrollbar-track {
    background: transparent;
}

.select2-container--bootstrap4 .select2-results > .select2-results__options::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 3px;
}

/* Enhanced Dropdown Z-index */
.select2-container--bootstrap4.select2-container--open .select2-dropdown {
    z-index: 2000;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}
.select2-container .select2-selection--single {
    box-sizing: border-box;
    cursor: pointer;
    display: block;
    height:auto;
    user-select: none;
    -webkit-user-select: none
}
.form-control{
  height: auto;
}
</style>
@endpush
@push('scripts')
<script src="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
(function(){
  // --- utils ---
  function strip(s){ if(s==null) return ''; const t=String(s).toLowerCase(); try{return t.normalize('NFD').replace(/[\u0300-\u036f]/g,'');}catch(_){return t;} }
  function matcher(params, data){
    const qRaw=(params.term||'').trim(); if(qRaw==='') return data;
    const q=strip(qRaw), text=strip(data.text||'');
    const $el = data.element ? $(data.element) : null;
    const iso = strip($el?.data('iso'));
    const ar  = strip($el?.data('ar'));
    const ku  = strip($el?.data('ku'));
    return (text.includes(q)||iso.includes(q)||ar.includes(q)||ku.includes(q)) ? data : null;
  }
  function formatCountry(state){
    if(!state.id) return state.text;
    const $el = $(state.element);
    const flag = $el.data('flag');
    return flag ? $(`<span><img src="${flag}" style="height:12px;margin-right:6px;vertical-align:-2px;"> ${state.text}</span>`) : state.text;
  }

  // --- core ---
  let programmatic = false; // prevents feedback loop

  function initCountryOnce(){
    const $s = $('#bsCountrySelect');
    if(!$s.length) return;                 // receivers tab – nothing to init
    if($s.data('select2')) return;         // already initialized

    $s.select2({
      theme: 'bootstrap4',
      width: '100%',
      allowClear: true,
      minimumResultsForSearch: 0,
      placeholder: $s.data('placeholder') || '{{ __('All countries') }}',
      templateResult: formatCountry,
      templateSelection: formatCountry,
      matcher: matcher,
      language: { noResults: () => '{{ __('No matches') }}' }
    });

    // Sync to Livewire only when the user actually changes it
    $s.on('change.select2.sync', function(){
      if (programmatic) return; // ignore our own .val() updates

      const val     = $(this).val() || '';
      const current = $('#bs_country_wire').val() || '';

      // If Livewire already has this value, do nothing (prevents redundant network calls)
      if (val === current) return;

      const root = this.closest('[wire\\:id]');
      if (root && window.Livewire) {
        window.Livewire.find(root.getAttribute('wire:id')).set('country', val);
      }
    });

    // Set initial value visually, but don't push back into Livewire
    const current = $('#bs_country_wire').val() || '';
    programmatic = true;
    $s.val(current).trigger('change.select2'); // update Select2 UI
    programmatic = false;
  }

  document.addEventListener('livewire:load', function(){
    // First load
    initCountryOnce();

    // When your component clears filters, clear Select2 but don't echo back to Livewire
    window.addEventListener('filter-cleared', () => {
      const $s = $('#bsCountrySelect');
      if($s.length){
        programmatic = true;
        $s.val('').trigger('change.select2');
        programmatic = false;
      }
    });

    // Only (re)init when the tab switches to "senders"
    window.addEventListener('tab-changed', (e) => {
      if (e.detail && e.detail.tab === 'senders') {
        initCountryOnce();
      }
    });

    // ⚠️ Note: we intentionally DO NOT re-init on every Livewire message.processed
    // That was the source of the loop (init -> trigger change -> Livewire.set -> update -> init...).
  });
})();
</script>
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Transfers Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Utilities') }}</li>
                            <li class="breadcrumb-item text-muted active">{{ __('Transfers') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page"><b>{{ __('Pending') }}</b> {{ __('Transfers') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('sender.bank-statement-livewire')
    </div>
@endsection
