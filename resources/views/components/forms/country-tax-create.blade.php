  {{-- =================== Create Set (rows) =================== --}}
  <div class="modal @if($showSetModal) show d-block @endif" tabindex="-1" role="dialog" @if($showSetModal) style="background:rgba(0,0,0,.5)" @endif>
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">{{ __('New Tax Bracket Set') }}</h6>
        <button class="close" wire:click="$set('showSetModal',false)">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Name</label>
          <input class="form-control" wire:model.defer="set_name">
          @error('set_name')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <div class="table-responsive">
          <table class="table table-sm">
            <thead class="thead-light">
              <tr>
                <th style="width:25%;">{{ __('Min') }}</th>
                <th style="width:25%;">{{ __('Max') }}</th>
                <th style="width:25%;">{{ __('Fee') }}</th>
                <th style="width:25%;"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($brackets as $i => $r)
                <tr wire:key="br-{{ $i }}">
                  <td>
                    <input type="number" step="any" inputmode="decimal" class="form-control form-control-sm" wire:model.lazy="brackets.{{ $i }}.min">
                    @error('brackets.'.$i.'.min') <small class="text-danger">{{ $message }}</small> @enderror
                  </td>
                  <td>
                    <input type="number" step="any" inputmode="decimal" class="form-control form-control-sm" wire:model.lazy="brackets.{{ $i }}.max">
                    @error('brackets.'.$i.'.max') <small class="text-danger">{{ $message }}</small> @enderror
                  </td>
                  <td>
                    <input type="number" step="any" inputmode="decimal" class="form-control form-control-sm" wire:model.lazy="brackets.{{ $i }}.fee">
                    @error('brackets.'.$i.'.fee') <small class="text-danger">{{ $message }}</small> @enderror
                  </td>
                  <td class="text-right">
                    <button type="button" class="btn btn-link text-danger p-0" wire:click="removeBracketRow({{ $i }})">{{ __('Delete') }}</button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addBracketRow">{{ __('Add Row') }}</button>
        <small class="text-muted d-block mt-2">{{ __('Ranges must be ascending and non-overlapping.') }}</small>
        @error('brackets')<small class="text-danger d-block">{{ $message }}</small>@enderror
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" wire:click="$set('showSetModal',false)">{{ __('Cancel') }}</button>
        <button class="btn btn-primary" wire:click="storeSet">{{ __('Save') }}</button>
      </div>
    </div></div>
  </div>

  {{-- =================== Edit Set (rows) =================== --}}
  <div class="modal @if($showSetEditModal) show d-block @endif" tabindex="-1" role="dialog" @if($showSetEditModal) style="background:rgba(0,0,0,.5)" @endif>
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">{{ __('Edit Tax Bracket Set') }}</h6>
        <button class="close" wire:click="$set('showSetEditModal',false)">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>{{ __('Name') }}</label>
          <input class="form-control" wire:model.defer="set_name">
          @error('set_name')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <div class="table-responsive">
          <table class="table table-sm">
            <thead class="thead-light">
              <tr>
                <th style="width:25%;">{{ __('Min') }}</th>
                <th style="width:25%;">{{ __('Max') }}</th>
                <th style="width:25%;">{{ __('Fee') }}</th>
                <th style="width:25%;"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($brackets as $i => $r)
                <tr wire:key="br-edit-{{ $i }}">
                  <td>
                    <input type="number" step="any" inputmode="decimal" class="form-control form-control-sm" wire:model.lazy="brackets.{{ $i }}.min">
                    @error('brackets.'.$i.'.min') <small class="text-danger">{{ $message }}</small> @enderror
                  </td>
                  <td>
                    <input type="number" step="any" inputmode="decimal" class="form-control form-control-sm" wire:model.lazy="brackets.{{ $i }}.max">
                    @error('brackets.'.$i.'.max') <small class="text-danger">{{ $message }}</small> @enderror
                  </td>
                  <td>
                    <input type="number" step="any" inputmode="decimal" class="form-control form-control-sm" wire:model.lazy="brackets.{{ $i }}.fee">
                    @error('brackets.'.$i.'.fee') <small class="text-danger">{{ $message }}</small> @enderror
                  </td>
                  <td class="text-right">
                    <button type="button" class="btn btn-link text-danger p-0" wire:click="removeBracketRow({{ $i }})">{{ __('Delete') }}</button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addBracketRow">{{ __('Add Row') }}</button>
        <small class="text-muted d-block mt-2">{{ __('Ranges must be ascending and non-overlapping.') }}</small>
        @error('brackets')<small class="text-danger d-block">{{ $message }}</small>@enderror
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" wire:click="$set('showSetEditModal',false)">{{ __('Close') }}</button>
        <button class="btn btn-primary" wire:click="updateSet">{{ __('Update') }}</button>
      </div>
    </div></div>
  </div>
  
