{{-- Create Modal --}}
<div id="createLimitModal" class="modal @if($showCreate) show d-block @endif" tabindex="-1" role="dialog" @if($showCreate) style="background:rgba(0,0,0,.5)" @endif>
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header">
      <h6 class="modal-title">{{ __('Add Country Limit') }}</h6>
      <button type="button" class="close" wire:click="$set('showCreate', false)">&times;</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>{{ __('Country') }}</label>
        <div wire:ignore class="form-control">
          <input type="hidden" id="country_id_wire" wire:model="country_id">
          <select id="countrySelect" class="form-control" data-placeholder="{{ __('Choose a country...') }}">
            <option value=""></option>
            @foreach($availableCountries as $c)
              <option value="{{ $c->id }}"
                      data-flag="{{ app('cloudfrontflagsx2').'/'.$c->flag_path }}">
                {{ $c->en_name }}
              </option>
            @endforeach
          </select>
        </div>
        @error('country_id')<small class="text-danger">{{ $message }}</small>@enderror
      </div>

      <div class="form-row">
        <div class="form-group col">
          <label>{{ __('Minimum') }}</label>
          <input type="number" step="0.01" class="form-control" wire:model.defer="min_value">
          @error('min_value')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group col">
          <label>{{ __('Maximum') }}</label>
          <input type="number" step="0.01" class="form-control" wire:model.defer="max_value">
          @error('max_value')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" wire:click="$set('showCreate', false)">{{ __('Cancel') }}</button>
      <button class="btn btn-primary" wire:click="store">{{ __('Save') }}</button>
    </div>
  </div></div>
</div>

{{-- Edit Modal (unchanged) --}}
<div class="modal @if($showEdit) show d-block @endif" tabindex="-1" role="dialog" @if($showEdit) style="background:rgba(0,0,0,.5)" @endif>
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">{{ __('Edit Country Limit') }}</h6>
      <button type="button" class="close" wire:click="$set('showEdit', false)">&times;</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>{{ __('Country') }}</label>
        <input class="form-control" value="{{ optional(\App\Models\Country::find($country_id))->en_name }}" disabled>
      </div>
      <div class="form-row">
        <div class="form-group col">
          <label>{{ __('Minimum') }}</label>
          <input type="number" step="0.01" class="form-control" wire:model.defer="min_value">
          @error('min_value')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group col">
          <label>{{ __('Maximum') }}</label>
          <input type="number" step="0.01" class="form-control" wire:model.defer="max_value">
          @error('max_value')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" wire:click="$set('showEdit', false)">{{ __('Close') }}</button>
      <button class="btn btn-primary" wire:click="update">{{ __('Update') }}</button>
    </div>
  </div></div>
</div>
  
{{-- =================== Delete Limit (confirm) =================== --}}
<div id="deleteLimitModal"
      class="modal @if($showDeleteLimitModal) show d-block @endif"
      tabindex="-1" role="dialog"
      @if($showDeleteLimitModal) style="background:rgba(0,0,0,.5)" @endif>
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h6 class="modal-title">{{ __('Delete Country Limit') }}</h6>
      <button class="close" wire:click="$set('showDeleteLimitModal', false)">&times;</button>
    </div>
    <div class="modal-body">
      <p>{{ __('Delete limit for:') }} <strong>{{ $deleteLimitSummary }}</strong> ?</p>
      <small class="text-muted d-block">{{ __('This action cannot be undone.') }}</small>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" wire:click="$set('showDeleteLimitModal', false)">{{ __('Cancel') }}</button>
      <button class="btn btn-danger" wire:click="deleteLimit">{{ __('Delete') }}</button>
    </div>
  </div></div>
</div>
