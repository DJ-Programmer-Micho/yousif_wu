@extends('layouts.app')

@push('css')
  <style>
    .twofactor-shell{
      max-width:560px;
      margin:1.5rem auto 0;
    }
    .twofactor-card{
      position:relative;
      overflow:hidden;
      padding:1.8rem;
      border-radius:28px;
      border:1px solid rgba(255,255,255,.7);
      background:
        radial-gradient(circle at top right, rgba(236,72,153,.14), transparent 24%),
        radial-gradient(circle at left center, rgba(99,102,241,.16), transparent 34%),
        linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.94));
      box-shadow:0 28px 70px rgba(15,23,42,.1);
      backdrop-filter:blur(16px);
    }
    .twofactor-kicker{
      display:inline-flex;
      align-items:center;
      padding:.38rem .78rem;
      border-radius:999px;
      background:rgba(99,102,241,.12);
      color:#4338ca;
      font-size:.78rem;
      font-weight:800;
      letter-spacing:.04em;
      text-transform:uppercase;
    }
    .twofactor-title{
      margin:1rem 0 .45rem;
      font-size:1.8rem;
      font-weight:800;
      color:#111827;
    }
    .twofactor-text{
      margin:0;
      color:#64748b;
      line-height:1.7;
    }
    .twofactor-info{
      display:flex;
      align-items:flex-start;
      gap:.9rem;
      margin:1.4rem 0 1.2rem;
      padding:1rem 1.05rem;
      border-radius:20px;
      background:linear-gradient(135deg, rgba(124,58,237,.08), rgba(236,72,153,.08));
      border:1px solid rgba(124,58,237,.14);
    }
    .twofactor-info-icon{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:44px;
      height:44px;
      border-radius:16px;
      background:rgba(124,58,237,.14);
      color:#6d28d9;
      flex:0 0 auto;
    }
    .twofactor-info strong{
      display:block;
      margin-bottom:.2rem;
      color:#111827;
    }
    .twofactor-info p{
      margin:0;
      color:#64748b;
    }
    .twofactor-label{
      display:block;
      margin-bottom:.55rem;
      color:#334155;
      font-weight:700;
    }
    .twofactor-input-shell{
      display:flex;
      align-items:center;
      gap:.75rem;
      min-height:56px;
      padding:0 1rem;
      border-radius:18px;
      border:1px solid rgba(148,163,184,.18);
      background:rgba(255,255,255,.9);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.88);
    }
    .twofactor-input-shell i{
      color:#8b5cf6;
    }
    .twofactor-input{
      border:none;
      background:transparent;
      box-shadow:none;
      padding:0;
      height:auto;
      color:#111827;
      font-size:1.05rem;
      font-weight:700;
      letter-spacing:.12em;
    }
    .twofactor-input:focus{
      box-shadow:none;
    }
    .twofactor-btn{
      width:100%;
      margin-top:1.35rem;
      border:none;
      border-radius:18px;
      padding:.95rem 1.2rem;
      background:linear-gradient(135deg, #7c3aed, #ec4899);
      color:#fff;
      font-weight:700;
      box-shadow:0 20px 34px rgba(124,58,237,.24);
    }
    .twofactor-btn:hover{
      color:#fff;
      transform:translateY(-1px);
    }
    .twofactor-note{
      margin-top:1rem;
      color:#94a3b8;
      text-align:center;
    }
    @media (max-width: 575.98px){
      .twofactor-card{
        padding:1.2rem;
        border-radius:22px;
      }
      .twofactor-title{
        font-size:1.5rem;
      }
    }
  </style>
@endpush

@section('app')
  <div class="container py-4 py-lg-5">
    <div class="twofactor-shell">
      <div class="twofactor-card">
        <span class="twofactor-kicker">{{ __('Secure Access') }}</span>
        <h5 class="twofactor-title">{{ __('Admin Verification') }}</h5>
        <p class="twofactor-text">{{ __('Enter your admin 2FA code to continue into the control panel securely.') }}</p>

        <div class="twofactor-info">
          <div class="twofactor-info-icon">
            <i class="fas fa-shield-alt"></i>
          </div>
          <div>
            <strong>{{ __('Short trusted session') }}</strong>
            <p>{{ __('You will stay verified for 10 minutes on this device after a successful confirmation.') }}</p>
          </div>
        </div>

        <form method="POST" action="{{ route('2fa.verify') }}">
          @csrf

          <div>
            <label class="twofactor-label">{{ __('2FA Code') }}</label>
            <div class="twofactor-input-shell">
              <i class="fas fa-key"></i>
              <input
                type="password"
                name="code"
                autocomplete="one-time-code"
                class="form-control twofactor-input @error('code') is-invalid @enderror"
                placeholder="{{ __('Enter verification code') }}"
                autofocus
              >
            </div>
            @error('code')
              <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
            @enderror
          </div>

          <button class="btn twofactor-btn" type="submit">
            {{ __('Verify & Continue') }}
          </button>
        </form>

        <div class="small twofactor-note">
          {{ __('Need a fresh code? Open your authenticator app and use the latest entry for this account.') }}
        </div>
      </div>
    </div>
  </div>
@endsection
