@extends('layouts.app')

@section('app')
<div class="container py-5" style="max-width: 520px;">
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="mb-3">{{ __('Admin Verification') }}</h5>
      <p class="text-muted">{{ __('Enter your admin 2FA code to continue.') }}</p>

      <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">{{ __('2FA Code') }}</label>
          <input type="password" name="code" class="form-control @error('code') is-invalid @enderror" autofocus>
          @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-end">
          <button class="btn btn-primary" type="submit">
            {{ __('Verify & Continue') }}
          </button>
        </div>
      </form>

      <div class="small text-muted mt-3">
        {{ __('You’ll stay verified for 10 minutes on this device.') }}
      </div>
    </div>
  </div>
</div>
@endsection
