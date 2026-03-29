@php
  $status = (string) ($sender->status ?? '');
  $formatMtcn = static function ($value) {
      $digits = preg_replace('/\D+/', '', (string) $value);

      if (strlen($digits) !== 10) {
          return trim((string) $value) !== '' ? (string) $value : '-';
      }

      return substr($digits, 0, 3).'-'.substr($digits, 3, 3).'-'.substr($digits, 6, 4);
  };
@endphp

<div class="d-inline-block">
  <div class="btn-group" role="group" aria-label="{{ __('Sender actions') }}">
    @if ($status === 'Pending')
      <button class="btn btn-sm btn-outline-success"
              wire:click="askExecute"
              wire:loading.attr="disabled"
              wire:target="askExecute"
              data-toggle="tooltip" title="{{ __('Review & Execute') }}">
        <i class="fas fa-check"></i>
      </button>

      <button class="btn btn-sm btn-outline-danger"
              onclick="confirm('{{ __('Mark as Rejected?') }}') || event.stopImmediatePropagation()"
              wire:click="markRejected"
              wire:loading.attr="disabled"
              wire:target="markRejected"
              data-toggle="tooltip" title="{{ __('Reject') }}">
        <i class="fas fa-times"></i>
      </button>

    @elseif ($status === 'Executed')
      <button class="btn btn-sm btn-outline-primary"
              onclick="confirm('{{ __('Mark as Pending?') }}') || event.stopImmediatePropagation()"
              wire:click="markPending"
              wire:loading.attr="disabled"
              wire:target="markPending"
              data-toggle="tooltip" title="{{ __('Move to Pending') }}">
        <i class="fas fa-retweet"></i>
      </button>

      <button class="btn btn-sm btn-outline-danger"
              onclick="confirm('{{ __('Mark as Rejected?') }}') || event.stopImmediatePropagation()"
              wire:click="markRejected"
              wire:loading.attr="disabled"
              wire:target="markRejected"
              data-toggle="tooltip" title="{{ __('Reject') }}">
        <i class="fas fa-times"></i>
      </button>

    @else
      <button class="btn btn-sm btn-outline-primary"
              onclick="confirm('{{ __('Mark as Pending?') }}') || event.stopImmediatePropagation()"
              wire:click="markPending"
              wire:loading.attr="disabled"
              wire:target="markPending"
              data-toggle="tooltip" title="{{ __('Move to Pending') }}">
        <i class="fas fa-retweet"></i>
      </button>

      <button class="btn btn-sm btn-outline-success"
              wire:click="askExecute"
              wire:loading.attr="disabled"
              wire:target="askExecute"
              data-toggle="tooltip" title="{{ __('Review & Execute') }}">
        <i class="fas fa-check"></i>
      </button>
    @endif
  </div>

  <div class="modal fade exec-modal" id="{{ $modalId }}" tabindex="-1" role="dialog"
       data-keep-open="{{ $execId ? '1' : '0' }}"
       aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">

        <form wire:submit.prevent="{{ $showExecutionConfirmation ? 'markExecutedConfirmed' : 'reviewExecution' }}" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title" id="{{ $modalId }}Label">
              <span class="soft-circle"><i class="fas fa-check"></i></span>
              {{ __('Execution Process') }}
            </h5>
            <button type="button" class="close btn-danger" data-dismiss="modal" aria-label="{{ __('Close') }}" onclick="window.modalCloseById && window.modalCloseById('{{ $modalId }}')" wire:click="closeModal">
              <span aria-hidden="true" class="text-white">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            @if ($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ __('Validation Errors:') }}</strong>
                <ul class="mb-0 mt-2">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            @endif

            @if ($showExecutionConfirmation)
              <div class="mb-3">
                <h5 class="mb-1">{{ __('Confirm Updated Values') }}</h5>
                <small class="text-muted">{{ __('Everything validated successfully. Review the final values below, then confirm to mark this transfer as Executed.') }}</small>
              </div>

              <div class="alert alert-primary exec-confirm-banner mb-4" role="alert">
                <strong>{{ __('Ready to submit.') }}</strong>
                {{ __('Please confirm these values before the transfer is marked as Executed.') }}
              </div>

              <div class="exec-confirm-grid">
                <div class="exec-confirm-card">
                  <span class="exec-confirm-kicker">{{ __('Sender') }}</span>
                  <h6>{{ __('Sender Details') }}</h6>
                  <div class="exec-confirm-row">
                    <span>{{ __('MTCN') }}</span>
                    <strong>{{ $executionPreview['newMtcn'] ?? '-' }}</strong>
                  </div>
                  <div class="exec-confirm-row">
                    <span>{{ __('First Name') }}</span>
                    <strong>{{ $executionPreview['newFirstName'] ?? '-' }}</strong>
                  </div>
                  <div class="exec-confirm-row">
                    <span>{{ __('Last Name') }}</span>
                    <strong>{{ $executionPreview['newLastName'] ?? '-' }}</strong>
                  </div>
                </div>

                <div class="exec-confirm-card">
                  <span class="exec-confirm-kicker">{{ __('Receiver') }}</span>
                  <h6>{{ __('Receiver Details') }}</h6>
                  <div class="exec-confirm-row">
                    <span>{{ __('First Name') }}</span>
                    <strong>{{ ($executionPreview['newReceiverFirstName'] ?? '') !== '' ? $executionPreview['newReceiverFirstName'] : '-' }}</strong>
                  </div>
                  <div class="exec-confirm-row">
                    <span>{{ __('Last Name') }}</span>
                    <strong>{{ ($executionPreview['newReceiverLastName'] ?? '') !== '' ? $executionPreview['newReceiverLastName'] : '-' }}</strong>
                  </div>
                </div>

                <div class="exec-confirm-card exec-confirm-card-wide">
                  <span class="exec-confirm-kicker">{{ __('Payout') }}</span>
                  <h6>{{ __('Execution Summary') }}</h6>
                  <div class="exec-confirm-row">
                    <span>{{ __('Payout Amount') }}</span>
                    <strong>{{ $executionPreview['payoutAmount'] ?? '-' }}</strong>
                  </div>
                  <div class="exec-confirm-row">
                    <span>{{ __('Currency') }}</span>
                    <strong>{{ $executionPreview['payoutCurrency'] ?? '-' }}</strong>
                  </div>
                  <div class="exec-confirm-row">
                    <span>{{ __('Transfer Total') }}</span>
                    <strong>{{ isset($execTotal) ? '$'.number_format($execTotal, 2) : '-' }}</strong>
                  </div>
                </div>
              </div>
            @else
              <div class="mb-3">
                <h5 class="mb-1">{{ __('Review & Update, then Approve') }}</h5>
                <small class="text-muted">{{ __('You can correct MTCN or the sender/receiver names before marking as Executed.') }}</small>
              </div>

              <div class="row">
                <div class="col-md-6 mb-2">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">{{ __('MTCN (current)') }}</span>
                    <strong>{{ $formatMtcn($oldMtcn) }}</strong>
                  </div>
                </div>
                <div class="col-md-6 mb-2">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">{{ __('Total') }}</span>
                    <strong>{{ isset($execTotal) ? '$'.number_format($execTotal, 2) : '-' }}</strong>
                  </div>
                </div>
              </div>

              <hr>

              <div class="mb-4">
                <h6 class="text-primary mb-3">
                  <i class="fas fa-user-circle mr-1"></i> {{ __('Sender Information') }}
                </h6>

                <div class="row">
                  <div class="col-md-6">
                    <div class="oldnew-label mb-2">{{ __('Old Values') }}</div>
                    <div class="form-group">
                      <label class="small text-muted">{{ __('MTCN (old)') }}</label>
                      <div class="old-box">{{ $formatMtcn($oldMtcn) }}</div>
                    </div>
                    <div class="form-group">
                      <label class="small text-muted">{{ __('First Name (old)') }}</label>
                      <div class="old-box">{{ $oldFirstName ?: '-' }}</div>
                    </div>
                    <div class="form-group">
                      <label class="small text-muted">{{ __('Last Name (old)') }}</label>
                      <div class="old-box">{{ $oldLastName ?: '-' }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="oldnew-label mb-2">{{ __('New Values') }}</div>
                    <div class="form-group">
                      <label class="small font-weight-semibold">{{ __('MTCN (new)') }} <span class="text-danger">*</span></label>
                      <input type="text" class="form-control @error('newMtcn') is-invalid @enderror"
                             maxlength="12" inputmode="numeric" placeholder="123-456-7890"
                             oninput="this.value = this.value.replace(/\D/g, '').slice(0, 10).replace(/(\d{0,3})(\d{0,3})(\d{0,4})/, function(_, a, b, c) { return [a, b, c].filter(Boolean).join('-'); });"
                             wire:model.defer="newMtcn">
                      @error('newMtcn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                      <small class="text-muted">{{ __('Format: XXX-XXX-XXXX') }}</small>
                    </div>
                    <div class="form-group">
                      <label class="small font-weight-semibold">{{ __('First Name (new)') }} <span class="text-danger">*</span></label>
                      <input type="text" class="form-control @error('newFirstName') is-invalid @enderror"
                             style="text-transform:uppercase"
                             wire:model.defer="newFirstName">
                      @error('newFirstName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                      <label class="small font-weight-semibold">{{ __('Last Name (new)') }} <span class="text-danger">*</span></label>
                      <input type="text" class="form-control @error('newLastName') is-invalid @enderror"
                             style="text-transform:uppercase"
                             wire:model.defer="newLastName">
                      @error('newLastName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                  </div>
                </div>
              </div>

              <hr class="my-4">

              <div class="mb-4">
                <h6 class="text-success mb-3">
                  <i class="fas fa-user-check mr-1"></i> {{ __('Receiver Information') }}
                </h6>

                <div class="row">
                  <div class="col-md-6">
                    <div class="oldnew-label mb-2">{{ __('Old Values') }}</div>
                    <div class="form-group">
                      <label class="small text-muted">{{ __('First Name (old)') }}</label>
                      <div class="old-box">{{ $oldReceiverFirstName ?: '-' }}</div>
                    </div>
                    <div class="form-group">
                      <label class="small text-muted">{{ __('Last Name (old)') }}</label>
                      <div class="old-box">{{ $oldReceiverLastName ?: '-' }}</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="oldnew-label mb-2">{{ __('New Values') }}</div>
                    <div class="form-group">
                      <label class="small font-weight-semibold">{{ __('First Name (new)') }}</label>
                      <input type="text" class="form-control @error('newReceiverFirstName') is-invalid @enderror"
                             style="text-transform:uppercase"
                             placeholder="{{ __('Optional') }}"
                             wire:model.defer="newReceiverFirstName">
                      @error('newReceiverFirstName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                      <label class="small font-weight-semibold">{{ __('Last Name (new)') }}</label>
                      <input type="text" class="form-control @error('newReceiverLastName') is-invalid @enderror"
                             style="text-transform:uppercase"
                             placeholder="{{ __('Optional') }}"
                             wire:model.defer="newReceiverLastName">
                      @error('newReceiverLastName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                  </div>
                </div>
              </div>

              <hr class="my-4">

              <div>
                <h6 class="text-info mb-3">
                  <i class="fas fa-money-bill-wave mr-1"></i> {{ __('Payout Details') }}
                </h6>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="small font-weight-semibold">{{ __('Payout Amount') }} <span class="text-danger">*</span></label>
                      <input type="text"
                             class="form-control @error('payoutAmount') is-invalid @enderror"
                             placeholder="6,500.50"
                             wire:model.defer="payoutAmount">
                      @error('payoutAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                      <small class="text-muted">{{ __('Use dot for decimals (e.g. 1234.56)') }}</small>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="small font-weight-semibold">{{ __('Currency (ISO 4217)') }} <span class="text-danger">*</span></label>
                      <input type="text"
                             class="form-control @error('payoutCurrency') is-invalid @enderror"
                             placeholder="USD / IQD / EUR"
                             maxlength="3"
                             style="text-transform:uppercase"
                             wire:model.defer="payoutCurrency">
                      @error('payoutCurrency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                      <small class="text-muted">{{ __('Exactly 3 letters (e.g. USD, IQD)') }}</small>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal" onclick="window.modalCloseById && window.modalCloseById('{{ $modalId }}')" wire:click="closeModal">
              {{ __('Cancel') }}
            </button>

            @if ($showExecutionConfirmation)
              <button type="button" class="btn btn-light" wire:click="editExecution" wire:loading.attr="disabled" wire:target="editExecution,markExecutedConfirmed">
                {{ __('Back to Edit') }}
              </button>
              <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="markExecutedConfirmed">
                <span wire:loading wire:target="markExecutedConfirmed" class="spinner-border spinner-border-sm mr-1"></span>
                <span wire:loading.remove wire:target="markExecutedConfirmed">{{ __('Confirm & Mark Executed') }}</span>
                <span wire:loading wire:target="markExecutedConfirmed">{{ __('Processing...') }}</span>
              </button>
            @else
              <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="reviewExecution">
                <span wire:loading wire:target="reviewExecution" class="spinner-border spinner-border-sm mr-1"></span>
                <span wire:loading.remove wire:target="reviewExecution">{{ __('Update & Mark Executed') }}</span>
                <span wire:loading wire:target="reviewExecution">{{ __('Checking...') }}</span>
              </button>
            @endif
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@push('css')
<style>
  .exec-modal .modal-dialog { max-width: 900px; }
  .exec-modal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 22px 60px rgba(0,0,0,.25);
  }

  .exec-modal .modal-header {
    background: linear-gradient(135deg,#111827,#1f2937);
    color: #fff;
    border: 0;
    padding: 16px 20px;
  }

  .exec-modal .soft-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .exec-modal .modal-footer { border: 0; background: #f9fafb; }

  .exec-modal .oldnew-label {
    font-size: .85rem;
    text-transform: uppercase;
    color: #6b7280;
    font-weight: 600;
    letter-spacing: .5px;
  }

  .exec-modal .old-box {
    background: #f3f4f6;
    border-radius: 8px;
    padding: .75rem;
    min-height: 42px;
    color: #4b5563;
    font-weight: 500;
  }

  .exec-modal h6 {
    font-weight: 600;
    font-size: 1rem;
    padding-bottom: 8px;
    border-bottom: 2px solid #e5e7eb;
  }

  .exec-modal .form-control {
    border-radius: 8px;
    border: 1px solid #d1d5db;
    padding: 0.65rem 0.75rem;
  }

  .exec-modal .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  .exec-modal label.font-weight-semibold {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
  }

  .exec-modal small.text-muted {
    font-size: 0.8rem;
    color: #6b7280;
  }

  .exec-modal hr {
    border-top: 1px solid #e5e7eb;
    margin: 1.5rem 0;
  }

  .exec-modal .exec-confirm-banner {
    border: 0;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(59,130,246,.16), rgba(37,99,235,.08));
    color: #1e3a8a;
  }

  .exec-modal .exec-confirm-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
  }

  .exec-modal .exec-confirm-card {
    border: 1px solid #dbeafe;
    border-radius: 14px;
    padding: 1rem;
    background: linear-gradient(180deg, #ffffff, #f8fbff);
  }

  .exec-modal .exec-confirm-card-wide {
    grid-column: 1 / -1;
  }

  .exec-modal .exec-confirm-kicker {
    display: inline-flex;
    align-items: center;
    padding: .3rem .65rem;
    border-radius: 999px;
    background: rgba(59,130,246,.1);
    color: #2563eb;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: .75rem;
  }

  .exec-modal .exec-confirm-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: .7rem 0;
    border-bottom: 1px solid #e5e7eb;
  }

  .exec-modal .exec-confirm-row:last-child {
    border-bottom: 0;
    padding-bottom: 0;
  }

  .exec-modal .exec-confirm-row span {
    color: #6b7280;
  }

  .exec-modal .exec-confirm-row strong {
    color: #111827;
    text-align: right;
    word-break: break-word;
  }

  .exec-modal .btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border: none;
    border-radius: 8px;
    padding: 0.65rem 1.5rem;
    font-weight: 600;
    transition: all 0.2s;
  }

  .exec-modal .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  }

  .exec-modal .btn-light {
    border-radius: 8px;
    padding: 0.65rem 1.5rem;
    font-weight: 600;
  }

  .modal-backdrop.show {
    z-index: -9999;
  }

  @media (max-width: 767.98px) {
    .exec-modal .exec-confirm-grid {
      grid-template-columns: 1fr;
    }

    .exec-modal .exec-confirm-card-wide {
      grid-column: auto;
    }

    .exec-modal .exec-confirm-row {
      flex-direction: column;
      gap: .3rem;
    }

    .exec-modal .exec-confirm-row strong {
      text-align: left;
    }
  }
</style>
@endpush

@push('scripts')
@once
<script>
(function () {
  if (window.__ExecModalBound) return;
  window.__ExecModalBound = true;

  const hasJQ = !!(window.$ && $.fn && $.fn.modal);

  function escapeId(id) {
    return window.CSS && CSS.escape ? CSS.escape(id) : id;
  }

  function isShown(el) {
    return !!el && (el.classList.contains('show') || el.style.display === 'block');
  }

  function forceVisible(el) {
    if (!el) return;
    el.style.display = 'block';
    el.classList.add('show');
    el.removeAttribute('aria-hidden');
    el.setAttribute('aria-modal', 'true');
    document.body.classList.add('modal-open');
  }

  function cleanupModalArtifacts() {
    if (document.querySelector('.modal.show')) return;
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      try { backdrop.remove(); } catch (_) {}
    });
  }

  function mountToBody(el) {
    const id = el?.id;
    if (!id) return el;

    const existing = document.querySelector(`body > .exec-modal#${escapeId(id)}`);
    if (existing && existing !== el) {
      const keepOpen = isShown(existing);
      const scrollTop = existing.querySelector('.modal-body')?.scrollTop || 0;
      try { existing.remove(); } catch (_) {}
      if (!el.dataset.portedToBody) {
        document.body.appendChild(el);
        el.dataset.portedToBody = '1';
      }
      if (keepOpen) {
        forceVisible(el);
        const body = el.querySelector('.modal-body');
        if (body) body.scrollTop = scrollTop;
      }
      return el;
    }

    if (!el.dataset.portedToBody) {
      document.body.appendChild(el);
      el.dataset.portedToBody = '1';
    }

    return el;
  }

  function showModal(id) {
    const el = mountToBody(document.getElementById(id));
    if (!el) return;
    if (isShown(el)) return;

    if (hasJQ) {
      $('#'+id).modal({ backdrop: 'static', keyboard: false, show: true });
    } else if (window.bootstrap?.Modal) {
      (window.bootstrap.Modal.getInstance(el) || new window.bootstrap.Modal(el, { backdrop: 'static', keyboard: false })).show();
    } else {
      forceVisible(el);
    }
  }

  function hideModal(id) {
    const el = document.querySelector(`body > .exec-modal#${escapeId(id)}`) || document.getElementById(id);
    if (!el) return;

    if (hasJQ) {
      $('#'+id).modal('hide');
    } else if (window.bootstrap?.Modal) {
      window.bootstrap.Modal.getInstance(el)?.hide();
    } else {
      el.classList.remove('show');
      el.style.display = 'none';
      el.setAttribute('aria-hidden', 'true');
      el.removeAttribute('aria-modal');
      if (!document.querySelector('body > .exec-modal.show')) {
        document.body.classList.remove('modal-open');
      }
    }
  }

  window.addEventListener('modal:open', e => {
    if (e.detail?.id) showModal(e.detail.id);
  }, { passive: true });

  window.addEventListener('modal:close', e => {
    if (e.detail?.id) hideModal(e.detail.id);
  }, { passive: true });

  document.addEventListener('livewire:load', () => {
    Livewire.hook('message.processed', () => {
      document.querySelectorAll('body > .exec-modal[data-ported-to-body="1"]').forEach(currentModal => {
        const source = Array.from(document.querySelectorAll(`.exec-modal#${escapeId(currentModal.id)}`))
          .find(candidate => candidate !== currentModal && !candidate.dataset.portedToBody);

        if (!source) return;

        const keepOpen = source.dataset.keepOpen === '1';
        const scrollTop = currentModal.querySelector('.modal-body')?.scrollTop || 0;

        try { currentModal.remove(); } catch (_) {}
        cleanupModalArtifacts();

        if (!keepOpen) {
          return;
        }

        document.body.appendChild(source);
        source.dataset.portedToBody = '1';

        if (keepOpen) {
          forceVisible(source);
          const body = source.querySelector('.modal-body');
          if (body) body.scrollTop = scrollTop;
        }
      });
    });
  });

  window.modalOpenById = showModal;
  window.modalCloseById = hideModal;
})();
</script>
@endonce
@endpush
