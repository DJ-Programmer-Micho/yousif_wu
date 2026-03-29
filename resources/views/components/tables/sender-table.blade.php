@php
  $isAdmin = (int) auth()->user()->role === 1;
@endphp

@once
  @push('css')
    <style>
      .ledger-ui {
        --ledger-accent: #4f46e5;
        --ledger-accent-strong: #2563eb;
        --ledger-accent-soft: rgba(79, 70, 229, 0.12);
        --ledger-accent-soft-2: rgba(14, 165, 233, 0.12);
        --ledger-border: rgba(226, 232, 240, 0.95);
        --ledger-text: #0f172a;
        --ledger-muted: #64748b;
        --ledger-surface: rgba(255, 255, 255, 0.98);
      }

      .ledger-ui__card {
        border: 0;
        border-radius: 30px;
        overflow: hidden;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.96));
        box-shadow: 0 28px 70px rgba(15, 23, 42, 0.08);
      }

      .ledger-ui__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.2rem;
        padding: 1.6rem 1.6rem 1rem;
        background:
          radial-gradient(circle at top right, var(--ledger-accent-soft), transparent 30%),
          radial-gradient(circle at left center, var(--ledger-accent-soft-2), transparent 34%),
          linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.94));
      }

      .ledger-ui__copy h5 {
        margin: 0;
        color: var(--ledger-text);
        font-size: 1.35rem;
        font-weight: 900;
      }

      .ledger-ui__copy p {
        margin: 0.45rem 0 0;
        color: var(--ledger-muted);
        line-height: 1.7;
      }

      .ledger-ui__toolbar {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        flex-wrap: wrap;
        justify-content: flex-end;
      }

      .ledger-ui__search {
        position: relative;
        min-width: 260px;
      }

      .ledger-ui__search i {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #94a3b8;
      }

      .ledger-ui__search input,
      .ledger-ui__select select,
      .ledger-ui__field input {
        min-height: 50px;
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.24);
        background: rgba(255,255,255,0.95);
        box-shadow: none;
      }

      .ledger-ui__search input {
        padding-left: 42px;
      }

      .ledger-ui__search input:focus,
      .ledger-ui__select select:focus,
      .ledger-ui__field input:focus {
        border-color: rgba(79, 70, 229, 0.35);
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.12);
      }

      .ledger-ui__select {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.45rem 0.6rem 0.45rem 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        background: rgba(255,255,255,0.85);
      }

      .ledger-ui__select span {
        color: var(--ledger-muted);
        font-size: 0.85rem;
        font-weight: 700;
      }

      .ledger-ui__select select {
        min-height: 42px;
        min-width: 88px;
      }

      .ledger-ui__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        padding: 0 1.6rem 1.2rem;
      }

      .ledger-ui__pill {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: rgba(255,255,255,0.82);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.04);
      }

      .ledger-ui__pill-label {
        display: block;
        color: var(--ledger-muted);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
      }

      .ledger-ui__pill strong {
        color: var(--ledger-text);
        font-size: 1rem;
        font-weight: 900;
      }

      .ledger-ui__table-wrap {
        padding: 0 1.15rem 1rem;
      }

      .ledger-ui__table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
      }

      .ledger-ui__table thead th {
        padding: 0.8rem 1rem 0.35rem;
        border: 0;
        color: var(--ledger-muted);
        font-size: 0.77rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
      }

      .ledger-ui__table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid var(--ledger-border);
        border-bottom: 1px solid var(--ledger-border);
        background: var(--ledger-surface);
      }

      .ledger-ui__table tbody tr td:first-child {
        border-left: 1px solid var(--ledger-border);
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
      }

      .ledger-ui__table tbody tr td:last-child {
        border-right: 1px solid var(--ledger-border);
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
      }

      .ledger-ui__index {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--ledger-accent-soft), var(--ledger-accent-soft-2));
        color: var(--ledger-accent);
        font-weight: 900;
      }

      .ledger-ui__user {
        display: flex;
        align-items: center;
        gap: 0.8rem;
      }

      .ledger-ui__avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--ledger-accent-soft), var(--ledger-accent-soft-2));
        color: var(--ledger-accent);
        font-size: 1.05rem;
        font-weight: 900;
        line-height: 1;
        flex: 0 0 auto;
      }

      .ledger-ui__user strong {
        display: block;
        color: var(--ledger-text);
        font-size: 0.95rem;
      }

      .ledger-ui__subtext {
        display: block;
        margin-top: 0.2rem;
        color: var(--ledger-muted);
        font-size: 0.8rem;
      }

      .ledger-ui__email {
        color: var(--ledger-muted);
        font-size: 0.9rem;
        word-break: break-word;
      }

      .ledger-ui__status {
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 800;
      }

      .ledger-ui__status.is-active {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
      }

      .ledger-ui__status.is-inactive {
        background: rgba(148, 163, 184, 0.16);
        color: #475569;
      }

      .ledger-ui__amount {
        text-align: right;
      }

      .ledger-ui__amount strong {
        display: block;
        color: var(--ledger-text);
        font-size: 1.15rem;
        font-weight: 900;
      }

      .ledger-ui__amount span {
        display: block;
        margin-top: 0.25rem;
        color: var(--ledger-muted);
        font-size: 0.78rem;
      }

      .ledger-ui__actions {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        flex-wrap: wrap;
      }

      .ledger-ui__action {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        min-height: 42px;
        padding: 0.62rem 0.95rem;
        border: 0;
        border-radius: 14px;
        font-size: 0.8rem;
        font-weight: 800;
      }

      .ledger-ui__action i {
        font-size: 0.82rem;
      }

      .ledger-ui__action--neutral {
        background: rgba(79, 70, 229, 0.1);
        color: #4338ca;
      }

      .ledger-ui__action--success {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
      }

      .ledger-ui__action--danger {
        background: rgba(239, 68, 68, 0.12);
        color: #b91c1c;
      }

      .ledger-ui__empty {
        padding: 3rem 1.5rem !important;
        text-align: center;
        color: var(--ledger-muted);
      }

      .ledger-ui__footer {
        padding: 0 1.6rem 1.45rem;
      }

      .ledger-ui__footer .pagination {
        justify-content: flex-end;
        margin-bottom: 0;
      }

      .ledger-modal .modal-dialog {
        max-width: 560px;
      }

      .ledger-modal .modal-dialog.modal-xl {
        max-width: 1180px;
      }

      .ledger-modal .modal-content {
        border: 0;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 28px 80px rgba(15, 23, 42, 0.18);
      }

      .ledger-modal .modal-header {
        align-items: flex-start;
        padding: 1.25rem 1.35rem;
        border: 0;
        background:
          radial-gradient(circle at top right, rgba(14, 165, 233, 0.22), transparent 30%),
          linear-gradient(135deg, #312e81, #2563eb);
        color: #fff;
      }

      .ledger-modal .modal-title {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        font-size: 1.1rem;
        font-weight: 900;
      }

      .ledger-modal__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
      }

      .ledger-modal__meta {
        display: block;
        margin-top: 0.2rem;
        color: rgba(255,255,255,0.72);
        font-size: 0.82rem;
        font-weight: 600;
      }

      .ledger-modal .close {
        color: #fff;
        opacity: 1;
        text-shadow: none;
      }

      .ledger-modal .modal-body {
        padding: 1.35rem;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.95));
      }

      .ledger-modal .modal-footer {
        border: 0;
        padding: 1rem 1.35rem 1.35rem;
        background: rgba(248,250,252,0.95);
      }

      .ledger-modal__placeholder,
      .ledger-modal__panel {
        padding: 1rem 1.05rem;
        border-radius: 20px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: rgba(255,255,255,0.88);
      }

      .ledger-modal__intro {
        margin-bottom: 1rem;
        padding: 1rem 1.05rem;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--ledger-accent-soft), var(--ledger-accent-soft-2));
        color: #312e81;
      }

      .ledger-modal__intro strong {
        display: block;
        color: var(--ledger-text);
        font-size: 0.98rem;
      }

      .ledger-modal__intro span {
        display: block;
        margin-top: 0.3rem;
        color: #475569;
        line-height: 1.65;
      }

      .ledger-ui__field + .ledger-ui__field {
        margin-top: 1rem;
      }

      .ledger-ui__field label {
        display: block;
        margin-bottom: 0.5rem;
        color: #334155;
        font-weight: 700;
      }

      .ledger-ui__field small {
        display: block;
        margin-top: 0.45rem;
        color: var(--ledger-muted);
        font-size: 0.8rem;
      }

      .ledger-ui__modal-actions {
        display: flex;
        gap: 0.7rem;
        margin-left: auto;
      }

      .ledger-ui__modal-btn {
        min-width: 140px;
        min-height: 48px;
        border: 0;
        border-radius: 16px;
        font-weight: 800;
      }

      .ledger-ui__modal-btn--light {
        background: rgba(241, 245, 249, 0.95);
        color: #334155;
      }

      .ledger-ui__modal-btn--primary {
        background: linear-gradient(135deg, #4f46e5, #2563eb);
        color: #fff;
      }

      .ledger-ui__modal-btn--danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
      }

      @media (max-width: 991.98px) {
        .ledger-ui__header {
          flex-direction: column;
        }

        .ledger-ui__toolbar {
          width: 100%;
          justify-content: flex-start;
        }
      }

      @media (max-width: 767.98px) {
        .ledger-ui__header,
        .ledger-ui__meta,
        .ledger-ui__footer,
        .ledger-ui__table-wrap,
        .ledger-modal .modal-header,
        .ledger-modal .modal-body,
        .ledger-modal .modal-footer {
          padding-left: 1rem;
          padding-right: 1rem;
        }

        .ledger-ui__search {
          min-width: 100%;
        }

        .ledger-ui__table {
          min-width: 760px;
        }

        .ledger-ui__modal-actions {
          width: 100%;
          flex-direction: column-reverse;
          margin-left: 0;
        }

        .ledger-ui__modal-btn {
          width: 100%;
        }
      }
    </style>
  @endpush
