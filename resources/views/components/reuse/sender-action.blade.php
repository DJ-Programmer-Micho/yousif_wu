@php
  $status = (string) ($sender->status ?? '');
@endphp

<div class="d-inline-block">
  <div class="btn-group" role="group" aria-label="Sender actions">
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

    @else {{-- Rejected --}}
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

  {{-- Modal (unique per row) --}}
  <div class="modal fade exec-modal" id="{{ $modalId }}" tabindex="-1" role="dialog"
       aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">

        <form wire:submit.prevent="markExecutedConfirmed" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title" id="{{ $modalId }}Label">
              <span class="soft-circle"><i class="fas fa-check"></i></span>
              {{ __('Execution Process') }}
            </h5>
            <button type="button" class="close btn-danger" data-dismiss="modal" aria-label="Close" wire:click="closeModal">
              <span aria-hidden="true" class="text-white">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            {{-- Display all validation errors at top --}}
            @if ($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ __('Validation Errors:') }}</strong>
                <ul class="mb-0 mt-2">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            @endif

            <div class="mb-3">
              <h5 class="mb-1">{{ __('Review & Update, then Approve') }}</h5>
              <small class="text-muted">{{ __('You can correct MTCN or the sender/receiver names before marking as Executed.') }}</small>
            </div>

            <div class="row">
              <div class="col-md-6 mb-2">
                <div class="d-flex justify-content-between">
                  <span class="text-muted">{{ __('MTCN (current)') }}</span>
                  <strong>{{ $oldMtcn ?: '—' }}</strong>
                </div>
              </div>
              <div class="col-md-6 mb-2">
                <div class="d-flex justify-content-between">
                  <span class="text-muted">{{ __('Total') }}</span>
                  <strong>{{ isset($execTotal) ? '$'.number_format($execTotal,2) : '—' }}</strong>
                </div>
              </div>
            </div>

            <hr>

            {{-- SENDER SECTION --}}
            <div class="mb-4">
              <h6 class="text-primary mb-3">
                <i class="fas fa-user-circle mr-1"></i> {{ __('Sender Information') }}
              </h6>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="oldnew-label mb-2">{{ __('Old Values') }}</div>
                  <div class="form-group">
                    <label class="small text-muted">{{ __('MTCN (old)') }}</label>
                    <div class="old-box">{{ $oldMtcn ?: '—' }}</div>
                  </div>
                  <div class="form-group">
                    <label class="small text-muted">{{ __('First Name (old)') }}</label>
                    <div class="old-box">{{ $oldFirstName ?: '—' }}</div>
                  </div>
                  <div class="form-group">
                    <label class="small text-muted">{{ __('Last Name (old)') }}</label>
                    <div class="old-box">{{ $oldLastName ?: '—' }}</div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="oldnew-label mb-2">{{ __('New Values') }}</div>
                  <div class="form-group">
                    <label class="small font-weight-semibold">{{ __('MTCN (new)') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('newMtcn') is-invalid @enderror"
                           maxlength="10" inputmode="numeric" placeholder="##########"
                           wire:model.defer="newMtcn">
                    @error('newMtcn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">{{ __('10 digits') }}</small>
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

            {{-- RECEIVER SECTION (NEW) --}}
            <div class="mb-4">
              <h6 class="text-success mb-3">
                <i class="fas fa-user-check mr-1"></i> {{ __('Receiver Information') }}
              </h6>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="oldnew-label mb-2">{{ __('Old Values') }}</div>
                  <div class="form-group">
                    <label class="small text-muted">{{ __('First Name (old)') }}</label>
                    <div class="old-box">{{ $oldReceiverFirstName ?: '—' }}</div>
                  </div>
                  <div class="form-group">
                    <label class="small text-muted">{{ __('Last Name (old)') }}</label>
                    <div class="old-box">{{ $oldReceiverLastName ?: '—' }}</div>
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

            {{-- PAYOUT SECTION --}}
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
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal" wire:click="closeModal">
              {{ __('Cancel') }}
            </button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="markExecutedConfirmed">
              <span wire:loading wire:target="markExecutedConfirmed" class="spinner-border spinner-border-sm mr-1"></span>
              <span wire:loading.remove wire:target="markExecutedConfirmed">{{ __('Update & Mark Executed') }}</span>
              <span wire:loading wire:target="markExecutedConfirmed">{{ __('Processing...') }}</span>
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@push('css') 
<style>
  /* size & shell */
  .exec-modal .modal-dialog { max-width: 900px; }
  .exec-modal .modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 22px 60px rgba(0,0,0,.25);
  }

  /* header */
  .exec-modal .modal-header {
    background: linear-gradient(135deg,#111827,#1f2937);
    color: #fff;
    border: 0;
    padding: 16px 20px;
  }
  .exec-modal .soft-circle {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,.12);
    display: inline-flex; align-items: center; justify-content: center;
  }

  /* footer */
  .exec-modal .modal-footer { border: 0; background: #f9fafb; }

  /* old-new labels */
  .exec-modal .oldnew-label { 
    font-size:.85rem; 
    text-transform:uppercase; 
    color:#6b7280; 
    font-weight: 600;
    letter-spacing: 0.5px;
  }
  
  .exec-modal .old-box { 
    background:#f3f4f6; 
    border-radius:8px; 
    padding:.75rem; 
    min-height:42px;
    color: #4b5563;
    font-weight: 500;
  }

  /* Section headers */
  .exec-modal h6 {
    font-weight: 600;
    font-size: 1rem;
    padding-bottom: 8px;
    border-bottom: 2px solid #e5e7eb;
  }

  /* Form inputs */
  .exec-modal .form-control {
    border-radius: 8px;
    border: 1px solid #d1d5db;
    padding: 0.65rem 0.75rem;
  }

  .exec-modal .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  /* Labels */
  .exec-modal label.font-weight-semibold {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
  }

  /* Small text */
  .exec-modal small.text-muted {
    font-size: 0.8rem;
    color: #6b7280;
  }

  /* Dividers */
  .exec-modal hr {
    border-top: 1px solid #e5e7eb;
    margin: 1.5rem 0;
  }

  /* Button styling */
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
    /* opacity: .5; */
    z-index: -9999;
}

</style>
@endpush

@push('scripts')
@once
<script>
/**
 * Exec Modal manager (Bootstrap 4/5 compatible).
 * Ensures:
 *  - listeners are bound only once
 *  - only one modal with a given id exists in <body>
 *  - stale ported modals are cleaned after Livewire patches
 */
(function () {
  if (window.__ExecModalBound) return;
  window.__ExecModalBound = true;

  const hasJQ = !!(window.$ && $.fn && $.fn.modal);

  function mountToBody(el) {
    const id = el.id;
    if (!id) return;

    // Remove any previously ported modal with the same id
    const existing = document.querySelector(`body > .exec-modal#${CSS && CSS.escape ? CSS.escape(id) : id}`);
    if (existing && existing !== el) {
      try { existing.remove(); } catch (_) {}
    }

    // Move current element to body once
    if (!el.dataset.portedToBody) {
      document.body.appendChild(el);
      el.dataset.portedToBody = "1";
    }
  }

  function showModal(id) {
    const el = document.getElementById(id);
    if (!el) return;

    // Port to body safely (dedupe first)
    mountToBody(el);

    if (hasJQ) {
      $('#'+id).modal({ backdrop: 'static', keyboard: false, show: true });
    } else if (window.bootstrap?.Modal) {
      (window.bootstrap.Modal.getInstance(el) || new window.bootstrap.Modal(el, { backdrop: 'static', keyboard: false })).show();
    }
  }

  function hideModal(id) {
    const el = document.getElementById(id);
    if (!el) return;

    if (hasJQ) {
      $('#'+id).modal('hide');
    } else if (window.bootstrap?.Modal) {
      window.bootstrap.Modal.getInstance(el)?.hide();
    }
  }

  // Livewire -> JS events
  window.addEventListener('modal:open',  e => e.detail?.id && showModal(e.detail.id),  { passive: true });
  window.addEventListener('modal:close', e => e.detail?.id && hideModal(e.detail.id), { passive: true });

  // After each Livewire patch, remove stale ported modals that no longer have a source element
  document.addEventListener('livewire:load', () => {
    Livewire.hook('message.processed', () => {
      document.querySelectorAll('body > .exec-modal[data-ported-to-body="1"], body > .exec-modal[data-portedtobody="1"], body > .exec-modal[data-ported_to_body="1"], body > .exec-modal[data-ported-to-body], body > .exec-modal[data-portedToBody]')
        .forEach(m => {
          // If there is no element with this id in the DOM except this one,
          // it means the component was re-rendered and original got replaced.
          const source = document.getElementById(m.id);
          // If the only element with that id is itself (in body), and no "origin" exists anymore,
          // allow it to be removed to prevent duplicates on next open.
          if (!source || source === m) return; // keep current; the new render will be ported when opened

          // If there are 2 nodes with same id (one in body, one in component), remove the stale one in body
          if (source !== m) {
            try { m.remove(); } catch (_) {}
          }
        });
    });
  });

  // Optional helpers (if you still call them elsewhere)
  window.modalOpenById  = showModal;
  window.modalCloseById = hideModal;
})();
</script>
@endonce
@endpush
