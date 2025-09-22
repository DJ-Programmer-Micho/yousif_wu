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
  // diacritics-insensitive for latin; keeps Arabic/Kurdish intact
  const strip = (s) => (s || '').toString().toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '');

  function countryMatcher(params, data) {
    const termRaw = (params.term || '').trim();
    if (termRaw === '') return data;
    const term = strip(termRaw);

    const text = strip(data.text || '');
    const $el  = data.element ? $(data.element) : null;

    const iso = strip($el?.data('iso'));
    const ar  = strip($el?.data('ar')); // Arabic kept as-is for direct substring
    const ku  = strip($el?.data('ku'));

    if (text.includes(term) || iso.includes(term) || ar.includes(term) || ku.includes(term)) {
      return data;
    }
    return null;
  }

  function formatCountry(state) {
    if (!state.id) return state.text;
    const flag = $(state.element).data('flag');
    return flag ? $(`<span><img src="${flag}"> ${state.text}</span>`) : state.text;
  }

  function ensureDestroyed($el) {
    if ($el.data('select2')) {
      $el.off('.assign');         // remove namespaced events
      $el.select2('destroy');     // destroy instance
    }
  }

  function initAssignCreate(componentId) {
    const $country = $('#ctAssignCountry');
    const $set     = $('#ctAssignSet');

    if (!$country.length || !$set.length) return;

    ensureDestroyed($country);
    ensureDestroyed($set);

    $country.select2({
      theme: 'bootstrap4',
      width: '100%',
      placeholder: $country.data('placeholder') || 'Choose a country...',
      dropdownParent: $('#assignTaxModal'),
      templateResult: formatCountry,
      templateSelection: formatCountry,
      matcher: countryMatcher,
    });

    $set.select2({
      theme: 'bootstrap4',
      width: '100%',
      placeholder: $set.data('placeholder') || 'Choose a set...',
      dropdownParent: $('#assignTaxModal'),
    });

    $country.on('change.assign', function () {
      window.Livewire.find(componentId).set('country_id', $(this).val() || null);
    });
    $set.on('change.assign', function () {
      window.Livewire.find(componentId).set('tax_bracket_set_id', $(this).val() || null);
    });

    // reflect values from hidden inputs
    $country.val($('#ct_country_id_wire').val() || '').trigger('change.select2');
    $set.val($('#ct_tax_set_id_wire').val() || '').trigger('change.select2');
  }

  function initAssignEdit(componentId) {
    const $set = $('#ctEditSet');
    if (!$set.length) return;

    ensureDestroyed($set);

    $set.select2({
      theme: 'bootstrap4',
      width: '100%',
      placeholder: $set.data('placeholder') || 'Choose a set...',
      dropdownParent: $('#assignTaxEditModal'),
    });

    $set.on('change.assign', function () {
      window.Livewire.find(componentId).set('tax_bracket_set_id', $(this).val() || null);
    });

    $set.val($('#ct_tax_set_id_wire_edit').val() || '').trigger('change.select2');
  }

  // Init on Livewire events
  window.addEventListener('country-tax-assign-opened',      (e) => initAssignCreate(e.detail.componentId));
  window.addEventListener('country-tax-assign-edit-opened', (e) => initAssignEdit(e.detail.componentId));

  // Re-init after Livewire DOM diffs, only if modal is visible
  document.addEventListener('livewire:load', function () {
    Livewire.hook('message.processed', (message, component) => {
      if ($('#assignTaxModal').hasClass('show'))     initAssignCreate(component.id);
      if ($('#assignTaxEditModal').hasClass('show')) initAssignEdit(component.id);
    });
  });
})();
</script>
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Country Tax Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Utilities') }}</li>
                            <li class="breadcrumb-item text-muted active">{{ __('Countries') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('Country Tax') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('country.country-tax-livewire')
    </div>
@endsection
