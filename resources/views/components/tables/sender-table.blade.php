{{-- resources/views/livewire/balance/sender-balance-livewire.blade.php --}}
<div>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ __('Registers (Sender)') }}</h5>
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
            <th class="text-right">{{ __('Balance (USD)') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
        @forelse($registers as $i => $u)
          @php
            $incoming = (float) ($u->incoming_sum ?? 0);
            $outgoing = (float) ($u->outgoing_sum ?? 0);
            $remaining = $incoming - $outgoing;
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
            <td class="text-right"><b>$ {{ number_format($remaining, 2) }}</b></td>
            <td>
              <button class="btn btn-sm btn-outline-primary"
                      wire:click="openDetails({{ $u->id }})">
                {{ __('Details') }}
              </button>
              
              @if(auth()->user()->role == 1)
              <button class="btn btn-sm btn-success"
                      wire:click="openTopUp({{ $u->id }})">
                {{ __('Top Up') }}
              </button>

              <button class="btn btn-sm btn-danger"
                      @if($remaining <= 0) disabled @endif
                      wire:click="openDeduct({{ $u->id }})">
                {{ __('Deduct / Transfer Back') }}
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
  <div class="modal fade" id="senderDetailsModal" tabindex="-1" aria-labelledby="senderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content" wire:ignore.self>
        <div class="modal-header">
          <h5 class="modal-title">
            {{ __('Sender Balance Details') }}
            @if($selectedUserName) — <span class="text-muted">{{ $selectedUserName }}</span>@endif
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="closeModal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @if($selectedUserId)
            @livewire('balance.sender-balance-details-livewire', ['userId' => $selectedUserId], key('s-details-'.$selectedUserId))
          @else
            <div class="text-muted">{{ __('Select a register to view details.') }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Topup Modal --}}
  <div class="modal fade" id="senderTopUpModal" tabindex="-1" aria-labelledby="senderTopUpModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            {{ __('Top Up Sender Balance') }}
            @if($topUpUserName) — <span class="text-muted">{{ $topUpUserName }}</span>@endif
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#senderTopUpModal').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          @if($topUpUserId)
            <div class="form-group">
              <label>{{ __('Amount (USD)') }}</label>
              <input type="number" step="0.01" min="0.01" class="form-control" wire:model.lazy="topUpAmount">
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
          <button type="button" class="btn btn-light" onclick="$('#senderTopUpModal').modal('hide')">{{ __('Cancel') }}</button>
          <button type="button" class="btn btn-primary" wire:click="saveTopUp" @if(!$topUpUserId) disabled @endif>
            {{ __('Save Top Up') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Deduct / Transfer Back Modal --}}
  <div class="modal fade" id="senderDeductModal" tabindex="-1" aria-labelledby="senderDeductModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            {{ __('Deduct / Transfer Back (USD)') }}
            @if($deductUserName) — <span class="text-muted">{{ $deductUserName }}</span>@endif
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#senderDeductModal').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          @if($deductUserId)
            <div class="form-group">
              <label>{{ __('Amount (USD)') }}</label>
              <input type="number" step="0.01" min="0.01" class="form-control" wire:model.lazy="deductAmount">
              @error('deductAmount')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
              <label>{{ __('Note') }}</label>
              <input type="text" class="form-control" wire:model.lazy="deductNote" placeholder="{{ __('Optional, e.g., Transfer back to company') }}">
              @error('deductNote')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
              <label>{{ __('Link to Sender transfer (optional)') }}</label>
              <input type="number" class="form-control" wire:model.lazy="deductSenderId" placeholder="{{ __('Sender ID (optional)') }}">
              @error('deductSenderId')<small class="text-danger">{{ $message }}</small>@enderror
              <small class="text-muted">{{ __('If you want to tie this deduction to a specific Sender (transfer), put its ID here.') }}</small>
            </div>
          @else
            <div class="text-muted">{{ __('Select a register first.') }}</div>
          @endif
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" onclick="$('#senderDeductModal').modal('hide')">{{ __('Cancel') }}</button>
          <button type="button" class="btn btn-danger" wire:click="saveDeduct" @if(!$deductUserId) disabled @endif>
            {{ __('Save Deduction') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- JS hooks to open/close modal --}}
@once
  @push('scripts')
  <script>
    window.addEventListener('open-sender-details-modal', () => $('#senderDetailsModal').modal('show'));
    window.addEventListener('close-sender-details-modal', () => $('#senderDetailsModal').modal('hide'));

    window.addEventListener('open-sender-topup-modal', () => $('#senderTopUpModal').modal('show'));
    window.addEventListener('close-sender-topup-modal', () => $('#senderTopUpModal').modal('hide'));

    window.addEventListener('open-sender-deduct-modal', () => $('#senderDeductModal').modal('show'));
    window.addEventListener('close-sender-deduct-modal', () => $('#senderDeductModal').modal('hide'));

    window.addEventListener('toast', e => {
      if (!e.detail?.message) return; alert(e.detail.message);
    });
  </script>
  @endpush
  @endonce
</div>
