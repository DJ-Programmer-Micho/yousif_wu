@include('components.forms._country-general-ui')

@php
  $viewCountry = optional(\App\Models\Country::find($country_id));
@endphp

<div
  id="countryRuleCreateModal"
  class="modal country-admin-modal @if($showCreate) show d-block @endif"
  tabindex="-1"
  role="dialog"
  @if($showCreate) style="background:rgba(15,23,42,.45)" @endif
>
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h6 class="modal-title">{{ __('Add Country to Not-Allowed List') }}</h6>
          <p class="country-admin-subtitle mb-0">{{ __('Mark a destination as blocked so it cannot be selected for new transfers.') }}</p>
        </div>
        <button class="close" wire:click="$set('showCreate',false)">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="country-admin-label">{{ __('Country') }}</label>
          <div wire:ignore class="country-admin-select-shell @error('country_id') is-invalid @enderror">
            <input type="hidden" id="cr_country_id_wire" wire:model="country_id">
            <select id="crCountrySelect" class="form-control country-admin-input" data-placeholder="{{ __('Choose a country...') }}">
              <option value=""></option>
              @foreach($available as $c)
                <option
                  value="{{ $c->id }}"
                  data-flag="{{ app('cloudfrontflagsx2').'/'.$c->flag_path }}"
                  data-iso="{{ $c->iso_code }}"
                  data-ar="{{ $c->ar_name }}"
                  data-ku="{{ $c->ku_name }}"
                >
                  {{ $c->en_name }}
                </option>
              @endforeach
            </select>
          </div>
          @error('country_id')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
        </div>

        <div class="country-admin-note is-muted">
          {{ __('The selected country will be added to the blocked list and removed from normal transfer selection until the rule is deleted.') }}
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light border" wire:click="$set('showCreate',false)">{{ __('Cancel') }}</button>
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
          <h6 class="modal-title">{{ __('View Rule') }}</h6>
          <p class="country-admin-subtitle mb-0">{{ __('Read-only view of the current block status for the selected country.') }}</p>
        </div>
        <button class="close" wire:click="$set('showEdit',false)">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="country-admin-label">{{ __('Country') }}</label>
          <div class="country-admin-static-field">
            @if($viewCountry && $viewCountry->flag_path)
              <img src="{{ app('cloudfrontflagsx2').'/'.$viewCountry->flag_path }}" class="country-admin-flag" alt="">
            @endif
            <span>{{ $viewCountry->en_name }}</span>
          </div>
        </div>

        <span class="country-admin-pill country-admin-pill-danger">{{ __('Not allowed to transfer') }}</span>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light border" wire:click="$set('showEdit',false)">{{ __('Close') }}</button>
      </div>
    </div>
  </div>
</div>

<div
  id="countryRuleDeleteModal"
  class="modal country-admin-modal @if($showDelete) show d-block @endif"
  tabindex="-1"
  role="dialog"
  @if($showDelete) style="background:rgba(15,23,42,.45)" @endif
>
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h6 class="modal-title">{{ __('Remove Country') }}</h6>
          <p class="country-admin-subtitle mb-0">{{ __('Deleting this rule will allow transfers for the country again.') }}</p>
        </div>
        <button class="close" wire:click="$set('showDelete',false)">&times;</button>
      </div>
      <div class="modal-body">
        <p class="mb-2">{{ __('Remove') }} <strong>{{ $deleteName }}</strong> {{ __('from the blocked list?') }}</p>
        <small class="text-muted d-block">{{ __('This takes effect immediately after deletion.') }}</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light border" wire:click="$set('showDelete',false)">{{ __('Cancel') }}</button>
        <button class="btn btn-danger" wire:click="delete">{{ __('Delete') }}</button>
      </div>
    </div>
  </div>
</div>
