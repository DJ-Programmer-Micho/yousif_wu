@include('components.forms._country-general-ui')

<div class="card country-admin-shell">
  <div class="country-admin-header is-success">
    <div>
      <span class="country-admin-eyebrow">{{ __('General Tax') }}</span>
      <h5 class="country-admin-title">{{ __('Fallback Tax Brackets') }}</h5>
      <p class="country-admin-subtitle">{{ __('Maintain the default fee ladder used when a country does not have its own assigned bracket set.') }}</p>
    </div>
    <div class="country-admin-badge-card">
      <span>{{ __('Rows') }}</span>
      <strong>{{ count($brackets) }}</strong>
    </div>
  </div>

  <div class="country-admin-body">
    @if (session('success'))
      <div class="alert alert-success country-admin-alert">{{ session('success') }}</div>
    @endif

    <div class="country-admin-table-wrap">
      <div class="table-responsive">
        <table class="table country-admin-table country-admin-mini-table">
          <thead>
            <tr>
              <th style="width:28%;">{{ __('Min') }}</th>
              <th style="width:28%;">{{ __('Max') }}</th>
              <th style="width:28%;">{{ __('Fee') }}</th>
              <th class="text-right" style="width:16%;">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($brackets as $i => $r)
              <tr wire:key="gbr-{{ $i }}">
                <td>
                  <input
                    type="number"
                    step="any"
                    inputmode="decimal"
                    class="form-control form-control-sm country-admin-input"
                    wire:model.lazy="brackets.{{ $i }}.min"
                  >
                  @error('brackets.'.$i.'.min') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
                </td>
                <td>
                  <input
                    type="number"
                    step="any"
                    inputmode="decimal"
                    class="form-control form-control-sm country-admin-input"
                    wire:model.lazy="brackets.{{ $i }}.max"
                  >
                  @error('brackets.'.$i.'.max') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
                </td>
                <td>
                  <input
                    type="number"
                    step="any"
                    inputmode="decimal"
                    class="form-control form-control-sm country-admin-input"
                    wire:model.lazy="brackets.{{ $i }}.fee"
                  >
                  @error('brackets.'.$i.'.fee') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
                </td>
                <td class="text-right">
                  <button type="button" class="btn btn-link text-danger p-0 font-weight-bold" wire:click="removeBracketRow({{ $i }})">
                    {{ __('Delete') }}
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="d-flex align-items-center flex-wrap mt-3" style="gap:10px;">
      <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addBracketRow">{{ __('Add Row') }}</button>
      <button class="btn btn-primary btn-sm" wire:click="save">{{ __('Save') }}</button>
    </div>

    @error('brackets')<small class="text-danger d-block mt-3">{{ $message }}</small>@enderror

    <div class="country-admin-note">
      {{ __('Ranges must be ascending and non-overlapping. This general tax is used for countries without a per-country assignment.') }}
    </div>
  </div>
</div>
