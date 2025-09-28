<div>
@php use Illuminate\Support\Facades\Storage; @endphp
@once
@push('scripts')
<script>
  // requires jQuery + bootstrap.js (v4) loaded in your layout
  window.addEventListener('receiver-preview:open', function (e) {
    var id = e.detail.id;
    $('#receiverPreview-' + id).modal({
      backdrop: 'static',  // optional: prevent closing on backdrop click
      keyboard: true,
      show: true
    });
  });

  window.addEventListener('receiver-preview:close', function (e) {
    var id = e.detail.id;
    $('#receiverPreview-' + id).modal('hide');
  });
</script>
@endpush
@push('css')
<style>
    .modal-backdrop{
        z-index: -5;   
    }
</style>
@endpush
@endonce
{{-- Trigger button (eye icon) --}}
<div class="btn-group">
<button type="button"
        class="btn btn-sm btn-outline-primary"
        wire:click.stop.prevent="open">
  <i class="far fa-eye"></i>
</button>
</div>
{{-- Modal --}}
<div class="modal fade" id="receiverPreview-{{ $receiverId }}" tabindex="-1" wire:ignore.self>
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          {{ __('Receiver Preview') }} — {{ $receiver?->first_name }} {{ $receiver?->last_name }}
        </h5>
        <button type="button" class="close" aria-label="Close" wire:click="close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
              @php
                $rInit = trim(($receiver->first_name[0] ?? '').($receiver->last_name[0] ?? ''));
                $amountIqd  = (float) $receiver->amount_iqd;
                $amountClass = $amountIqd > 1_000_000 ? 'amount-display-g'
                              : ($amountIqd >= 500_000 ? 'amount-display-b' : 'amount-display-r');
                $statusClass = match($receiver->status) {
                  'Pending'  => 'bg-warning text-dark',
                  'Executed' => 'bg-success text-white',
                  'Rejected' => 'bg-danger text-white',
                  default    => 'bg-secondary text-white'
                };
              @endphp
      <div class="modal-body">
        @if(!$receiver)
          <div class="text-muted">{{ __('No data') }}</div>
        @else
          <div class="row g-3">
            <div class="col-md-6">
              <div class="small text-muted">{{ __('MTCN') }}</div>
              <div class="h6 mb-0">{{ $receiver->mtcn }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">{{ __('Status') }}</div>
              <span class="badge {{ $statusClass }}">
                {{ $receiver->status }}
              </span>
            </div>

            <div class="col-md-6">
              <div class="small text-muted">{{ __('First Name') }}</div>
              <div class="h6 mb-0">{{ $receiver->first_name }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">{{ __('Last Name') }}</div>
              <div class="h6 mb-0">{{ $receiver->last_name }}</div>
            </div>

            <div class="col-md-6">
              <div class="small text-muted">{{ __('Phone') }}</div>
              <div class="h6 mb-0">{{ $receiver->phone }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">{{ __('Amount (IQD)') }}</div>
              <div class="h6 mb-0 {{ $amountClass }}">{{ number_format((float)$receiver->amount_iqd, 0) }}</div>
            </div>

            <div class="col-12">
              <div class="small text-muted">{{ __('Address') }}</div>
              <div class="h6 mb-0">{{ $receiver->address ?: '-' }}</div>
            </div>

            {{-- Identification --}}
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>{{ __('Identification') }}</strong>
                @if(empty($identFiles))
                  <span class="text-muted small">{{ __('No file uploaded') }}</span>
                @endif
              </div>

              @if(!empty($identFiles))
                <div class="d-flex flex-wrap gap-3">
                  @foreach($identFiles as $f)
                    @if($f['is_image'])
                      <a href="{{ $f['url'] }}" target="_blank" class="text-decoration-none mr-3 mb-3">
                        <img src="{{ $f['url'] }}"
                             alt="{{ $f['name'] }}"
                             class="rounded border"
                             style="width:360px;height:240px;object-fit:cover;">
                        <div class="small mt-1 text-muted text-center" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                          {{ $f['name'] }}
                        </div>
                      </a>
                    @elseif($f['is_pdf'])
                      <a href="{{ $f['url'] }}" target="_blank"
                         class="d-inline-flex align-items-center px-3 py-2 border rounded bg-light mr-3 mb-3">
                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                        <span class="small">{{ $f['name'] }}</span>
                      </a>
                    @endif
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        @endif
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" wire:click="close">{{ __('Close') }}</button>
      </div>
    </div>
  </div>
</div>
</div>