{{-- resources/views/components/forms/sender-create.blade.php --}}
@push('css')
<style>
  .sender-create-card{
    border-radius:28px;
    overflow:hidden;
    background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.92));
    box-shadow:0 24px 55px rgba(15,23,42,.08);
  }
  .sender-create-header{
    position:relative;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:18px;
    padding:28px 30px 22px;
    background:
      radial-gradient(circle at top right, rgba(236,72,153,.18), transparent 28%),
      radial-gradient(circle at left center, rgba(124,58,237,.14), transparent 32%),
      linear-gradient(135deg, rgba(248,250,252,.98), rgba(244,236,255,.94));
    border-bottom:1px solid rgba(148,163,184,.14);
  }
  .sender-create-header::after{
    content:"";
    position:absolute;
    right:-56px;
    top:-78px;
    width:220px;
    height:220px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(167,139,250,.22), transparent 64%);
    pointer-events:none;
  }
  .sender-create-eyebrow{
    display:inline-block;
    margin-bottom:8px;
    color:#7c3aed;
    font-size:.74rem;
    font-weight:800;
    letter-spacing:.08em;
    text-transform:uppercase;
  }
  .sender-create-title{
    margin:0;
    font-size:1.4rem;
    font-weight:900;
    color:#0f172a;
  }
  .sender-create-subtitle{
    margin:8px 0 0;
    max-width:680px;
    color:#64748b;
    font-size:.92rem;
    line-height:1.6;
  }
  .sender-create-badge{
    position:relative;
    z-index:1;
    min-width:200px;
    padding:15px 16px;
    border-radius:22px;
    background:rgba(255,255,255,.78);
    border:1px solid rgba(148,163,184,.16);
    backdrop-filter:blur(14px);
    box-shadow:0 14px 26px rgba(99,102,241,.1);
    text-align:right;
  }
  .sender-create-badge span{
    display:block;
    color:#64748b;
    font-size:.73rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.05em;
  }
  .sender-create-badge strong{
    display:block;
    margin-top:6px;
    color:#4c1d95;
    font-size:1rem;
    font-weight:900;
  }
  .sender-create-body{
    padding:24px;
    background:linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.94));
  }
  .sender-form-section{
    margin-bottom:20px;
    padding:22px;
    border-radius:24px;
    background:linear-gradient(180deg, rgba(255,255,255,.92), rgba(248,250,252,.8));
    border:1px solid rgba(148,163,184,.14);
    box-shadow:0 16px 34px rgba(15,23,42,.04);
  }
  .sender-form-section-highlight{
    background:
      radial-gradient(circle at top right, rgba(216,180,254,.18), transparent 24%),
      linear-gradient(180deg, rgba(252,248,255,.96), rgba(245,241,255,.92));
    border-color:rgba(124,58,237,.16);
  }
  .sender-section-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:14px;
    margin-bottom:18px;
    flex-wrap:wrap;
  }
  .sender-section-title{
    margin:0;
    color:#0f172a;
    font-size:1rem;
    font-weight:900;
  }
  .sender-section-subtitle{
    margin:6px 0 0;
    color:#64748b;
    font-size:.86rem;
    line-height:1.55;
  }
  .sender-section-pill{
    display:inline-flex;
    align-items:center;
    padding:.45rem .85rem;
    border-radius:999px;
    background:rgba(124,58,237,.08);
    color:#5b21b6;
    font-size:.76rem;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.05em;
  }
  .sender-section-pill-muted{
    background:rgba(148,163,184,.12);
    color:#475569;
  }
  .sender-field-label{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:4px;
    margin-bottom:.55rem;
    color:#111827;
    font-size:.92rem;
    font-weight:800;
  }
  .sender-input{
    min-height:52px;
    border-radius:16px;
    border:1px solid rgba(148,163,184,.22);
    background:rgba(255,255,255,.92);
    box-shadow:none;
  }
  .sender-input:focus{
    border-color:rgba(124,58,237,.35);
    box-shadow:0 0 0 .2rem rgba(124,58,237,.12);
  }
  .sender-input[readonly]{
    background:linear-gradient(180deg, rgba(248,250,252,.95), rgba(241,245,249,.92));
    color:#4338ca;
    font-weight:800;
  }
  .sender-input.is-valid{
    border-color:rgba(16,185,129,.48);
    background:rgba(240,253,244,.92);
  }
  .sender-input.is-invalid{
    border-color:rgba(239,68,68,.56);
    background:rgba(254,242,242,.92);
  }
  .sender-select-shell{
    padding:0;
    border:none;
    background:transparent;
  }
  .sender-select-shell .select2-container{
    width:100% !important;
  }
  .sender-select-shell .select2-selection--single{
    min-height:52px !important;
    border-radius:16px !important;
    border:1px solid rgba(148,163,184,.22) !important;
    background:rgba(255,255,255,.92) !important;
    display:flex !important;
    align-items:center !important;
    padding:0 14px !important;
    box-shadow:none !important;
  }
  .sender-select-shell .select2-selection__rendered{
    display:flex !important;
    align-items:center !important;
    line-height:1.2 !important;
    padding-left:0 !important;
    color:#111827 !important;
  }
  .sender-select-shell .select2-selection__arrow{
    height:50px !important;
    right:10px !important;
  }
  .sender-select-shell.is-invalid .select2-selection--single{
    border-color:#dc3545 !important;
    box-shadow:0 0 0 .2rem rgba(220,53,69,.1) !important;
  }
  .sender-help{
    display:block;
    margin-top:8px;
    color:#64748b;
    font-size:.78rem;
    line-height:1.5;
  }
  .sender-feedback{
    margin-top:8px;
  }
  .sender-quote-grid{
    display:grid;
    grid-template-columns:repeat(3, minmax(0, 1fr));
    gap:14px;
    margin-top:8px;
  }
  .sender-quote-card{
    padding:16px 18px;
    border-radius:20px;
    background:rgba(255,255,255,.82);
    border:1px solid rgba(148,163,184,.14);
    box-shadow:0 12px 26px rgba(15,23,42,.05);
  }
  .sender-quote-card.is-primary{
    background:linear-gradient(135deg, rgba(124,58,237,.14), rgba(236,72,153,.08));
    border-color:rgba(124,58,237,.18);
  }
  .sender-quote-label{
    display:block;
    color:#64748b;
    font-size:.75rem;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.05em;
  }
  .sender-quote-value{
    display:block;
    margin-top:8px;
    color:#0f172a;
    font-size:1.24rem;
    font-weight:900;
    line-height:1.15;
  }
  .sender-quote-card.is-primary .sender-quote-value{
    color:#5b21b6;
  }
  .sender-quote-note{
    display:block;
    margin-top:8px;
    color:#64748b;
    font-size:.78rem;
    line-height:1.45;
  }
  .sender-limit-note{
    margin-top:14px;
    padding:12px 14px;
    border-radius:16px;
    background:rgba(124,58,237,.08);
    color:#5b21b6;
    font-size:.82rem;
    font-weight:700;
  }
  .sender-footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-top:24px;
    flex-wrap:wrap;
  }
  .sender-footer-note{
    flex:1 1 280px;
    margin:0;
    color:#64748b;
    font-size:.86rem;
    line-height:1.6;
  }
  .sender-submit-btn{
    min-width:220px;
    min-height:54px;
    border:none;
    border-radius:16px;
    padding:0 22px;
    background:linear-gradient(135deg,#7c3aed,#5b5ce6);
    color:#fff;
    font-weight:900;
    letter-spacing:.01em;
    box-shadow:0 18px 30px rgba(124,58,237,.22);
    transition:all .22s ease;
  }
  .sender-submit-btn:hover:not(:disabled){
    color:#fff;
    transform:translateY(-1px);
    box-shadow:0 22px 34px rgba(124,58,237,.26);
  }
  .sender-submit-btn:disabled{
    opacity:.72;
    box-shadow:none;
    cursor:not-allowed;
  }
  @media (max-width: 767.98px){
    .sender-create-header{
      padding:22px 20px 18px;
    }
    .sender-create-badge{
      min-width:0;
      width:100%;
      text-align:left;
    }
    .sender-create-body{
      padding:18px;
    }
    .sender-form-section{
      padding:18px;
    }
    .sender-quote-grid{
      grid-template-columns:1fr;
    }
    .sender-footer{
      align-items:stretch;
    }
    .sender-submit-btn{
      width:100%;
      min-width:0;
    }
  }
</style>
@endpush

@php
  $quoteAmount = is_numeric($amount) ? (float) $amount : 0.0;
  $quoteCommission = is_numeric($commission) ? (float) $commission : 0.0;
  $quoteTotal = is_numeric($total) ? (float) $total : 0.0;
  $quoteReady = $quoteAmount > 0;
@endphp

<div class="card border-0 sender-create-card">
  <div class="sender-create-header">
    <div>
      <span class="sender-create-eyebrow">{{ __('Sender Intake') }}</span>
      <h5 class="sender-create-title">{{ __('Create New Sender') }}</h5>
      <p class="sender-create-subtitle">
        {{ __('Capture sender information, choose the destination country, and review the live fee calculation before saving the transfer.') }}
      </p>
    </div>

    <div class="sender-create-badge">
      <span>{{ __('Quote Mode') }}</span>
      <strong>{{ __('Amount + Commission = Total') }}</strong>
    </div>
  </div>

  <div class="sender-create-body">
    <form wire:submit.prevent="submit">
      <div class="sender-form-section">
        <div class="sender-section-top">
          <div>
            <h6 class="sender-section-title">{{ __('Sender Details') }}</h6>
            <p class="sender-section-subtitle">{{ __('Basic customer identity, contact number, and address used for the transaction record.') }}</p>
          </div>
          <span class="sender-section-pill">{{ __('Required') }}</span>
        </div>

        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="sender_first_name" class="sender-field-label">{{ __('Sender First Name') }} <span class="text-danger">*</span></label>
            <input
              id="sender_first_name"
              type="text"
              autocomplete="given-name"
              placeholder="{{ __('FIRST NAME') }}"
              style="text-transform:uppercase"
              wire:model.debounce.500ms="sender_first_name"
              class="{{ $this->getInputClass('sender_first_name') }} sender-input"
              required
            >
            @error('sender_first_name')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @else
              @if(isset($touched['sender_first_name']))
                <div class="valid-feedback d-block sender-feedback">{{ __('Looks good!') }}</div>
              @endif
            @enderror
          </div>

          <div class="col-md-6 mb-4">
            <label for="sender_last_name" class="sender-field-label">{{ __('Sender Last Name') }} <span class="text-danger">*</span></label>
            <input
              id="sender_last_name"
              type="text"
              autocomplete="family-name"
              placeholder="{{ __('LAST NAME') }}"
              style="text-transform:uppercase"
              wire:model.debounce.500ms="sender_last_name"
              class="{{ $this->getInputClass('sender_last_name') }} sender-input"
              required
            >
            @error('sender_last_name')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @else
              @if(isset($touched['sender_last_name']))
                <div class="valid-feedback d-block sender-feedback">{{ __('Looks good!') }}</div>
              @endif
            @enderror
          </div>

          <div class="col-md-6 mb-4">
            <label for="sender_phone_number" class="sender-field-label">{{ __('Sender Phone Number') }} <span class="text-danger">*</span></label>
            <input
              id="sender_phone_number"
              type="tel"
              inputmode="tel"
              autocomplete="tel"
              placeholder="+9647xxxxxxxx"
              wire:model.debounce.500ms="sender_phone_number"
              class="{{ $this->getInputClass('sender_phone_number') }} sender-input"
              required
            >
            @error('sender_phone_number')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @else
              @if(isset($touched['sender_phone_number']))
                <div class="valid-feedback d-block sender-feedback">{{ __('Looks good!') }}</div>
              @endif
            @enderror
          </div>

          <div class="col-md-6 mb-4">
            <label for="sender_address" class="sender-field-label">{{ __('Sender Address') }} <span class="text-danger">*</span></label>
            <input
              id="sender_address"
              type="text"
              autocomplete="street-address"
              placeholder="{{ __('Street, City') }}"
              wire:model.debounce.500ms="sender_address"
              class="{{ $this->getInputClass('sender_address') }} sender-input"
              oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '')"
              required
            >
            @error('sender_address')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @else
              @if(isset($touched['sender_address']) && $sender_address)
                <div class="valid-feedback d-block sender-feedback">{{ __('Looks good!') }}</div>
              @endif
            @enderror
          </div>
        </div>
      </div>

      <div class="sender-form-section sender-form-section-highlight">
        <div class="sender-section-top">
          <div>
            <h6 class="sender-section-title">{{ __('Destination & Transfer Quote') }}</h6>
            <p class="sender-section-subtitle">{{ __('The commission and total update automatically from the selected country rules and transfer amount.') }}</p>
          </div>
          <span class="sender-section-pill">{{ __('Live Quote') }}</span>
        </div>
        <div class="sender-limit-note mb-3">
          @if($minLimit && $maxLimit)
            {{ __('Allowed range:') }} {{ number_format($minLimit, 2) }} - {{ number_format($maxLimit, 2) }} {{ __('USD') }}
          @elseif($minLimit)
            {{ __('Minimum allowed:') }} {{ number_format($minLimit, 2) }} {{ __('USD') }}
          @elseif($maxLimit)
            {{ __('Maximum allowed:') }} {{ number_format($maxLimit, 2) }} {{ __('USD') }}
          @else
            {{ __('Fees and total update automatically when the amount or destination changes.') }}
          @endif
        </div>
        <div class="row">
          <div class="col-lg-4 col-md-6 mb-4">
            <label class="sender-field-label">{{ __('Country') }} <span class="text-danger">*</span></label>
            <div class="sender-select-shell @error('country_id') is-invalid @enderror" wire:ignore>
              <input type="hidden" id="sender_country_id_wire" wire:model="country_id">
              <select id="senderCountrySelect" class="form-control sender-input" data-placeholder="{{ __('Choose a country...') }}" required>
                <option value=""></option>
                @foreach($availableCountries as $c)
                  <option
                    value="{{ $c['id'] }}"
                    data-flag="{{ app('cloudfrontflagsx2').'/'.$c['flag_path'] }}"
                    data-iso="{{ strtoupper($c['iso_code']) }}"
                    data-ar="{{ $c['ar_name'] }}"
                    data-ku="{{ $c['ku_name'] }}"
                  >
                    {{ $c['en_name'] }}
                  </option>
                @endforeach
              </select>
            </div>
            @error('country_id')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-lg-4 col-md-6 mb-4">
            <label class="sender-field-label">
              {{ __('State / Province') }}
              @if($stateRequired)
                <span class="text-danger">*</span>
              @endif
            </label>

            <div class="sender-select-shell @error('state_id') is-invalid @enderror">
              <input type="hidden" id="sender_state_id_wire" wire:model="state_id">
              <select
                id="senderStateSelect"
                class="form-control sender-input"
                data-placeholder="{{ __('Choose a state/province...') }}"
              >
                <option value=""></option>
                @foreach($availableStates as $s)
                  <option
                    value="{{ $s['id'] }}"
                    data-code="{{ $s['code'] }}"
                    data-ar="{{ $s['ar_name'] }}"
                    data-ku="{{ $s['ku_name'] }}"
                  >
                    {{ $s['en_name'] }}
                  </option>
                @endforeach
              </select>
            </div>

            @if($stateRequired)
              <small class="sender-help">{{ __('State or province is required for selected destinations such as the United States and Canada.') }}</small>
            @endif
            @error('state_id')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-lg-4 col-md-12 mb-4">
            <label for="amount" class="sender-field-label">{{ __('Amount (USD)') }} <span class="text-danger">*</span></label>
            <input
              id="amount"
              type="number"
              step="0.01"
              min="{{ $minLimit ?? 0.01 }}"
              @if($maxLimit) max="{{ $maxLimit }}" @endif
              wire:model.debounce.500ms="amount"
              class="{{ $this->getInputClass('amount') }} sender-input"
              required
            >
            @error('amount')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="sender-quote-grid">
          <div class="sender-quote-card">
            <span class="sender-quote-label">{{ __('Amount') }}</span>
            <strong class="sender-quote-value">${{ number_format($quoteAmount, 2) }}</strong>
            <span class="sender-quote-note">{{ __('Transfer principal before fees.') }}</span>
          </div>

          <div class="sender-quote-card">
            <span class="sender-quote-label">{{ __('Commission') }}</span>
            <strong class="sender-quote-value">${{ number_format($quoteCommission, 2) }}</strong>
            <span class="sender-quote-note">{{ __('Calculated from the current country fee brackets.') }}</span>
          </div>

          <div class="sender-quote-card is-primary">
            <span class="sender-quote-label">{{ __('Total Payable') }}</span>
            <strong class="sender-quote-value">${{ number_format($quoteTotal, 2) }}</strong>
            <span class="sender-quote-note">
              {{ $quoteReady ? __('Ready to save with the live calculation.') : __('Enter an amount to preview the final total.') }}
            </span>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-6 mb-4 mb-md-0">
            <label for="commission" class="sender-field-label">{{ __('Commission (USD)') }} <span class="text-danger">*</span></label>
            <input
              id="commission"
              type="number"
              step="0.01"
              min="0"
              wire:model.debounce.500ms="commission"
              class="{{ $this->getInputClass('commission') }} sender-input"
              required
              readonly
            >
            @error('commission')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="total" class="sender-field-label">{{ __('Total (USD)') }}</label>
            <input
              id="total"
              type="number"
              step="0.01"
              min="0"
              readonly
              wire:model="total"
              class="{{ $this->getInputClass('total') }} sender-input"
            >
            <small class="sender-help {{ $errors->has('total') ? 'text-danger' : '' }}">{{ __('Auto calculated from amount + commission.') }}</small>
            @error('total')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>


      </div>

      <div class="sender-form-section mb-0">
        <div class="sender-section-top">
          <div>
            <h6 class="sender-section-title">{{ __('Receiver Snapshot') }}</h6>
            <p class="sender-section-subtitle">{{ __('Optional receiver information for faster follow-up. You can still save the sender without completing this section.') }}</p>
          </div>
          <span class="sender-section-pill sender-section-pill-muted">{{ __('Optional') }}</span>
        </div>

        <div class="row">
          <div class="col-md-4 mb-4">
            <label for="receiver_first_name" class="sender-field-label">{{ __('Receiver First Name') }}</label>
            <input
              id="receiver_first_name"
              type="text"
              autocomplete="off"
              placeholder="{{ __('FIRST NAME') }}"
              style="text-transform:uppercase"
              wire:model.debounce.500ms="receiver_first_name"
              class="{{ $this->getInputClass('receiver_first_name') }} sender-input"
            >
            @error('receiver_first_name')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4 mb-4">
            <label for="receiver_last_name" class="sender-field-label">{{ __('Receiver Last Name') }}</label>
            <input
              id="receiver_last_name"
              type="text"
              autocomplete="off"
              placeholder="{{ __('LAST NAME') }}"
              style="text-transform:uppercase"
              wire:model.debounce.500ms="receiver_last_name"
              class="{{ $this->getInputClass('receiver_last_name') }} sender-input"
            >
            @error('receiver_last_name')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4 mb-4">
            <label for="receiver_phone_number" class="sender-field-label">{{ __('Receiver Phone') }}</label>
            <input
              id="receiver_phone_number"
              type="tel"
              inputmode="tel"
              autocomplete="off"
              placeholder="+xxxxxxxxxxxx"
              wire:model.debounce.500ms="receiver_phone_number"
              class="{{ $this->getInputClass('receiver_phone_number') }} sender-input"
            >
            @error('receiver_phone_number')
              <div class="invalid-feedback d-block sender-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <div class="sender-footer">
        <p class="sender-footer-note">
          {{ __('The business logic, balance checks, and commission rules remain unchanged. This form only improves the structure, clarity, and live quote presentation.') }}
        </p>

        <button type="submit" class="btn sender-submit-btn" wire:loading.attr="disabled">
          <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
          {{ __('Save Sender') }}
        </button>
      </div>
    </form>
  </div>
</div>
