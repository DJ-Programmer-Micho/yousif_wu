@once
  @push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
      .sw-shell{display:grid;grid-template-columns:minmax(0,1.8fr) minmax(320px,.9fr);gap:1.5rem;align-items:start}
      .sw-card,.sw-side{border:1px solid rgba(255,255,255,.72);border-radius:28px;background:linear-gradient(180deg,rgba(255,255,255,.97),rgba(248,250,252,.93));box-shadow:0 24px 60px rgba(15,23,42,.08);backdrop-filter:blur(16px)}
      .sw-card{overflow:hidden}
      .sw-head{padding:1.7rem 1.8rem 1.35rem;border-bottom:1px solid rgba(226,232,240,.9);background:radial-gradient(circle at top right,rgba(236,72,153,.12),transparent 24%),radial-gradient(circle at left center,rgba(124,58,237,.12),transparent 34%),linear-gradient(180deg,rgba(255,255,255,.97),rgba(248,250,252,.93))}
      .sw-kicker,.sw-pill{display:inline-flex;align-items:center;padding:.38rem .8rem;border-radius:999px;background:rgba(124,58,237,.12);color:#5b21b6;font-size:.76rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase}
      .sw-title{margin:.9rem 0 .4rem;color:#0f172a;font-size:1.6rem;font-weight:900}
      .sw-sub{margin:0;color:#64748b;line-height:1.7}
      .sw-progress{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.85rem;margin-top:1.3rem}
      .sw-step{display:flex;align-items:center;gap:.75rem;padding:.9rem;border-radius:20px;border:1px solid rgba(226,232,240,.9);background:rgba(255,255,255,.8)}
      .sw-step:not(.active):not(.done){border-color:rgba(96,165,250,.26);background:radial-gradient(circle at top right,rgba(56,189,248,.18),transparent 38%),radial-gradient(circle at bottom left,rgba(59,130,246,.14),transparent 40%),linear-gradient(135deg,rgba(255,255,255,.98),rgba(239,246,255,.94));box-shadow:0 14px 28px rgba(15,23,42,.05)}
      .sw-step.active{background:linear-gradient(135deg,rgba(124,58,237,.14),rgba(236,72,153,.08));border-color:rgba(124,58,237,.24)}
      .sw-step.done{background:rgba(236,253,245,.92);border-color:rgba(16,185,129,.2)}
      .sw-step-badge{display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:14px;background:rgba(148,163,184,.16);color:#475569;font-weight:900;flex:0 0 auto}
      .sw-step:not(.active):not(.done) .sw-step-badge{background:linear-gradient(135deg,rgba(59,130,246,.16),rgba(6,182,212,.2));color:#1d4ed8;box-shadow:inset 0 1px 0 rgba(255,255,255,.7)}
      .sw-step.active .sw-step-badge{background:linear-gradient(135deg,#7c3aed,#ec4899);color:#fff}
      .sw-step.done .sw-step-badge{background:rgba(16,185,129,.14);color:#047857}
      .sw-step-copy strong{display:block;color:#111827;font-size:.92rem}
      .sw-step-copy span{display:block;margin-top:.15rem;color:#64748b;font-size:.78rem}
      .sw-step:not(.active):not(.done) .sw-step-copy strong{color:#1e3a8a}
      .sw-step:not(.active):not(.done) .sw-step-copy span{color:#2563eb}
      .sw-bar{height:8px;margin-top:1rem;border-radius:999px;background:rgba(226,232,240,.92);overflow:hidden}
      .sw-bar span{display:block;height:100%;border-radius:inherit;background:linear-gradient(135deg,#7c3aed,#ec4899);transition:width .28s ease}
      .sw-body{padding:1.6rem 1.8rem 1.75rem}
      .sw-alert{margin-bottom:1.2rem;border:none;border-radius:18px;background:linear-gradient(135deg,rgba(124,58,237,.12),rgba(59,130,246,.08));color:#4c1d95}
      .sw-panel{padding:1.35rem;border-radius:24px;border:1px solid rgba(226,232,240,.9);background:rgba(255,255,255,.82);box-shadow:0 14px 30px rgba(15,23,42,.04)}
      .sw-panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.25rem}
      .sw-panel-head h6{margin:0 0 .3rem;color:#111827;font-size:1.08rem;font-weight:900}
      .sw-panel-head p{margin:0;color:#64748b;line-height:1.65}
      .sw-label{display:block;margin-bottom:.55rem;color:#334155;font-weight:700}
      .sw-input{min-height:52px;border-radius:16px;border:1px solid rgba(148,163,184,.22);background:rgba(255,255,255,.92);box-shadow:none}
      .sw-input:focus{border-color:rgba(124,58,237,.35);box-shadow:0 0 0 .2rem rgba(124,58,237,.12)}
      .sw-input[readonly]{background:linear-gradient(180deg,rgba(248,250,252,.95),rgba(241,245,249,.92));color:#4338ca;font-weight:800}
      .sw-input.is-valid{border-color:rgba(16,185,129,.48);background:rgba(240,253,244,.92)}
      .sw-input.is-invalid{border-color:rgba(239,68,68,.5);background:rgba(254,242,242,.92)}
      .sw-help{display:block;margin-top:.45rem;color:#64748b;font-size:.8rem}
      .sw-select .select2-container{width:100%!important}
      .sw-select .select2-selection--single{min-height:52px!important;border-radius:16px!important;border:1px solid rgba(148,163,184,.22)!important;background:rgba(255,255,255,.92)!important;display:flex!important;align-items:center!important;padding:0 14px!important;box-shadow:none!important}
      .sw-select .select2-selection__rendered{display:flex!important;align-items:center!important;line-height:1.2!important;padding-left:0!important;color:#111827!important}
      .sw-select .select2-selection__arrow{height:50px!important;right:10px!important}
      .sw-select .select2-results__option img,.sw-select .select2-selection__rendered img{height:14px;width:auto;margin-right:8px;border-radius:2px}
      .sw-select.is-invalid .select2-selection--single{border-color:#dc3545!important;box-shadow:0 0 0 .2rem rgba(220,53,69,.1)!important}
      .sw-quote{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.9rem;margin-top:1rem}
      .sw-q{padding:1rem 1.05rem;border-radius:20px;background:rgba(255,255,255,.82);border:1px solid rgba(226,232,240,.9);box-shadow:0 12px 24px rgba(15,23,42,.05)}
      .sw-q.primary{background:linear-gradient(135deg,rgba(124,58,237,.14),rgba(236,72,153,.08));border-color:rgba(124,58,237,.18)}
      .sw-q-label{display:block;color:#64748b;font-size:.76rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em}
      .sw-q-value{display:block;margin-top:.5rem;color:#111827;font-size:1.28rem;font-weight:900}
      .sw-q.primary .sw-q-value{color:#5b21b6}
      .sw-q-note{display:block;margin-top:.45rem;color:#64748b;font-size:.8rem;line-height:1.55}
      .sw-review{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
      .sw-review-section{padding:1.15rem;border-radius:22px;border:1px solid rgba(226,232,240,.9);background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(248,250,252,.92));box-shadow:0 12px 26px rgba(15,23,42,.04)}
      .sw-review-section.wide{grid-column:1 / -1}
      .sw-review-section.summary{background:radial-gradient(circle at top right,rgba(236,72,153,.12),transparent 36%),linear-gradient(180deg,rgba(245,243,255,.95),rgba(255,255,255,.96));border-color:rgba(196,181,253,.42)}
      .sw-review-head{display:flex;align-items:flex-start;justify-content:space-between;gap:.85rem;margin-bottom:1rem}
      .sw-review-kicker{display:inline-flex;align-items:center;padding:.32rem .7rem;border-radius:999px;background:rgba(124,58,237,.1);color:#6d28d9;font-size:.72rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase}
      .sw-review-head h6{margin:.5rem 0 0;color:#111827;font-size:1rem;font-weight:900}
      .sw-review-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.85rem}
      .sw-review-item{padding:.9rem 1rem;border-radius:18px;background:rgba(255,255,255,.86);border:1px solid rgba(226,232,240,.85)}
      .sw-review-item.highlight{background:linear-gradient(135deg,rgba(124,58,237,.12),rgba(236,72,153,.08));border-color:rgba(124,58,237,.18)}
      .sw-review-item.wide{grid-column:1 / -1}
      .sw-review-label{display:block;color:#64748b;font-size:.76rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase}
      .sw-review-value{display:block;margin-top:.45rem;color:#111827;font-size:1rem;font-weight:800;line-height:1.5;word-break:break-word}
      .sw-foot{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-top:1.4rem;flex-wrap:wrap}
      .sw-foot p{flex:1 1 280px;margin:0;color:#64748b;line-height:1.65}
      .sw-actions{display:flex;align-items:center;gap:.75rem;margin-left:auto}
      .sw-prev,.sw-next,.sw-submit{min-width:170px;min-height:52px;border:none;border-radius:16px;font-weight:800}
      .sw-prev{background:rgba(241,245,249,.96);color:#334155}
      .sw-next,.sw-submit{background:linear-gradient(135deg,#7c3aed,#5b5ce6);color:#fff;box-shadow:0 18px 32px rgba(124,58,237,.22)}
      .sw-side{padding:1.45rem}
      .sw-side h5{margin:0 0 .35rem;color:#111827;font-size:1.15rem;font-weight:900}
      .sw-side p{margin:0;color:#64748b;line-height:1.65}
      .sw-search{position:relative;margin:1.15rem 0 1rem}
      .sw-search input{min-height:52px;padding-right:44px}
      .sw-search i{position:absolute;top:50%;right:16px;transform:translateY(-50%);color:#94a3b8}
      .sw-list{display:flex;flex-direction:column;gap:.85rem}
      .sw-item{display:flex;align-items:center;gap:.85rem;width:100%;padding:.9rem;border:none;border-radius:20px;background:rgba(255,255,255,.82);box-shadow:0 12px 24px rgba(15,23,42,.05);text-align:left}
      .sw-avatar{display:inline-flex;align-items:center;justify-content:center;width:46px;height:46px;border-radius:16px;background:linear-gradient(135deg,rgba(124,58,237,.18),rgba(236,72,153,.14));color:#6d28d9;font-weight:900;flex:0 0 auto}
      .sw-copy{min-width:0;flex:1 1 auto}
      .sw-copy strong{display:block;color:#111827;font-size:.95rem}
      .sw-copy span{display:block;color:#64748b;font-size:.8rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
      .sw-meta{color:#5b21b6;font-size:.8rem;font-weight:800}
      .sw-empty{padding:1.4rem 1rem;border-radius:18px;background:rgba(248,250,252,.92);color:#94a3b8;text-align:center}
      .select2-container--bootstrap4 .select2-dropdown{border-radius:18px!important;border-color:rgba(148,163,184,.18)!important;overflow:hidden;box-shadow:0 18px 40px rgba(15,23,42,.14)}
      .select2-container--bootstrap4 .select2-results>.select2-results__options{max-height:280px!important;overflow-y:auto!important}
      @media (max-width:1199.98px){.sw-shell{grid-template-columns:1fr}}
      @media (max-width:767.98px){.sw-head,.sw-body,.sw-side{padding:1.1rem}.sw-progress,.sw-review,.sw-review-grid{grid-template-columns:1fr}.sw-panel-head,.sw-foot,.sw-review-head{flex-direction:column;align-items:flex-start}.sw-quote{grid-template-columns:1fr}.sw-review-section.wide,.sw-review-item.wide{grid-column:auto}.sw-actions{width:100%;margin-left:0;flex-direction:column-reverse}.sw-prev,.sw-next,.sw-submit{width:100%}}
    </style>
  @endpush
@endonce

@once
  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script>
      (function () {
        function strip(v){ if(v==null) return ''; const t=String(v).toLowerCase(); try{return t.normalize('NFD').replace(/[\u0300-\u036f]/g,'');}catch(_){return t;} }
        function matcher(params,data){ const q=(params.term||'').trim(); if(q==='') return data; const term=strip(q), text=strip(data.text||''); const el=data.element?$(data.element):null; const iso=strip(el?el.data('iso'):''); const ar=strip(el?el.data('ar'):''); const ku=strip(el?el.data('ku'):''); return (text.includes(term)||iso.includes(term)||ar.includes(term)||ku.includes(term))?data:null; }
        function fmtCountry(opt){ if(!opt.id) return opt.text; const flag=$(opt.element).data('flag'); return flag ? $(`<span><img src="${flag}" alt=""> ${opt.text}</span>`) : opt.text; }
        function destroy($s){ try{ if($s.data('select2')) $s.select2('destroy'); }catch(_){ } $s.next('.select2-container').remove(); }
        function rootId(id){ const el=document.getElementById(id); if(!el) return null; const root=el.closest('[wire\\:id]'); return root?root.getAttribute('wire:id'):null; }
        function initCountry(){ const $s=$('#senderV2CountrySelect'); if(!$s.length) return; destroy($s); $s.select2({theme:'bootstrap4',width:'100%',placeholder:$s.data('placeholder')||'Choose a country...',templateResult:fmtCountry,templateSelection:fmtCountry,matcher:matcher,allowClear:true,language:{noResults:()=> 'No matches'}}); $s.off('change.sw-country').on('change.sw-country',function(){ const id=rootId('senderV2CountrySelect'); const component=id ? window.Livewire.find(id) : null; if(component){ component.set('country_id', $(this).val()||null); component.set('state_id', null); } const $state=$('#senderV2StateSelect'); if($state.length){ $state.val(null).trigger('change'); $state.prop('disabled', true); } }); const id=rootId('senderV2CountrySelect'); let value=$('#sender_v2_country_id_wire').val()||''; if(id){ const component=window.Livewire.find(id); if(component && typeof component.get==='function') value=component.get('country_id')||value; } $s.val(value).trigger('change.select2'); }
        function initState(){ const $s=$('#senderV2StateSelect'); if(!$s.length) return; destroy($s); $s.select2({theme:'bootstrap4',width:'100%',allowClear:true,placeholder:$s.data('placeholder')||'Choose a state/province...'}); $s.off('change.sw-state').on('change.sw-state',function(){ const id=rootId('senderV2StateSelect'); if(id) window.Livewire.find(id).set('state_id', $(this).val()||null); }); const hasStates=$s.find('option').length>1; $s.prop('disabled', !hasStates); const id=rootId('senderV2StateSelect'); let value=$('#sender_v2_state_id_wire').val()||''; if(id){ const component=window.Livewire.find(id); if(component && typeof component.get==='function') value=component.get('state_id')||value; } if(value && hasStates) $s.val(value).trigger('change.select2'); else $s.val(null).trigger('change.select2'); }
        document.addEventListener('livewire:load', function(){ initCountry(); initState(); Livewire.hook('message.processed', function(){ if($('#senderV2CountrySelect').length) initCountry(); if($('#senderV2StateSelect').length) setTimeout(initState,50); }); });
      })();
    </script>
  @endpush
@endonce

@php
  $quoteAmount = is_numeric($amount) ? (float) $amount : 0.0;
  $quoteCommission = is_numeric($commission) ? (float) $commission : 0.0;
  $quoteTotal = is_numeric($total) ? (float) $total : 0.0;
  $stepTitles = [1 => __('Sender Information'), 2 => __('Destination & Quote'), 3 => __('Receiver Information'), 4 => __('Confirmation')];
  $showStateField = !empty($country_id) && count($availableStates) > 0;
  $countryColumnClass = $showStateField ? 'col-lg-4 col-md-6 mb-4' : 'col-lg-6 col-md-6 mb-4';
  $amountColumnClass = $showStateField ? 'col-lg-4 col-md-12 mb-4' : 'col-lg-6 col-md-12 mb-4';
@endphp

<div class="sw-shell">
  <div class="sw-card">
    <div class="sw-head">
      <span class="sw-kicker">{{ __('Sender Wizard') }}</span>
      <h5 class="sw-title">{{ __('Create Transfer in 4 Steps') }}</h5>
      <p class="sw-sub">{{ __('Move through sender info, transfer setup, receiver details, and a final confirmation review without losing your data between steps.') }}</p>

      <div class="sw-progress">
        @foreach($stepTitles as $stepNumber => $stepTitle)
          @php $stepClass = $currentStep === $stepNumber ? 'active' : ($currentStep > $stepNumber ? 'done' : ''); @endphp
          <div class="sw-step {{ $stepClass }}">
            <span class="sw-step-badge">
              @if($currentStep > $stepNumber)
                <i class="fas fa-check"></i>
              @else
                {{ $stepNumber }}
              @endif
            </span>
            <div class="sw-step-copy">
              <strong>{{ $stepTitle }}</strong>
              <span>{{ __('Step') }} {{ $stepNumber }} {{ __('of') }} 4</span>
            </div>
          </div>
        @endforeach
      </div>

      <div class="sw-bar"><span style="width: {{ $this->progressPercent }}%;"></span></div>
    </div>

    <div class="sw-body">
      @if(session('quick_send_prefilled') || $hasQuickPrefill)
        <div class="alert sw-alert">
          <strong>{{ __('Quick Send detected.') }}</strong>
          {{ __('The transfer country and amount were carried into Step 2. Complete Step 1, then continue.') }}
        </div>
      @endif
      @if($currentStep === 1)
        <div class="sw-panel">
          <div class="sw-panel-head">
            <div>
              <h6>{{ __('Sender Information') }}</h6>
              <p>{{ __('Capture the sender identity and address that will be attached to this transfer.') }}</p>
            </div>
            <span class="sw-pill">{{ __('Required') }}</span>
          </div>
          <div class="row">
            <div class="col-md-6 mb-4">
              <label class="sw-label">{{ __('Sender First Name') }}</label>
              <input type="text" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '')" autocomplete="given-name" placeholder="{{ __('FIRST NAME') }}" style="text-transform:uppercase" wire:model.debounce.500ms="sender_first_name" class="{{ $this->getInputClass('sender_first_name') }} sw-input">
              @error('sender_first_name') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="sw-label">{{ __('Sender Last Name') }}</label>
              <input type="text" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '')" autocomplete="family-name" placeholder="{{ __('LAST NAME') }}" style="text-transform:uppercase" wire:model.debounce.500ms="sender_last_name" class="{{ $this->getInputClass('sender_last_name') }} sw-input">
              @error('sender_last_name') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="sw-label">{{ __('Sender Phone Number') }}</label>
              <input type="tel" inputmode="tel" autocomplete="tel" placeholder="+9647xxxxxxxx" wire:model.debounce.500ms="sender_phone_number" class="{{ $this->getInputClass('sender_phone_number') }} sw-input">
              @error('sender_phone_number') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="sw-label">{{ __('Sender Address') }}</label>
              <input type="text" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '')" autocomplete="street-address" placeholder="{{ __('Street, City') }}" wire:model.debounce.500ms="sender_address" class="{{ $this->getInputClass('sender_address') }} sw-input">
              @error('sender_address') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      @elseif($currentStep === 2)
        <div class="sw-panel">
          <div class="sw-panel-head">
            <div>
              <h6>{{ __('Destination & Transfer Quote') }}</h6>
              <p>{{ __('Choose the destination and amount. Commission and total update live from the same business rules as the current sender form.') }}</p>
            </div>
            <span class="sw-pill">{{ __('Live') }}</span>
          </div>
          <div class="row">
            <div class="{{ $countryColumnClass }}">
              <label class="sw-label">{{ __('Country') }}</label>
              <div class="sw-select @error('country_id') is-invalid @enderror" wire:ignore>
                <input type="hidden" id="sender_v2_country_id_wire" wire:model="country_id">
                <select id="senderV2CountrySelect" class="form-control sw-input" data-placeholder="{{ __('Choose a country...') }}">
                  <option value=""></option>
                  @foreach($availableCountries as $country)
                    <option value="{{ $country['id'] }}" data-flag="{{ app('cloudfrontflagsx2').'/'.$country['flag_path'] }}" data-iso="{{ strtoupper($country['iso_code']) }}" data-ar="{{ $country['ar_name'] }}" data-ku="{{ $country['ku_name'] }}">{{ $country['en_name'] }}</option>
                  @endforeach
                </select>
              </div>
              @error('country_id') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
            @if($showStateField)
              <div class="col-lg-4 col-md-6 mb-4">
                <label class="sw-label">{{ __('State / Province') }} @if($stateRequired)<span class="text-danger">*</span>@endif</label>
                <div class="sw-select @error('state_id') is-invalid @enderror" wire:key="sender-v2-state-{{ $country_id ?: 'none' }}-{{ count($availableStates) }}">
                  <input type="hidden" id="sender_v2_state_id_wire" wire:model="state_id">
                  <select id="senderV2StateSelect" class="form-control sw-input" data-placeholder="{{ __('Choose a state/province...') }}">
                    <option value=""></option>
                    @foreach($availableStates as $state)
                      <option value="{{ $state['id'] }}">{{ $state['en_name'] }}</option>
                    @endforeach
                  </select>
                </div>
                @if($stateRequired)<small class="sw-help">{{ __('State or province is required for destinations such as the United States and Canada.') }}</small>@endif
                @error('state_id') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
              </div>
            @endif
            <div class="{{ $amountColumnClass }}">
              <label class="sw-label">{{ __('Amount (USD)') }}</label>
              <input type="number" step="0.01" min="{{ $minLimit ?? 0.01 }}" @if($maxLimit) max="{{ $maxLimit }}" @endif wire:model.debounce.500ms="amount" class="{{ $this->getInputClass('amount') }} sw-input">
              @error('amount') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
              @if($minLimit || $maxLimit)
                <small class="sw-help">
                  @if($minLimit && $maxLimit)
                    {{ __('Allowed range:') }} {{ number_format($minLimit, 2) }} - {{ number_format($maxLimit, 2) }} {{ __('USD') }}
                  @elseif($minLimit)
                    {{ __('Minimum allowed:') }} {{ number_format($minLimit, 2) }} {{ __('USD') }}
                  @elseif($maxLimit)
                    {{ __('Maximum allowed:') }} {{ number_format($maxLimit, 2) }} {{ __('USD') }}
                  @endif
                </small>
              @endif
            </div>
          </div>

          <div class="sw-quote">
            <div class="sw-q">
              <span class="sw-q-label">{{ __('Amount') }}</span>
              <span class="sw-q-value">${{ number_format($quoteAmount, 2) }}</span>
              <span class="sw-q-note">{{ __('Transfer principal before fees.') }}</span>
            </div>
            <div class="sw-q">
              <span class="sw-q-label">{{ __('Commission') }}</span>
              <span class="sw-q-value">${{ number_format($quoteCommission, 2) }}</span>
              <span class="sw-q-note">{{ __('Calculated from the selected destination brackets.') }}</span>
            </div>
            <div class="sw-q primary">
              <span class="sw-q-label">{{ __('Total Payable') }}</span>
              <span class="sw-q-value">${{ number_format($quoteTotal, 2) }}</span>
              <span class="sw-q-note">{{ __('Auto-updated in real time as amount and destination change.') }}</span>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-6 mb-4 mb-md-0">
              <label class="sw-label">{{ __('Commission (USD)') }}</label>
              <input type="number" step="0.01" readonly wire:model="commission" class="{{ $this->getInputClass('commission') }} sw-input">
              @error('commission') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="sw-label">{{ __('Total (USD)') }}</label>
              <input type="number" step="0.01" readonly wire:model="total" class="{{ $this->getInputClass('total') }} sw-input">
              @error('total') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      @elseif($currentStep === 3)
        <div class="sw-panel">
          <div class="sw-panel-head">
            <div>
              <h6>{{ __('Receiver Information') }}</h6>
              <p>{{ __('Add an optional receiver snapshot for easier follow-up after the sender is created.') }}</p>
            </div>
            <span class="sw-pill">{{ __('Optional Snapshot') }}</span>
          </div>
          <div class="row">
            <div class="col-md-4 mb-4">
              <label class="sw-label">{{ __('Receiver First Name') }}</label>
              <input type="text" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '')" autocomplete="off" placeholder="{{ __('FIRST NAME') }}" style="text-transform:uppercase" wire:model.debounce.500ms="receiver_first_name" class="{{ $this->getInputClass('receiver_first_name') }} sw-input">
              @error('receiver_first_name') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-4">
              <label class="sw-label">{{ __('Receiver Last Name') }}</label>
              <input type="text" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '')" autocomplete="off" placeholder="{{ __('LAST NAME') }}" style="text-transform:uppercase" wire:model.debounce.500ms="receiver_last_name" class="{{ $this->getInputClass('receiver_last_name') }} sw-input">
              @error('receiver_last_name') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-4">
              <label class="sw-label">{{ __('Receiver Phone') }}</label>
              <input type="tel" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '')" inputmode="tel" autocomplete="off" placeholder="+xxxxxxxxxxxx" wire:model.debounce.500ms="receiver_phone_number" class="{{ $this->getInputClass('receiver_phone_number') }} sw-input">
              @error('receiver_phone_number') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      @else
        <div class="sw-panel">
          <div class="sw-panel-head">
            <div>
              <h6>{{ __('Confirmation & Review') }}</h6>
              <p>{{ __('Review all entered information from the previous steps before confirming the transfer.') }}</p>
            </div>
            <span class="sw-pill">{{ __('Final Check') }}</span>
          </div>
          <div class="sw-review">
            <div class="sw-review-section wide">
              <div class="sw-review-head">
                <div>
                  <span class="sw-review-kicker">{{ __('Sender') }}</span>
                  <h6>{{ __('Sender Details') }}</h6>
                </div>
                <span class="sw-pill">{{ __('Step 1') }}</span>
              </div>
              <div class="sw-review-grid">
                <div class="sw-review-item">
                  <span class="sw-review-label">{{ __('Sender Name') }}</span>
                  <span class="sw-review-value">{{ trim($sender_first_name . ' ' . $sender_last_name) ?: '-' }}</span>
                </div>
                <div class="sw-review-item">
                  <span class="sw-review-label">{{ __('Sender Phone') }}</span>
                  <span class="sw-review-value">{{ $sender_phone_number ?: '-' }}</span>
                </div>
                <div class="sw-review-item wide">
                  <span class="sw-review-label">{{ __('Sender Address') }}</span>
                  <span class="sw-review-value">{{ $sender_address ?: '-' }}</span>
                </div>
              </div>
            </div>

            <div class="sw-review-section summary">
              <div class="sw-review-head">
                <div>
                  <span class="sw-review-kicker">{{ __('Transfer') }}</span>
                  <h6>{{ __('Destination & Quote') }}</h6>
                </div>
                <span class="sw-pill">{{ __('Step 2') }}</span>
              </div>
              <div class="sw-review-grid">
                <div class="sw-review-item">
                  <span class="sw-review-label">{{ __('Country') }}</span>
                  <span class="sw-review-value">{{ $this->selectedCountryName ?: '-' }}</span>
                </div>
                <div class="sw-review-item">
                  <span class="sw-review-label">{{ __('State / Province') }}</span>
                  <span class="sw-review-value">{{ $this->selectedStateName ?: '-' }}</span>
                </div>
                <div class="sw-review-item">
                  <span class="sw-review-label">{{ __('Amount') }}</span>
                  <span class="sw-review-value">${{ number_format($quoteAmount, 2) }}</span>
                </div>
                <div class="sw-review-item">
                  <span class="sw-review-label">{{ __('Commission') }}</span>
                  <span class="sw-review-value">${{ number_format($quoteCommission, 2) }}</span>
                </div>
                <div class="sw-review-item wide highlight">
                  <span class="sw-review-label">{{ __('Total Payable') }}</span>
                  <span class="sw-review-value">${{ number_format($quoteTotal, 2) }}</span>
                </div>
              </div>
            </div>

            <div class="sw-review-section">
              <div class="sw-review-head">
                <div>
                  <span class="sw-review-kicker">{{ __('Receiver') }}</span>
                  <h6>{{ __('Receiver Snapshot') }}</h6>
                </div>
                <span class="sw-pill">{{ __('Step 3') }}</span>
              </div>
              <div class="sw-review-grid">
                <div class="sw-review-item wide">
                  <span class="sw-review-label">{{ __('Receiver Name') }}</span>
                  <span class="sw-review-value">{{ trim($receiver_first_name . ' ' . $receiver_last_name) ?: '-' }}</span>
                </div>
                <div class="sw-review-item wide">
                  <span class="sw-review-label">{{ __('Receiver Phone') }}</span>
                  <span class="sw-review-value">{{ $receiver_phone_number ?: '-' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif

      <div class="sw-foot">
        <p>
          {{ __('Step') }} {{ $currentStep }} {{ __('of') }} 4.
          @if($currentStep < 4)
            {{ __('Your data stays in memory while you move forward and backward through the wizard.') }}
          @else
            {{ __('Confirming now runs the same save, balance, notification, and receipt logic as the original sender form.') }}
          @endif
        </p>
        <div class="sw-actions">
          @if($currentStep > 1)
            <button type="button" class="btn sw-prev" wire:click="previousStep" wire:loading.attr="disabled">{{ __('Previous') }}</button>
          @endif
          @if($currentStep < 4)
            <button type="button" class="btn sw-next" wire:click="nextStep" wire:loading.attr="disabled">{{ __('Next') }}</button>
          @else
            <button type="button" class="btn sw-submit" wire:click="submit" wire:loading.attr="disabled">
              <span wire:loading.remove wire:target="submit">{{ __('Confirm Transfer') }}</span>
              <span wire:loading wire:target="submit"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>{{ __('Submitting...') }}</span>
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="sw-side">
    <h5>{{ __('Recent Senders') }}</h5>
    <p>{{ __('Search by customer name or phone and load the latest sender details directly into Step 1.') }}</p>
    <div class="sw-search">
      <input type="text" class="form-control sw-input" placeholder="{{ __('Search name or phone...') }}" wire:model.debounce.350ms="senderSearch">
      <i class="fas fa-search"></i>
    </div>
    <div class="sw-list">
      @forelse($this->senderSidebarResults as $sender)
        @php $initials = strtoupper(mb_substr((string) $sender->first_name, 0, 1) . mb_substr((string) $sender->last_name, 0, 1)); @endphp
        <button type="button" class="sw-item" wire:click="loadSender({{ $sender->id }})">
          <span class="sw-avatar">{{ $initials ?: 'S' }}</span>
          <span class="sw-copy">
            <strong>{{ trim($sender->first_name . ' ' . $sender->last_name) }}</strong>
            <span>{{ $sender->phone }}</span>
            <span>{{ $sender->address ?: $sender->country }}</span>
          </span>
          <span class="sw-meta">{{ __('Use') }}</span>
        </button>
      @empty
        <div class="sw-empty">{{ __('No sender matches found right now.') }}</div>
      @endforelse
    </div>
  </div>
</div>
