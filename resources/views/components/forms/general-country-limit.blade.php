<div class="card">
  <div class="card-header"><strong>{{ __('General Limits (Fallback)') }}</strong></div>
  <div class="card-body">
    @if (session('success')) <div class="alert alert-success py-2">{{ session('success') }}</div> @endif
    <div class="form-row">
      <div class="form-group col-md-3">
        <label>{{ __('Minimum') }}</label>
        <input type="number" step="0.01" class="form-control" wire:model.defer="min_value">
        @error('min_value')<small class="text-danger">{{ $message }}</small>@enderror
      </div>
      <div class="form-group col-md-3">
        <label>{{ __('Maximum') }}</label>
        <input type="number" step="0.01" class="form-control" wire:model.defer="max_value">
        @error('max_value')<small class="text-danger">{{ $message }}</small>@enderror
      </div>
    </div>
    <button class="btn btn-primary" wire:click="save">{{ __('Save') }}</button>
    <small class="text-muted d-block mt-2">{{ __('Used for all countries that do not have a specific Country Limit.') }}</small>
  </div>
</div>
