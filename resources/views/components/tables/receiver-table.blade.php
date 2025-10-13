{{-- resources/views/components/tables/receiver-table.blade.php --}}
<div>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ __('Registers (Receiver)') }}</h5>
      <div class="form-inline">
        <input type="text" class="form-control form-control-sm mr-2" wire:model.debounce.500ms="q" placeholder="{{ __('Search name/email') }}">
        <select class="form-control form-control-sm" wire:model="perPage">
          <option>10</option><option>25</option><option>50</option>
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead class="thead-light">
          <tr>
            <th>#</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-right">{{ __('Running (IQD)') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
        @forelse($registers as $i => $u)
          @php
            $incoming = (int) ($u->incoming_sum ?? 0);
            $outgoing = (int) ($u->outgoing_sum ?? 0);
            $running  = $incoming - $outgoing;
          @endphp
          <tr>
            <td>{{ $registers->firstItem() + $i }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>
              <span class="badge badge-{{ (int)$u->status === 1 ? 'success' : 'secondary' }}">
                {{ (int)$u->status === 1 ? __('Active') : __('Inactive') }}
              </span>
            </td>
            <td class="text-right"><b>{{ number_format($running) }}</b></td>
            <td class="d-flex align-items-center">
              <button class="btn btn-sm btn-outline-primary mr-2"
                      wire:click="openDetails({{ $u->id }})">
                {{ __('Details') }}
              </button>
              @if($isAdmin)
              <button class="btn btn-sm btn-success mr-2"
                      wire:click="openTopUp({{ $u->id }})">
                {{ __('Top Up') }}
              </button>
              
              <button class="btn btn-sm btn-danger"
                      wire:click="openDeduct({{ $u->id }})">
                {{ __('Deduct') }}
              </button>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted p-4">{{ __('No registers found.') }}</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer">
      {{ $registers->links() }}
    </div>
  </div>

  {{-- Details Modal --}}
  <div class="modal fade" id="receiverDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Receiver Balance Details') }} @if($selectedUserName) — <span class="text-muted">{{ $selectedUserName }}</span>@endif</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="closeModal"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        @if($selectedUserId)
          @livewire('balance.receiver-balance-details-livewire', ['userId' => $selectedUserId], key('r-details-'.$selectedUserId))
        @else
          <div class="text-muted">{{ __('Select a register to view details.') }}</div>
        @endif
      </div>
    </div></div>
  </div>

  {{-- Deduct Modal --}}
  <div class="modal fade" id="receiverDeductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Deduct from Receiver Balance') }} @if($deductUserName) — <span class="text-muted">{{ $deductUserName }}</span>@endif</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#receiverDeductModal').modal('hide')"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        @if($deductUserId)
          <div class="form-group">
            <label>{{ __('Amount (IQD)') }}</label>
            <input type="number" step="1" min="1" class="form-control" wire:model.lazy="deductAmount">
            @error('deductAmount')<small class="text-danger">{{ $message }}</small>@enderror
          </div>
          <div class="form-group">
            <label>{{ __('Note') }}</label>
            <input type="text" class="form-control" wire:model.lazy="deductNote" placeholder="{{ __('Optional') }}">
            @error('deductNote')<small class="text-danger">{{ $message }}</small>@enderror
          </div>
        @else
          <div class="text-muted">{{ __('Select a register to deduct.') }}</div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" onclick="$('#receiverDeductModal').modal('hide')">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-danger" wire:click="saveDeduct" @if(!$deductUserId) disabled @endif>{{ __('Save Deduction') }}</button>
      </div>
    </div></div>
  </div>


    {{-- MODAL --}}
    {{-- Top-Up Modal --}}
    <div class="modal fade" id="receiverTopUpModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
      <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            {{ __('Top Up Receiver Balance') }}
            @if($topUpUserName) — <span class="text-muted">{{ $topUpUserName }}</span>@endif
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#receiverTopUpModal').modal('hide')"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
          @if($topUpUserId)
            <div class="form-group">
              <label>{{ __('Amount (IQD)') }}</label>
              <input type="number" step="1" min="1" class="form-control" wire:model.lazy="topUpAmount">
              @error('topUpAmount')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
              <label>{{ __('Note') }}</label>
              <input type="text" class="form-control" wire:model.lazy="topUpNote" placeholder="{{ __('Optional') }}">
              @error('topUpNote')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
          @else
            <div class="text-muted">{{ __('Select a register to top up.') }}</div>
          @endif
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" onclick="$('#receiverTopUpModal').modal('hide')">{{ __('Cancel') }}</button>
          <button type="button" class="btn btn-primary" wire:click="saveTopUp" @if(!$topUpUserId) disabled @endif>
            {{ __('Save Top Up') }}
          </button>
        </div>
      </div></div>
    </div>



  @once
  @push('scripts')
  <script>
    window.addEventListener('open-receiver-details-modal', () => $('#receiverDetailsModal').modal('show'));
    window.addEventListener('close-receiver-details-modal', () => $('#receiverDetailsModal').modal('hide'));
    window.addEventListener('open-receiver-deduct-modal', () => $('#receiverDeductModal').modal('show'));
    window.addEventListener('close-receiver-deduct-modal', () => $('#receiverDeductModal').modal('hide'));
    window.addEventListener('open-receiver-topup-modal', () => $('#receiverTopUpModal').modal('show'));
    window.addEventListener('close-receiver-topup-modal', () => $('#receiverTopUpModal').modal('hide'));
    window.addEventListener('toast', e => { if (!e.detail?.message) return; alert(e.detail.message); });
  </script>
  @endpush
  @endonce
</div>
