@extends('layouts.app')

@push('css')
  {{-- existing --}}
  <link rel="stylesheet" href="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
  <link rel="stylesheet" href="{{ asset('assets/js/utils/country_select/country_select.css') }}">
  {{-- Select2 (CSS + Bootstrap 4 theme) --}}
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
  <style>
    /* tiny tweak so flag aligns nicely in options */
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
  {{-- existing --}}
  <script src="{{ asset('assets/js/utils/country_select/countrySelect.min.js') }}"></script>
  <script src="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>

  {{-- Select2 --}}
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
  <script>
(function () {
  function formatOption(state) {
    if (!state.id) return state.text;
    const flag = $(state.element).data('flag');
    return flag ? $(`<span><img src="${flag}" style="height:12px;vertical-align:-2px;margin-right:6px">${state.text}</span>`) : state.text;
  }
  function initCountrySelect(componentId) {
    const $el = $('#countrySelect');
    if (!$el.length) return;

    if ($el.data('select2')) {
      $el.off('change.country').select2('destroy');
    }

    $el.select2({
      theme: 'bootstrap4',
      width: '100%',
      placeholder: $el.data('placeholder') || 'Choose a country...',
      dropdownParent: $('#createLimitModal'),
      templateResult: formatOption,
      templateSelection: formatOption,
    });

    // sync to Livewire via componentId
    $el.on('change.country', function () {
      const val = $(this).val() || null;
      window.Livewire.find(componentId).set('country_id', val);
    });

    // reflect current value from hidden input bound to wire:model
    const current = document.getElementById('country_id_wire')?.value || '';
    $el.val(current).trigger('change.select2');
  }

  // init when modal opens (Livewire dispatches this with componentId)
  window.addEventListener('country-limit-create-opened', (e) => {
    initCountrySelect(e.detail.componentId);
  });

  // re-init after DOM updates while modal is open
  document.addEventListener('livewire:load', function () {
    Livewire.hook('message.processed', (message, component) => {
      if ($('#createLimitModal').hasClass('show')) {
        initCountrySelect(component.id);
      }
    });
  });
})();
</script>
@endpush

@section('app')
  <div class="page-breadcrumb">
    <div class="row">
      <div class="col-12 align-self-center">
        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Country Limit Section') }}</h4>
        <div class="d-flex align-items-center">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0 p-0">
              <li class="breadcrumb-item text-muted active">{{ __('Utilities') }}</li>
              <li class="breadcrumb-item text-muted active">{{ __('Countries') }}</li>
              <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('Country Limit') }}</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid mt-3">
    @livewire('country.country-limit-livewire')
  </div>
@endsection
