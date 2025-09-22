{{-- resources/views/pages/balance/sender/index.blade.php --}}
@extends('layouts.app')

@push('css')
@endpush

@push('scripts')
{{-- <script>
  window.addEventListener('toast', e => {
    if (!e.detail?.message) return;
    alert(e.detail.message);
  });
</script> --}}

<script>
  window.addEventListener('open-confirm-modal', () => {
    const el = document.getElementById('confirmModal');
    if (!el) return;
    const modal = bootstrap ? bootstrap.Modal.getOrCreateInstance(el) : null;
    modal && modal.show();
  });
  window.addEventListener('close-confirm-modal', () => {
    const el = document.getElementById('confirmModal');
    if (!el) return;
    const modal = bootstrap ? bootstrap.Modal.getOrCreateInstance(el) : null;
    modal && modal.hide();
  });
</script>
@endpush

@section('app')
  <div class="page-breadcrumb">
    <div class="row">
      <div class="col-12 align-self-center">
        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Registers (Sender)') }}</h4>
        <div class="d-flex align-items-center">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0 p-0">
              <li class="breadcrumb-item text-muted active">{{ __('Balance') }}</li>
              <li class="breadcrumb-item text-muted active">{{ __('Register') }}</li>
              <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('Sender') }}</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid mt-3">
    @livewire('balance.sender-balance-livewire')
  </div>
@endsection