{{-- =================== Assign (create) =================== --}}
<div id="assignTaxModal" class="modal @if($showAssignModal) show d-block @endif" tabindex="-1" role="dialog" @if($showAssignModal) style="background:rgba(0,0,0,.5)" @endif>
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">{{ __('Assign Set to Country') }}</h6><button class="close" wire:click="$set('showAssignModal',false)">&times;</button></div>
    <div class="modal-body">
      <div class="form-group">
        <label>{{ __('Country') }}</label>
        <div wire:ignore class="form-control">
          <input type="hidden" id="ct_country_id_wire" wire:model="country_id">
          <select id="ctAssignCountry" class="form-control" data-placeholder="Choose a country...">
            <option value=""></option>
            @foreach($availableCountries as $c)
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

      <div class="form-group">
        <label>Tax Set</label>
        <div wire:ignore class="form-control">
          <input type="hidden" id="ct_tax_set_id_wire" wire:model="tax_bracket_set_id">
          <select id="ctAssignSet" class="form-control" data-placeholder="Choose a set...">
            <option value=""></option>
            @foreach($sets as $s)
              <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        @error('tax_bracket_set_id')<small class="text-danger">{{ $message }}</small>@enderror
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" wire:click="$set('showAssignModal',false)">{{ __('Cancel') }}</button>
      <button class="btn btn-primary" wire:click="storeAssign">{{ __('Save') }}</button>
    </div>
  </div></div>
</div>

{{-- =================== Edit Assignment =================== --}}
<div id="assignTaxEditModal" class="modal @if($showAssignEditModal) show d-block @endif" tabindex="-1" role="dialog" @if($showAssignEditModal) style="background:rgba(0,0,0,.5)" @endif>
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">{{ __('Edit Assignment') }}</h6><button class="close" wire:click="$set('showAssignEditModal',false)">&times;</button></div>
    <div class="modal-body">
      <div class="form-group">
        <label>{{ __('Country') }}</label>
        <div class="d-flex align-items-center form-control" style="height:auto;">
          <img src="{{ app('cloudfrontflagsx2').'/'.optional(\App\Models\Country::find($country_id))->flag_path }}" style="height:12px" class="mr-2">
          <span>{{ optional(\App\Models\Country::find($country_id))->en_name }}</span>
        </div>
      </div>

      <div class="form-group">
        <label>{{ __('Tax Set') }}</label>
        <div wire:ignore class="form-control">
          <input type="hidden" id="ct_tax_set_id_wire_edit" wire:model="tax_bracket_set_id">
          <select id="ctEditSet" class="form-control" data-placeholder="Choose a set...">
            <option value=""></option>
            @foreach($sets as $s)
              <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        @error('tax_bracket_set_id')<small class="text-danger">{{ $message }}</small>@enderror
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" wire:click="$set('showAssignEditModal',false)">{{ __('Close') }}</button>
      <button class="btn btn-primary" wire:click="updateAssign">{{ __('Update') }}</button>
    </div>
  </div></div>
</div>

{{-- =================== Delete Set (confirm) =================== --}}
<div id="deleteTaxSetModal"
     class="modal @if($showDeleteSetModal) show d-block @endif"
     tabindex="-1" role="dialog"
     @if($showDeleteSetModal) style="background:rgba(0,0,0,.5)" @endif>
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h6 class="modal-title">{{ __('Delete Tax Bracket Set') }}</h6>
      <button class="close" wire:click="$set('showDeleteSetModal', false)">&times;</button>
    </div>
    <div class="modal-body">
      <p>{{ __('Are you sure you want to delete the set') }} <strong>{{ $deleteSetName }}</strong>?</p>
      <small class="text-muted d-block">{{ __('This action cannot be undone.') }}</small>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" wire:click="$set('showDeleteSetModal', false)">{{ __('Cancel') }}</button>
      <button class="btn btn-danger" wire:click="deleteSet">{{ __('Delete') }}</button>
    </div>
  </div></div>
</div>

{{-- =================== Delete Assignment (confirm) =================== --}}
<div id="deleteTaxAssignModal"
     class="modal @if($showDeleteAssignModal) show d-block @endif"
     tabindex="-1" role="dialog"
     @if($showDeleteAssignModal) style="background:rgba(0,0,0,.5)" @endif>
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h6 class="modal-title">{{ __('Delete Assignment') }}</h6>
      <button class="close" wire:click="$set('showDeleteAssignModal', false)">&times;</button>
    </div>
    <div class="modal-body">
      <p>{{ __('Delete this assignment:') }} <strong>{{ $deleteAssignSummary }}</strong> ?</p>
      <small class="text-muted d-block">{{ __('This only removes the country’s link to the set.') }}</small>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" wire:click="$set('showDeleteAssignModal', false)">{{ __('Cancel') }}</button>
      <button class="btn btn-danger" wire:click="deleteAssign">{{ __('Delete') }}</button>
    </div>
  </div></div>
</div>
