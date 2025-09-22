@extends('layouts.app')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
<style>
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
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<script>
(function () {
  // safe lowercasing + diacritics strip for latin; keeps Arabic/Kurdish intact
  function strip(s) {
    if (s == null) return '';
    const str = String(s).toLowerCase();
    try { return str.normalize('NFD').replace(/[\u0300-\u036f]/g, ''); }
    catch (_) { return str; }
  }

  // SELECT2 MATCHER — uses jQuery .data() instead of dataset
  function countryMatcher(params, data) {
    const qRaw = (params.term || '').trim();
    if (qRaw === '') return data;

    const q    = strip(qRaw);
    const text = strip(data.text || '');

    const $el  = data.element ? $(data.element) : null;
    const iso  = strip($el && $el.length ? $el.data('iso') : '');
    const ar   = strip($el && $el.length ? $el.data('ar')  : '');
    const ku   = strip($el && $el.length ? $el.data('ku')  : '');

    return (text.includes(q) || iso.includes(q) || ar.includes(q) || ku.includes(q)) ? data : null;
  }

  function formatCountry(state) {
    if (!state.id) return state.text;
    const $el  = state.element ? $(state.element) : null;
    const flag = $el && $el.length ? $el.data('flag') : '';
    return flag ? $(`<span><img src="${flag}" style="height:12px;vertical-align:-2px;margin-right:6px;"> ${state.text}</span>`) : state.text;
  }

  function ensureDestroyed($el) {
    if ($el && $el.data('select2')) { $el.off('.cr'); $el.select2('destroy'); }
  }

  function initCreate(componentId) {
    const $select = $('#crCountrySelect');
    if (!$select.length) return;

    ensureDestroyed($select);

    $select.select2({
      theme: 'bootstrap4',
      width: '100%',
      placeholder: $select.data('placeholder') || 'Choose a country...',
      dropdownParent: $('#countryRuleCreateModal'),
      templateResult: formatCountry,
      templateSelection: formatCountry,
      matcher: countryMatcher,
      language: { noResults: () => 'No matches' }
    });

    // Sync to Livewire
    $select.on('change.cr', function () {
      window.Livewire.find(componentId).set('country_id', $(this).val() || null);
    });

    // Reflect current value from the hidden input (if any)
    $select.val($('#cr_country_id_wire').val() || '').trigger('change.select2');
  }

  // Init when the modal opens (from Livewire)
  window.addEventListener('country-rule-create-opened', (e) => initCreate(e.detail.componentId));

  // Re-init after Livewire DOM updates while modal is open
  document.addEventListener('livewire:load', function () {
    Livewire.hook('message.processed', (message, component) => {
      if ($('#countryRuleCreateModal').hasClass('show')) initCreate(component.id);
    });
  });
})();
</script>
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Country Bans Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Utilities') }}</li>
                            <li class="breadcrumb-item text-muted active">{{ __('Countries') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('Country Bans') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('country.country-rule-livewire')
    </div>
@endsection
