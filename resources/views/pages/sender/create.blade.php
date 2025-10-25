{{-- resources/views/pages/sender/create.blade.php --}}
@extends('layouts.app')
@push('css')
<link rel="stylesheet" href="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
<style>
  .select2-results__option img { vertical-align: -2px; height: 12px; margin-right: 6px; }
  .select2-selection__rendered img { vertical-align: -2px; height: 12px; margin-right: 6px; }
  .select2-container .select2-selection--single .select2-selection__rendered { display: contents;}

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
{{-- <script src="{{ asset('assets/js/utils/country_select/countrySelect.min.js') }}"></script> --}}
<script src="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
{{-- COUNTRY select: Select2 + Livewire binding via component id --}}
<script>
(function(){
  function strip(s){ if(s==null) return ''; const t=String(s).toLowerCase(); try{return t.normalize('NFD').replace(/[\u0300-\u036f]/g,'');}catch(_){return t;} }
  function matcher(params, data){
    const qRaw=(params.term||'').trim(); if(qRaw==='') return data;
    const q=strip(qRaw), text=strip(data.text||'');
    const $el=data.element?$(data.element):null;
    const iso=strip($el?.dataset?.iso), ar=strip($el?.dataset?.ar), ku=strip($el?.dataset?.ku);
    return (text.includes(q)||iso.includes(q)||ar.includes(q)||ku.includes(q)) ? data : null;
  }
  function formatCountry(opt){
    if(!opt.id) return opt.text;
    const flag=$(opt.element).data('flag');
    return flag ? $(`<span><img src="${flag}"> ${opt.text}</span>`) : opt.text;
  }
  function destroy($s){ try{ if($s.data('select2')) $s.select2('destroy'); }catch(e){} $s.next('.select2-container').remove(); }

  // Resolve wrapping Livewire component id for the country select
  function getCountryComponentId(){
    const el = document.getElementById('senderCountrySelect');
    if(!el) return null;
    const root = el.closest('[wire\\:id]');
    return root ? root.getAttribute('wire:id') : null;
  }

  function initCountry(){
    const $s = $('#senderCountrySelect'); if(!$s.length) return;
    destroy($s);
    $s.select2({
      theme:'bootstrap4',
      width:'100%',
      placeholder: $s.data('placeholder') || 'Choose a country...',
      templateResult:formatCountry,
      templateSelection:formatCountry,
      matcher:matcher,
      allowClear:true,
      language:{ noResults:()=> 'No matches' }
    });

    // When country changes, push value to Livewire and reset state select immediately
    $s.off('change.sender').on('change.sender', function(){
      const parentId = getCountryComponentId();
      if(parentId){
        window.Livewire.find(parentId).set('country_id', $(this).val() || null);
      }
      const $state = $('#senderStateSelect');
      $state.val(null).trigger('change');   // clear UI
      $state.prop('disabled', true);        // disable until options refresh
    });

    // Preselect from hidden wire model
    $s.val($('#sender_country_id_wire').val() || '').trigger('change.select2');
  }

  document.addEventListener('livewire:load', function(){
    initCountry();
    Livewire.hook('message.processed', () => {
      if($('#senderCountrySelect').length) initCountry();
    });
  });
})();
</script>

{{-- STATE/PROVINCE select: Select2 + Livewire binding via component id (no @this) --}}
<script>
// STATE/PROVINCE select: Fixed Select2 + Livewire binding
document.addEventListener('livewire:load', function () {
  const $state = $('#senderStateSelect');

  function getStateComponentId() {
    const el = document.getElementById('senderStateSelect');
    if (!el) return null;
    const root = el.closest('[wire\\:id]');
    return root ? root.getAttribute('wire:id') : null;
  }

  function initStateSelect() {
    // Destroy existing Select2
    try { 
      if ($state.data('select2')) {
        $state.select2('destroy'); 
      }
    } catch (e) {}

    // Re-initialize Select2
    $state.select2({
      theme: 'bootstrap4',
      width: '100%',
      allowClear: true,
      placeholder: $state.data('placeholder') || 'Choose a state/province...'
    });

    // Bind change event to update Livewire
    $state.off('change.state').on('change.state', function () {
      const val = $(this).val() || null;
      const parentId = getStateComponentId();
      if (parentId) {
        window.Livewire.find(parentId).set('state_id', val);
      }
    });

    // Enable/disable based on options (excluding blank option)
    const hasStates = $state.find('option').length > 1;
    $state.prop('disabled', !hasStates);

    // Restore selected value from Livewire model
    const wireValue = $('#sender_state_id_wire').val();
    if (wireValue && hasStates) {
      $state.val(wireValue).trigger('change.select2');
    } else if (!hasStates) {
      $state.val(null).trigger('change.select2');
    }
  }

  // Initial initialization
  initStateSelect();

  // Re-initialize after Livewire updates DOM
  Livewire.hook('message.processed', () => {
    if ($('#senderStateSelect').length) {
      // Small delay to ensure DOM is fully updated
      setTimeout(initStateSelect, 50);
    }
  });
});
</script>

{{-- Receipt tab helper (unchanged) --}}
<script>
let _tabs=[];
function openReceiptPlaceholders(){
  _tabs = [window.open('', '_blank'), window.open('', '_blank')];
}
window.addEventListener('open-receipts', e=>{
  (e.detail.urls||[]).forEach((u,i)=>{
    if (_tabs[i] && !_tabs[i].closed) _tabs[i].location = u;
    else window.open(u, '_blank', 'noopener,noreferrer');
  });
});
</script>
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Customer Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Applications') }}</li>
                            <li class="breadcrumb-item text-muted active">{{ __('Sender') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('Create Send Form') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('sender.sender-create-livewire')
        {{-- @livewire('sender.country-info-panel-livewire', ['countryId' => null], key('country-info-'.$country_id))
        @livewire('sender.country-info-panel-livewire', ['countryId' => null], key('country-info-'.$country_id)) --}}
        @livewire('sender.country-info-panel-livewire', ['countryId' => null], key('country-info'))


    </div>
@endsection
