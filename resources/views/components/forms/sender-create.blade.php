<div class="card border-0 shadow-sm">
  <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-semibold"><b>{{ __('Create New Sender') }}</b></h5>
  </div>
  <hr class="mt-0"/>

  <div class="card-body pt-0">
    <form wire:submit.prevent="submit">
      <div class="row g-4">

        {{-- Sender names (always uppercase) --}}
        <div class="col-md-6 mb-4">
          <label for="sender_first_name" class="form-label">{{ __('Sender First Name') }} <span class="text-danger">*</span></label>
          <input id="sender_first_name" type="text" placeholder="{{ __('FIRST NAME') }}"
                 style="text-transform:uppercase"
                 wire:model.debounce.500ms="sender_first_name"
                 class="{{ $this->getInputClass('sender_first_name') }}"
                 required>
          @error('sender_first_name') <div class="invalid-feedback d-block">{{ $message }}</div>
          @else @if(isset($touched['sender_first_name'])) <div class="valid-feedback d-block">{{ __('Looks good!') }}</div> @endif @enderror
        </div>

        <div class="col-md-6 mb-4">
          <label for="sender_last_name" class="form-label">{{ __('Sender Last Name') }} <span class="text-danger">*</span></label>
          <input id="sender_last_name" type="text" placeholder="{{ __('LAST NAME') }}"
                 style="text-transform:uppercase"
                 wire:model.debounce.500ms="sender_last_name"
                 class="{{ $this->getInputClass('sender_last_name') }}"
                 required>
          @error('sender_last_name') <div class="invalid-feedback d-block">{{ $message }}</div>
          @else @if(isset($touched['sender_last_name'])) <div class="valid-feedback d-block">{{ __('Looks good!') }}</div> @endif @enderror
        </div>

        {{-- Sender phone (required) --}}
        <div class="col-md-6 mb-4">
          <label for="sender_phone_number" class="form-label">{{ __('Sender Phone Number') }} <span class="text-danger">*</span></label>
          <input id="sender_phone_number" placeholder="+9647xxxxxxxx"
                 wire:model.debounce.500ms="sender_phone_number"
                 type="tel"
                 inputmode="tel"
                 autocomplete="tel"
                 class="{{ $this->getInputClass('sender_phone_number') }}"
                 required>
          @error('sender_phone_number') <div class="invalid-feedback d-block">{{ $message }}</div>
          @else @if(isset($touched['sender_phone_number'])) <div class="valid-feedback d-block">{{ __('Looks good!') }}</div> @endif @enderror
        </div>

        {{-- Sender address (required) --}}
        <div class="col-md-6 mb-4">
          <label for="sender_address" class="form-label">{{ __('Sender Address') }} <span class="text-danger">*</span></label>
          <input id="sender_address" type="text" placeholder="{{ __('Street, City') }}"
                 wire:model.debounce.500ms="sender_address"
                 class="{{ $this->getInputClass('sender_address') }}"
                 required>
          @error('sender_address') <div class="invalid-feedback d-block">{{ $message }}</div>
          @else @if(isset($touched['sender_address']) && $sender_address) <div class="valid-feedback d-block">{{ __('Looks good!') }}</div> @endif @enderror
        </div>

        {{-- Country (Select2, ID-based) --}}
        <div class="col-md-6 mb-4">
          <label class="form-label">{{ __('Country') }} <span class="text-danger">*</span></label>

          <div class="form-control" wire:ignore>
            <input type="hidden" id="sender_country_id_wire" wire:model="country_id">
            <select id="senderCountrySelect" class="form-control" data-placeholder="{{ __('Choose a country...') }}" required>
              <option value=""></option>
              @foreach($availableCountries as $c)
                <option value="{{ $c['id'] }}"
                        data-flag="{{ app('cloudfrontflagsx2').'/'.$c['flag_path'] }}"
                        data-iso="{{ strtoupper($c['iso_code']) }}"
                        data-ar="{{ $c['ar_name'] }}"
                        data-ku="{{ $c['ku_name'] }}">
                  {{ $c['en_name'] }}
                </option>
              @endforeach
            </select>
          </div>

          @error('country_id')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        {{-- Amount / Commission / Total --}}
        <div class="col-md-2 mb-4">
          <label for="amount" class="form-label">{{ __('Amount (USD)') }} <span class="text-danger">*</span></label>
          <input id="amount" type="number" step="0.01"
                min="{{ $minLimit ?? 0.01 }}"
                @if($maxLimit) max="{{ $maxLimit }}" @endif
                wire:model.debounce.500ms="amount"
                class="{{ $this->getInputClass('amount') }}"
                required>
          <small class="text-muted d-block mt-1">
            @if($minLimit && $maxLimit)
              {{ __('Limit:') }} {{ number_format($minLimit,2) }} – {{ number_format($maxLimit,2) }} {{ __('USD') }}
            @elseif($minLimit)
              {{ __('Min:') }} {{ number_format($minLimit,2) }} {{ __('USD') }}
            @elseif($maxLimit)
              {{ __('Max:') }} {{ number_format($maxLimit,2) }} {{ __('USD') }}
            @endif
          </small>
          @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-2 mb-4">
          <label for="commission" class="form-label">{{ __('Commission (USD)') }} <span class="text-danger">*</span></label>
          <input id="commission" type="number" step="0.01" min="0"
                wire:model.debounce.500ms="commission"
                class="{{ $this->getInputClass('commission') }}"
                required readonly>
          @error('commission') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-2 mb-4">
          <label for="total" class="form-label">{{ __('Total (USD)') }}</label>
          <input id="total" type="number" step="0.01" min="0" readonly
                wire:model="total"
                class="{{ $this->getInputClass('total') }}">
          <small class="{{ $errors->has('total') ? 'text-danger' : 'text-muted' }}">
            {{ __('Auto = Amount + Commission') }}
          </small>
          @error('total') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Receiver snapshot (optional; names uppercase) --}}
        <div class="col-md-4 mb-4">
          <label for="receiver_first_name" class="form-label">{{ __('Receiver First Name') }}</label>
          <input id="receiver_first_name" type="text" placeholder="{{ __('FIRST NAME') }}"
                 style="text-transform:uppercase"
                 wire:model.debounce.500ms="receiver_first_name"
                 class="{{ $this->getInputClass('receiver_first_name') }}"
                 required>
          @error('receiver_first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4 mb-4">
          <label for="receiver_last_name" class="form-label">{{ __('Receiver Last Name') }}</label>
          <input id="receiver_last_name" type="text" placeholder="{{ __('LAST NAME') }}"
                 style="text-transform:uppercase"
                 wire:model.debounce.500ms="receiver_last_name"
                 class="{{ $this->getInputClass('receiver_last_name') }}"
                 required>
          @error('receiver_last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4 mb-4">
          <label for="receiver_phone_number" class="form-label">{{ __('Receiver Phone') }}</label>
          <input id="receiver_phone_number" type="tel" inputmode="tel" placeholder="+xxxxxxxxxxxx"
                 wire:model.debounce.500ms="receiver_phone_number"
                 class="{{ $this->getInputClass('receiver_phone_number') }}">
          @error('receiver_phone_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
          <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          {{ __('Save Sender') }}
        </button>
      </div>
    </form>
</div>
</div>