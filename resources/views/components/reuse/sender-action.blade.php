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
  <div wire:ignore.self class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog"
       aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">

        <form wire:submit.prevent="markExecutedConfirmed">
          <div class="modal-header">
            <h5 class="modal-title" id="{{ $modalId }}Label">
              <span class="soft-circle"><i class="fas fa-check"></i></span>
              {{ __('Execution Process') }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="closeModal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <h5 class="mb-1">{{ __('Review & Update, then Approve') }}</h5>
              <small class="text-muted">{{ __('You can correct MTCN or the sender name before marking as Executed.') }}</small>
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

            <div class="row">
              <div class="col-md-6">
                <div class="text-muted mb-1">{{ __('Old Values') }}</div>
                <div class="form-group">
                  <label class="small text-muted">{{ __('MTCN (old)') }}</label>
                  <div class="border rounded p-2">{{ $oldMtcn ?: '—' }}</div>
                </div>
                <div class="form-group">
                  <label class="small text-muted">{{ __('First Name (old)') }}</label>
                  <div class="border rounded p-2">{{ $oldFirstName ?: '—' }}</div>
                </div>
                <div class="form-group">
                  <label class="small text-muted">{{ __('Last Name (old)') }}</label>
                  <div class="border rounded p-2">{{ $oldLastName ?: '—' }}</div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-1">{{ __('New Values') }}</div>
                <div class="form-group">
                  <label class="small">{{ __('MTCN (new)') }}</label>
                  <input type="text" class="form-control @error('newMtcn') is-invalid @enderror"
                         maxlength="10" inputmode="numeric" placeholder="##########"
                         wire:model.defer="newMtcn">
                  @error('newMtcn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  <small class="text-muted">{{ __('10 digits') }}</small>
                </div>
                <div class="form-group">
                  <label class="small">{{ __('First Name (new)') }}</label>
                  <input type="text" class="form-control @error('newFirstName') is-invalid @enderror"
                         wire:model.defer="newFirstName">
                  @error('newFirstName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                  <label class="small">{{ __('Last Name (new)') }}</label>
                  <input type="text" class="form-control @error('newLastName') is-invalid @enderror"
                         wire:model.defer="newLastName">
                  @error('newLastName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <hr class="my-3">

              <div class="row mx-auto">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="small">{{ __('Payout Amount') }}</label>
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
                    <label class="small">{{ __('Currency (ISO 4217)') }}</label>
                    <input type="text"
                          class="form-control @error('payoutCurrency') is-invalid @enderror"
                          placeholder="USD / IQD / IDR"
                          maxlength="3"
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
            <button type="submit" class="btn btn-primary">
              <span wire:loading wire:target="markExecutedConfirmed" class="spinner-border spinner-border-sm mr-1"></span>
              {{ __('Update & Mark Executed') }}
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
  .exec-modal .modal-dialog { max-width: 720px; }
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

  /* kpi / old-new */
  .exec-modal .kpi { background: #f3f4f6; border-radius: 12px; padding: 10px 12px; }
  .exec-modal .oldnew-label { font-size:.8rem; text-transform:uppercase; color:#6b7280; }
  .exec-modal .old-box { background:#f3f4f6; border-radius:10px; padding:.65rem .75rem; min-height:40px; }
</style>


@endpush
@push('scripts')
<script>
  // Bootstrap 4 helpers
  window.modalOpenById  = id => $('#' + id).modal({ backdrop: 'static', keyboard: false, show: true });
  window.modalCloseById = id => $('#' + id).modal('hide');

  // Livewire -> JS
  window.addEventListener('modal:open',  e => {
    const id = e.detail?.id;
    if (!id) return;
    const $m = $('#' + id);
    if ($m.length) {
      window.modalOpenById(id);
    }
  });

  window.addEventListener('modal:close', e => {
    const id = e.detail?.id;
    if (id) window.modalCloseById(id);
  });
</script>
<script>
(function () {
  const useJQ = !!(window.$ && $.fn && $.fn.modal);

  function show(id){
    const el = document.getElementById(id);
    if (!el) return;
    // port once to body (prevents clipping inside tables)
    if (!el.dataset.portedToBody) {
      document.body.appendChild(el);
      el.dataset.portedToBody = "1";
    }
    if (useJQ) { $('#'+id).modal({backdrop:'static',keyboard:false,show:true}); }
    else {
      const bs = window.bootstrap;
      if (bs?.Modal) (bs.Modal.getInstance(el) || new bs.Modal(el)).show();
    }
  }

  function hide(id){
    const el = document.getElementById(id);
    if (!el) return;
    if (useJQ) { $('#'+id).modal('hide'); }
    else {
      const bs = window.bootstrap;
      if (bs?.Modal) bs.Modal.getInstance(el)?.hide();
    }
  }

  window.addEventListener('modal:open',  e => e.detail?.id && show(e.detail.id));
  window.addEventListener('modal:close', e => e.detail?.id && hide(e.detail.id));

  // safety: if a component re-renders and kills the row before closing, auto-hide any open modals after update
  document.addEventListener('livewire:load', () => {
    Livewire.hook('message.processed', () => {
      document.querySelectorAll('.modal.show').forEach(m => hide(m.id));
    });
  });
})();
</script>
@endpush
