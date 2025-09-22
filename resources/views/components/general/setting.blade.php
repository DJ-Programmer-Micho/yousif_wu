<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <strong>{{ __('Receivers Access Control') }}</strong>
    <div class="small text-muted">
      {{ __('Registers:') }} <b>{{ $totalRegs }}</b> ·
      {{ __('Disabled:') }} <b>{{ $disabledRegs }}</b>
      @if($disableAll)
        <span class="badge badge-danger ml-2">{{ __('Global: ALL disabled') }}</span>
      @endif
    </div>
  </div>

  <div class="card-body">
    {{-- Global toggle --}}
    <div class="custom-control custom-switch mb-3">
      <input type="checkbox" class="custom-control-input" id="switchDisableAll" wire:model="disableAll">
      <label class="custom-control-label" for="switchDisableAll">
        <b>{{ __('Disable Receivers for ALL registers') }}</b>
      </label>
    </div>
    <p class="text-muted mb-4" style="margin-top:-8px;">
      {{ __("When enabled, all receiver pages and actions are blocked for every register, regardless of the per-register settings below.") }}
    </p>

    {{-- Per-register table --}}
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead class="thead-light">
          <tr>
            <th style="width:70px">#</th>
            <th>{{ __('Register') }}</th>
            <th style="width:160px" class="text-center">{{ __('Status') }}</th>
            <th style="width:180px" class="text-center">{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $idx => $r)
            <tr @class(['text-muted' => $disableAll])>
              <td>{{ $idx+1 }}</td>
              <td class="text-nowrap">
                <i class="fas fa-user mr-2 text-secondary"></i>{{ $r['name'] }}
              </td>
              <td class="text-center">
                @if($disableAll || $r['disabled'])
                  <span class="badge badge-danger">{{ __('Disabled') }}</span>
                @else
                  <span class="badge badge-success">{{ __('Enabled') }}</span>
                @endif
              </td>
              <td class="text-center">
                <button
                  class="btn btn-sm {{ $r['disabled'] ? 'btn-success' : 'btn-outline-danger' }}"
                  wire:click="toggleRegister({{ $r['id'] }})"
                  @if($disableAll) disabled @endif
                >
                  @if($r['disabled'])
                    <i class="fas fa-unlock mr-1"></i> {{ __('Enable') }}
                  @else
                    <i class="fas fa-ban mr-1"></i> {{ __('Disable') }}
                  @endif
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">{{ __('No registers found') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card-footer bg-white d-flex justify-content-between">
    <div>
      <button class="btn btn-outline-secondary btn-sm" wire:click="enableAllRegisters" @if($disableAll) disabled @endif>
        <i class="fas fa-toggle-on mr-1"></i> {{ __('Enable all (per-register)') }}
      </button>
    </div>
    <button wire:click="save" class="btn btn-primary">
      <i class="fas fa-save mr-1"></i> {{ __('Save') }}
    </button>
  </div>
</div>
