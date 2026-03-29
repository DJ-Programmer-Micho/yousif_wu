@include('components.forms._country-general-ui')

<div class="card country-admin-shell">
  <div class="country-admin-header is-success">
    <div>
      <span class="country-admin-eyebrow">{{ __('General Limits') }}</span>
      <h5 class="country-admin-title">{{ __('Fallback Transfer Limits') }}</h5>
      <p class="country-admin-subtitle">{{ __('These values apply automatically to any country that does not have a dedicated exception row.') }}</p>
    </div>
    <div class="country-admin-badge-card">
      <span>{{ __('Mode') }}</span>
      <strong>{{ __('Global Fallback') }}</strong>
    </div>
  </div>

  <div class="country-admin-body">
    @if (session('success'))
      <div class="alert alert-success country-admin-alert">{{ session('success') }}</div>
    @endif

    {{-- <div class="country-admin-stat-grid">
      <div class="country-admin-stat">
        <span class="country-admin-stat-label">{{ __('Minimum') }}</span>
        <strong class="country-admin-stat-value">{{ is_numeric($min_value) ? number_format((float) $min_value, 2) : '--' }}</strong>
      </div>
      <div class="country-admin-stat">
        <span class="country-admin-stat-label">{{ __('Maximum') }}</span>
        <strong class="country-admin-stat-value">{{ is_numeric($max_value) ? number_format((float) $max_value, 2) : '--' }}</strong>
      </div>
    </div> --}}

    <div class="form-row">
      <div class="form-group col-md-4">
        <label class="country-admin-label">{{ __('Minimum') }}</label>
        <input type="number" step="0.01" class="form-control country-admin-input" wire:model.defer="min_value">
        @error('min_value')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
      </div>
      <div class="form-group col-md-4">
        <label class="country-admin-label">{{ __('Maximum') }}</label>
        <input type="number" step="0.01" class="form-control country-admin-input" wire:model.defer="max_value">
        @error('max_value')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
      </div>
    </div>

    <div class="country-admin-note">
      {{ __('Used for all countries that do not have a specific Country Limit.') }}
    </div>

    <div class="mt-3">
      <button class="btn btn-primary" wire:click="save">{{ __('Save') }}</button>
    </div>
  </div>
</div>