@endonce

<div class="ledger-ui ledger-ui--sender">
  <div class="card ledger-ui__card">
    <div class="ledger-ui__header">
      <div class="ledger-ui__copy">
        <h5>{{ __('Sender Register Balances') }}</h5>
        <p>{{ __('Search registers, review current USD position, and open balance actions or ledger details without leaving the table.') }}</p>
      </div>

      <div class="ledger-ui__toolbar">
        <label class="ledger-ui__search mb-0">
          <i class="fas fa-search"></i>
          <input type="text" class="form-control" wire:model.debounce.500ms="q" placeholder="{{ __('Search name, email, password') }}">
        </label>

        <label class="ledger-ui__select mb-0">
          <span>{{ __('Rows') }}</span>
          <select class="form-control" wire:model="perPage">
            <option>10</option>
            <option>25</option>
            <option>50</option>
          </select>
        </label>
      </div>
    </div>

    <div class="ledger-ui__meta">
      <div class="ledger-ui__pill">
        <div>
          <span class="ledger-ui__pill-label">{{ __('Total Registers') }}</span>
          <strong>{{ $registers->total() }}</strong>
        </div>
      </div>
      <div class="ledger-ui__pill">
        <div>
          <span class="ledger-ui__pill-label">{{ __('Showing') }}</span>
          <strong>{{ $registers->count() ? $registers->firstItem().' - '.$registers->lastItem() : '0' }}</strong>
        </div>
      </div>
      <div class="ledger-ui__pill">
        <div>
          <span class="ledger-ui__pill-label">{{ __('Workspace') }}</span>
          <strong>{{ __('USD Ledger Control') }}</strong>
        </div>
      </div>
      @if($isAdmin)
        <div class="ledger-ui__pill">
          <div>
            <span class="ledger-ui__pill-label">{{ __('Admin Tools') }}</span>
            <strong>{{ __('Top Up & Transfer Back Enabled') }}</strong>
          </div>
        </div>
      @endif
    </div>

    <div class="table-responsive ledger-ui__table-wrap">
      <table class="table ledger-ui__table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('Register') }}</th>
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
              $displayName = trim((string) $u->name);
              $initial = $displayName !== '' ? (function_exists('mb_substr') ? mb_substr($displayName, 0, 1, 'UTF-8') : substr($displayName, 0, 1)) : 'R';
              $initials = function_exists('mb_strtoupper') ? mb_strtoupper($initial, 'UTF-8') : strtoupper($initial);
            @endphp
            <tr>
              <td><span class="ledger-ui__index">{{ $registers->firstItem() + $i }}</span></td>
              <td>
                <div class="ledger-ui__user">
                  <span class="ledger-ui__avatar">{{ $initials }}</span>
                  <div>
                    <strong>{{ $u->name }}</strong>
                    <span class="ledger-ui__subtext">{{ __('Register Account') }}</span>
                  </div>
                </div>
              </td>
              <td><span class="ledger-ui__email">{{ $u->email }}</span></td>
              <td>
                <span class="ledger-ui__status {{ (int) $u->status === 1 ? 'is-active' : 'is-inactive' }}">
                  {{ (int) $u->status === 1 ? __('Active') : __('Inactive') }}
                </span>
              </td>
              <td class="text-right">
                <div class="ledger-ui__amount">
                  <strong>$ {{ number_format($remaining, 2) }}</strong>
                  <span>{{ __('In') }} ${{ number_format($incoming, 2) }} / {{ __('Out') }} ${{ number_format($outgoing, 2) }}</span>
                </div>
              </td>
              <td>
                <div class="ledger-ui__actions">
                  <button class="btn ledger-ui__action ledger-ui__action--neutral" wire:click="openDetails({{ $u->id }})">
                    <i class="fas fa-wallet"></i>
                    <span>{{ __('Details') }}</span>
                  </button>

                  @if($isAdmin)
                    <button class="btn ledger-ui__action ledger-ui__action--success" wire:click="openTopUp({{ $u->id }})">
                      <i class="fas fa-plus-circle"></i>
                      <span>{{ __('Top Up') }}</span>
                    </button>

                    <button class="btn ledger-ui__action ledger-ui__action--danger" wire:click="openDeduct({{ $u->id }})">
                      <i class="fas fa-arrow-up"></i>
                      <span>{{ __('Transfer Back') }}</span>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="ledger-ui__empty">
                <strong class="d-block mb-2">{{ __('No registers found.') }}</strong>
                <span>{{ __('Try a different search term or widen the current filter range.') }}</span>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="ledger-ui__footer">
      {{ $registers->links() }}
    </div>
  </div>

  <div class="modal fade ledger-modal" id="senderDetailsModal" tabindex="-1" aria-labelledby="senderDetailsModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="senderDetailsModalLabel">
            <span class="ledger-modal__icon"><i class="fas fa-chart-line"></i></span>
            <span>
              {{ __('Sender Balance Details') }}
              @if($selectedUserName)
                <span class="ledger-modal__meta">{{ $selectedUserName }}</span>
              @endif
            </span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}" wire:click="closeModal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @if($selectedUserId)
            <div class="ledger-modal__panel">
              @livewire('balance.sender-balance-details-livewire', ['userId' => $selectedUserId], key('s-details-'.$selectedUserId))
            </div>
          @else
            <div class="ledger-modal__placeholder">{{ __('Select a register to view details.') }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade ledger-modal" id="senderTopUpModal" tabindex="-1" aria-labelledby="senderTopUpModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="senderTopUpModalLabel">
            <span class="ledger-modal__icon"><i class="fas fa-plus"></i></span>
            <span>
              {{ __('Top Up Sender Balance') }}
              @if($topUpUserName)
                <span class="ledger-modal__meta">{{ $topUpUserName }}</span>
              @endif
            </span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}" onclick="$('#senderTopUpModal').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          @if($topUpUserId)
            <div class="ledger-modal__intro">
              <strong>{{ __('Add fresh balance to this sender register') }}</strong>
              <span>{{ __('Use this for controlled USD balance increases. The optional note will be saved with the ledger entry.') }}</span>
            </div>

            <div class="ledger-modal__panel">
              <div class="ledger-ui__field">
                <label>{{ __('Amount (USD)') }}</label>
                <input type="number" step="0.01" min="0.01" class="form-control" wire:model.lazy="topUpAmount">
                @error('topUpAmount')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              <div class="ledger-ui__field">
                <label>{{ __('Note') }}</label>
                <input type="text" class="form-control" wire:model.lazy="topUpNote" placeholder="{{ __('Optional') }}">
                @error('topUpNote')<small class="text-danger">{{ $message }}</small>@enderror
              </div>
            </div>
          @else
            <div class="ledger-modal__placeholder">{{ __('Select a register to top up.') }}</div>
          @endif
        </div>

        <div class="modal-footer">
          <div class="ledger-ui__modal-actions">
            <button type="button" class="btn ledger-ui__modal-btn ledger-ui__modal-btn--light" onclick="$('#senderTopUpModal').modal('hide')">{{ __('Cancel') }}</button>
            <button type="button" class="btn ledger-ui__modal-btn ledger-ui__modal-btn--primary" wire:click="saveTopUp" @if(!$topUpUserId) disabled @endif>
              {{ __('Save Top Up') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade ledger-modal" id="senderDeductModal" tabindex="-1" aria-labelledby="senderDeductModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="senderDeductModalLabel">
            <span class="ledger-modal__icon"><i class="fas fa-arrow-up"></i></span>
            <span>
              {{ __('Deduct / Transfer Back (USD)') }}
              @if($deductUserName)
                <span class="ledger-modal__meta">{{ $deductUserName }}</span>
              @endif
            </span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}" onclick="$('#senderDeductModal').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          @if($deductUserId)
            <div class="ledger-modal__intro">
              <strong>{{ __('Move balance back out of the sender ledger') }}</strong>
              <span>{{ __('Use this when you need to reduce the register balance and optionally link that movement to a specific sender transfer.') }}</span>
            </div>

            <div class="ledger-modal__panel">
              <div class="ledger-ui__field">
                <label>{{ __('Amount (USD)') }}</label>
                <input type="number" step="0.01" min="0.01" class="form-control" wire:model.lazy="deductAmount">
                @error('deductAmount')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              <div class="ledger-ui__field">
                <label>{{ __('Note') }}</label>
                <input type="text" class="form-control" wire:model.lazy="deductNote" placeholder="{{ __('Optional, e.g., Transfer back to company') }}">
                @error('deductNote')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              <div class="ledger-ui__field">
                <label>{{ __('Link to Sender Transfer (Optional)') }}</label>
                <input type="number" class="form-control" wire:model.lazy="deductSenderId" placeholder="{{ __('Sender ID') }}">
                @error('deductSenderId')<small class="text-danger">{{ $message }}</small>@enderror
                <small>{{ __('If you want to tie this deduction to a specific sender transfer, add the sender ID here.') }}</small>
              </div>
            </div>
          @else
            <div class="ledger-modal__placeholder">{{ __('Select a register first.') }}</div>
          @endif
        </div>

        <div class="modal-footer">
          <div class="ledger-ui__modal-actions">
            <button type="button" class="btn ledger-ui__modal-btn ledger-ui__modal-btn--light" onclick="$('#senderDeductModal').modal('hide')">{{ __('Cancel') }}</button>
            <button type="button" class="btn ledger-ui__modal-btn ledger-ui__modal-btn--danger" wire:click="saveDeduct" @if(!$deductUserId) disabled @endif>
              {{ __('Save Deduction') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

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
          if (!e.detail?.message) return;
          alert(e.detail.message);
        });
      </script>
    @endpush
  @endonce
</div>
