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

  /* MODERN */
          :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --card-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

          .modern-header {
            background: var(--primary-gradient);
            position: relative;
            padding: 2rem 1.5rem;
            overflow: hidden;
        }

        .modern-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='1'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            opacity: 0.5;
        }

        .floating-labels label {
            left: 16px;           
            background: white;
            padding: 0 8px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #667eea;
            z-index: 10;
        }

                .modern-input {
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            padding: 12px 16px;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .modern-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: #ffffff;
            outline: none;
        }

        .badge-modern {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
            border: none;
            position: relative;
            overflow: hidden;
        }

                .badge-modern::before {
            content: '';
            position: absolute;
            inset: 0;
            background: inherit;
            filter: brightness(0.9);
            opacity: 0.1;
        }

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
        }

        .avatar-modern::before {
            content: '';
            position: absolute;
            background: inherit;
        }

        .amount-display-b {
            font-weight: 700;
            font-size: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .amount-display-r {
            font-weight: 700;
            font-size: 1rem;
            background: linear-gradient(135deg, #ea6666, #a24b4b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .amount-display-g {
            font-weight: 700;
            font-size: 1rem;
            background: linear-gradient(135deg, #66ea7c, #4ba272);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .btn-group .btn{
          border-radius: 8px;
          margin: 2px;
        }
</style>
@endpush
@push('scripts')
{{-- <script src="{{ asset('assets/js/utils/country_select/countrySelect.min.js') }}"></script> --}}
<script src="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

@push('scripts')
<script>
(function(){
  function strip(s){ if(s==null) return ''; const t=String(s).toLowerCase(); try{return t.normalize('NFD').replace(/[\u0300-\u036f]/g,'');}catch(_){return t;} }
  function matcher(params, data){
    const qRaw=(params.term||'').trim(); if(qRaw==='') return data;
    const q=strip(qRaw), text=strip(data.text||'');
    const $el = data.element ? $(data.element) : null;
    // IMPORTANT: use jQuery .data() not dataset
    const iso = strip($el?.data('iso'));
    const ar  = strip($el?.data('ar'));
    const ku  = strip($el?.data('ku'));
    return (text.includes(q) || iso.includes(q) || ar.includes(q) || ku.includes(q)) ? data : null;
  }
  function formatCountry(state){
    if(!state.id) return state.text;
    const $el = $(state.element);
    const flag = $el.data('flag');
    return flag ? $(`<span><img src="${flag}" style="height:12px;margin-right:6px;vertical-align:-2px;"> ${state.text}</span>`) : state.text;
  }
  function destroy($s){ try{ if($s.data('select2')) $s.select2('destroy'); }catch(e){} $s.next('.select2-container').remove(); }

  function init(){
    const $s = $('#pendingCountrySelect'); if(!$s.length) return;

    destroy($s);
    $s.select2({
      theme: 'bootstrap4',
      width: '100%',
      allowClear: true,
      minimumResultsForSearch: 0,     // <-- always show search box
      placeholder: $s.data('placeholder') || 'All countries',
      templateResult: formatCountry,
      templateSelection: formatCountry,
      matcher: matcher,
      language: { noResults: () => '{{ __('No matches') }}' }
    });

    $s.off('change.pending').on('change.pending', function(){
      const val = $(this).val() || '';
      const root = this.closest('[wire\\:id]');
      if(root && window.Livewire){
        const id = root.getAttribute('wire:id');
        window.Livewire.find(id).set('country', val);
      }
    });

    // Preselect from Livewire
    const current = $('#pending_country_wire').val() || '';
    $s.val(current).trigger('change.select2');

    // Optional: open on focus so users can type immediately
    $s.on('select2:open', () => {
      document.querySelector('.select2-container--open .select2-search__field')?.focus();
    });
  }

  document.addEventListener('livewire:load', function(){
    init();
    Livewire.hook('message.processed', () => {
      if($('#pendingCountrySelect').length) init();
    });
  });
})();
</script>
@endpush

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
        @livewire('sender.pending-transfer-livewire')
    </div>
@endsection
