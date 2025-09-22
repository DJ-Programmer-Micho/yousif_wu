@extends('layouts.app')
@push('css')
<link rel="stylesheet" href="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
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
{{-- <script src="{{ asset('assets/js/utils/country_select/countrySelect.min.js') }}"></script> --}}
<script src="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<script>
(function(){
  function strip(s){ if(s==null) return ''; const t=String(s).toLowerCase(); try{return t.normalize('NFD').replace(/[\u0300-\u036f]/g,'');}catch(_){return t;} }
  function matcher(params, data){
    const qRaw=(params.term||'').trim(); if(qRaw==='') return data;
    const q=strip(qRaw), text=strip(data.text||'');
    const $el=data.element?$(data.element):null;
    const iso=strip($el?.data('iso')), ar=strip($el?.data('ar')), ku=strip($el?.data('ku'));
    return (text.includes(q)||iso.includes(q)||ar.includes(q)||ku.includes(q)) ? data : null;
  }
  function formatCountry(state){
    if(!state.id) return state.text;
    const flag=$(state.element).data('flag');
    return flag ? $(`<span><img src="${flag}"> ${state.text}</span>`) : state.text;
  }
  function destroy($s){ try{ if($s.data('select2')) $s.select2('destroy'); }catch(e){} $s.next('.select2-container').remove(); }

  // Always get the PARENT Livewire component id that wraps the select
  function getParentComponentId(){
    const el = document.getElementById('senderCountrySelect');
    if(!el) return null;
    const root = el.closest('[wire\\:id]');
    return root ? root.getAttribute('wire:id') : null;
  }

  function init(){
    const $s = $('#senderCountrySelect'); if(!$s.length) return;
    destroy($s);
    $s.select2({
      theme:'bootstrap4', width:'100%',
      placeholder: $s.data('placeholder') || 'Choose a country...',
      templateResult:formatCountry, templateSelection:formatCountry, matcher:matcher,
      language:{ noResults:()=> 'No matches' }
    });
    $s.off('change.sender').on('change.sender', function(){
      const parentId = getParentComponentId();
      if(parentId){
        window.Livewire.find(parentId).set('country_id', $(this).val() || null);
      }
    });
    // Preselect from hidden wire model
    $s.val($('#sender_country_id_wire').val() || '').trigger('change.select2');
  }

  document.addEventListener('livewire:load', function(){
    init();
    // After ANY Livewire DOM patch, re-init (don’t pass component.id; we resolve parent each time)
    Livewire.hook('message.processed', () => {
      if($('#senderCountrySelect').length) init();
    });
  });
})();
</script>
<script>
let _tabs=[];
function openReceiptPlaceholders(){
  _tabs = [window.open('', '_blank'), window.open('', '_blank')];
}
window.addEventListener('open-receiver-receipts', e=>{
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
                            <li class="breadcrumb-item text-muted active">{{ __('Receiver') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('Create Reciever Form') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('receiver.receiver-create-livewire')
    </div>
@endsection
