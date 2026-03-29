@include('components.forms._country-general-ui')

@php
  $editCountry = optional(\App\Models\Country::find($country_id));
@endphp

<div
  id="createLimitModal"
  class="modal country-admin-modal @if($showCreate) show d-block @endif"
  tabindex="-1"
  role="dialog"
  @if($showCreate) style="background:rgba(15,23,42,.45)" @endif
>
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h6 class="modal-title">{{ __('Add Country Limit') }}</h6>
          <p class="country-admin-subtitle mb-0">{{ __('Create a destination-specific limit that overrides the global fallback.') }}</p>
        </div>
        <button type="button" class="close" wire:click="$set('showCreate', false)">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="country-admin-label">{{ __('Country') }}</label>
          <div wire:ignore class="country-admin-select-shell @error('country_id') is-invalid @enderror">
            <input type="hidden" id="country_id_wire" wire:model="country_id">
            <select id="countrySelect" class="form-control country-admin-input" data-placeholder="{{ __('Choose a country...') }}">
              <option value=""></option>
              @foreach($availableCountries as $c)
                <option value="{{ $c->id }}" data-flag="{{ app('cloudfrontflagsx2').'/'.$c->flag_path }}">
                  {{ $c->en_name }}
                </option>
              @endforeach
            </select>
          </div>
          @error('country_id')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="country-admin-label">{{ __('Minimum') }}</label>
            <input type="number" step="0.01" class="form-control country-admin-input" wire:model.defer="min_value">
            @error('min_value')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
          </div>
          <div class="form-group col-md-6">
            <label class="country-admin-label">{{ __('Maximum') }}</label>
            <input type="number" step="0.01" class="form-control country-admin-input" wire:model.defer="max_value">
            @error('max_value')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
          </div>
        </div>

        <div class="country-admin-note">
          {{ __('This exception is used only for the selected country. All other countries keep using the configured general limit.') }}
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light border" wire:click="$set('showCreate', false)">{{ __('Cancel') }}</button>
        <button class="btn btn-primary" wire:click="store">{{ __('Save') }}</button>
      </div>
    </div>
  </div>
</div>

<div
  class="modal country-admin-modal @if($showEdit) show d-block @endif"
  tabindex="-1"
  role="dialog"
  @if($showEdit) style="background:rgba(15,23,42,.45)" @endif
>
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h6 class="modal-title">{{ __('Edit Country Limit') }}</h6>
          <p class="country-admin-subtitle mb-0">{{ __('Adjust the minimum and maximum values for this destination.') }}</p>
        </div>
        <button type="button" class="close" wire:click="$set('showEdit', false)">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="country-admin-label">{{ __('Country') }}</label>
          <div class="country-admin-static-field">
            @if($editCountry && $editCountry->flag_path)
              <img src="{{ app('cloudfrontflagsx2').'/'.$editCountry->flag_path }}" class="country-admin-flag" alt="">
            @endif
            <span>{{ $editCountry->en_name }}</span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="country-admin-label">{{ __('Minimum') }}</label>
            <input type="number" step="0.01" class="form-control country-admin-input" wire:model.defer="min_value">
            @error('min_value')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
          </div>
          <div class="form-group col-md-6">
            <label class="country-admin-label">{{ __('Maximum') }}</label>
            <input type="number" step="0.01" class="form-control country-admin-input" wire:model.defer="max_value">
            @error('max_value')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light border" wire:click="$set('showEdit', false)">{{ __('Close') }}</button>
        <button class="btn btn-primary" wire:click="update">{{ __('Update') }}</button>
      </div>
    </div>
  </div>
</div>

<div
  id="deleteLimitModal"
  class="modal country-admin-modal @if($showDeleteLimitModal) show d-block @endif"
  tabindex="-1"
  role="dialog"
  @if($showDeleteLimitModal) style="background:rgba(15,23,42,.45)" @endif
>
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h6 class="modal-title">{{ __('Delete Country Limit') }}</h6>
          <p class="country-admin-subtitle mb-0">{{ __('This permanently removes the exception and sends the country back to the general fallback.') }}</p>
        </div>
        <button class="close" wire:click="$set('showDeleteLimitModal', false)">&times;</button>
      </div>
      <div class="modal-body">
        <p class="mb-2">{{ __('Delete limit for:') }} <strong>{{ $deleteLimitSummary }}</strong>?</p>
        <small class="text-muted d-block">{{ __('This action cannot be undone.') }}</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light border" wire:click="$set('showDeleteLimitModal', false)">{{ __('Cancel') }}</button>
        <button class="btn btn-danger" wire:click="deleteLimit">{{ __('Delete') }}</button>
      </div>
    </div>
  </div>
</div>
