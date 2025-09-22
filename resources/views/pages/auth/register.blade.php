@extends('layouts.app')
@push('css')

@endpush
@push('scripts')
  {{-- Simple toast hook (optional) --}}
  <script>
    window.addEventListener('toast', e => {
      if (!e.detail?.message) return;
      // minimal toast; replace with your preferred toastr
      alert(e.detail.message);
    });
  </script>
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Registers Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Authintication') }}</li>
                            <li class="breadcrumb-item text-muted active">{{ __('Register') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('Register Data') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('auth.auth-register-livewire')
    </div>
@endsection
