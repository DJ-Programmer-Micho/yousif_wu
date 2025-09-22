<div class="card">
  <div class="card-header"><strong>{{ __('General Tax Brackets (Fallback)') }}</strong></div>
  <div class="card-body">
    @if (session('success')) <div class="alert alert-success py-2">{{ session('success') }}</div> @endif

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
            <tr wire:key="gbr-{{ $i }}">
              <td>
                <input type="number" step="any" inputmode="decimal"
                       class="form-control form-control-sm"
                       wire:model.lazy="brackets.{{ $i }}.min">
                @error('brackets.'.$i.'.min') <small class="text-danger">{{ $message }}</small> @enderror
              </td>
              <td>
                <input type="number" step="any" inputmode="decimal"
                       class="form-control form-control-sm"
                       wire:model.lazy="brackets.{{ $i }}.max">
                @error('brackets.'.$i.'.max') <small class="text-danger">{{ $message }}</small> @enderror
              </td>
              <td>
                <input type="number" step="any" inputmode="decimal"
                       class="form-control form-control-sm"
                       wire:model.lazy="brackets.{{ $i }}.fee">
                @error('brackets.'.$i.'.fee') <small class="text-danger">{{ $message }}</small> @enderror
              </td>
              <td class="text-right">
                <button type="button" class="btn btn-link text-danger p-0"
                        wire:click="removeBracketRow({{ $i }})">{{ __('Delete') }}</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addBracketRow">{{ __('Add Row') }}</button>

    @error('brackets')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
    <small class="text-muted d-block mt-2">{{ __('Ranges must be ascending and non-overlapping. This general tax is used for countries without a per-country assignment.') }}</small>

    <div class="mt-3">
      <button class="btn btn-primary" wire:click="save">{{ __('Save') }}</button>
    </div>
  </div>
</div>
