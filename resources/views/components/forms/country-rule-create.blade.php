  {{-- Create (Select2 with flags) --}}
  <div id="countryRuleCreateModal" class="modal @if($showCreate) show d-block @endif"
       tabindex="-1" role="dialog" @if($showCreate) style="background:rgba(0,0,0,.5)" @endif>
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">{{ __('Add Country to Not-Allowed List') }}</h6>
        <button class="close" wire:click="$set('showCreate',false)">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>{{ __('Country') }}</label>
          <div wire:ignore class="form-control">
            <input type="hidden" id="cr_country_id_wire" wire:model="country_id">
            <select id="crCountrySelect" class="form-control" data-placeholder="Choose a country...">
              <option value=""></option>
              @foreach($available as $c)
                <option value="{{ $c->id }}"
                        data-flag="{{ app('cloudfrontflagsx2').'/'.$c->flag_path }}"
                        data-iso="{{ $c->iso_code }}"
                        data-ar="{{ $c->ar_name }}"
                        data-ku="{{ $c->ku_name }}">
                  {{ $c->en_name }}
                </option>
              @endforeach
            </select>
          </div>
          @error('country_id')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <small class="text-muted">{{ __('Selected country will be marked as') }} <strong>{{ __('Not allowed to transfer') }}</strong>.</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" wire:click="$set('showCreate',false)">{{ __('Cancel') }}</button>
        <button class="btn btn-primary" wire:click="store">{{ __('Save') }}</button>
      </div>
    </div></div>
  </div>

  {{-- Edit (read-only) --}}
  <div class="modal @if($showEdit) show d-block @endif"
       tabindex="-1" role="dialog" @if($showEdit) style="background:rgba(0,0,0,.5)" @endif>
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">{{ __('View Rule') }}</h6>
        <button class="close" wire:click="$set('showEdit',false)">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>{{ __('Country') }}</label>
          <div class="d-flex align-items-center form-control" style="height:auto;">
            <img src="{{ app('cloudfrontflagsx2').'/'.optional(\App\Models\Country::find($country_id))->flag_path }}" style="height:12px" class="mr-2">
            <span>{{ optional(\App\Models\Country::find($country_id))->en_name }}</span>
          </div>
        </div>
        <div><span class="badge badge-danger">{{ __('Not allowed to transfer') }}</span></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" wire:click="$set('showEdit',false)">{{ __('Close') }}</button>
      </div>
    </div></div>
  </div>
    {{-- =============== Delete (confirm) =============== --}}
  <div id="countryRuleDeleteModal"
      class="modal @if($showDelete) show d-block @endif"
      tabindex="-1" role="dialog"
      @if($showDelete) style="background:rgba(0,0,0,.5)" @endif>
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">{{ __('Remove Country') }}</h6>
        <button class="close" wire:click="$set('showDelete',false)">&times;</button>
      </div>
      <div class="modal-body">
        <p>{{ __('Remove') }} <strong>{{ $deleteName }}</strong> {{ __('from the') }} <em>{{ __('Not-Allowed to transfer') }}</em> {{ __('list?') }}</p>
        <small class="text-muted d-block">{{ __('This will allow transfers for this country again.') }}</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" wire:click="$set('showDelete',false)">{{ __('Cancel') }}</button>
        <button class="btn btn-danger" wire:click="delete">{{ __('Delete') }}</button>
      </div>
    </div></div>
  </div>