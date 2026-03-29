@extends('layouts.app')

@push('css')
<style>
  .balance-page-shell {
    padding-top: 0.5rem;
    padding-bottom: 1rem;
  }

  .balance-page-hero {
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1.25rem;
    padding: 2rem 2rem 1.8rem;
    margin-bottom: 1.35rem;
    overflow: hidden;
    border-radius: 30px;
    border: 1px solid rgba(129, 140, 248, 0.2);
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.1);
  }

  .balance-page-hero::before,
  .balance-page-hero::after {
    content: "";
    position: absolute;
    border-radius: 999px;
    pointer-events: none;
  }

  .balance-page-hero::before {
    width: 240px;
    height: 240px;
    top: -120px;
    right: -60px;
    background: radial-gradient(circle, rgba(96, 165, 250, 0.3), transparent 70%);
  }

  .balance-page-hero::after {
    width: 180px;
    height: 180px;
    bottom: -90px;
    left: -20px;
    background: radial-gradient(circle, rgba(79, 70, 229, 0.2), transparent 72%);
  }

  .balance-page-hero--sender {
    background:
      linear-gradient(135deg, rgba(79, 70, 229, 0.96), rgba(14, 165, 233, 0.88)),
      linear-gradient(180deg, #ffffff, #f8fafc);
    color: #fff;
  }

  .balance-page-hero__kicker {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.85rem;
    margin-bottom: 0.95rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.16);
    color: rgba(255, 255, 255, 0.92);
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .balance-page-hero h4 {
    margin: 0;
    font-size: 2rem;
    font-weight: 900;
    color: #fff;
  }

  .balance-page-hero p {
    max-width: 760px;
    margin: 0.8rem 0 0;
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.75;
  }

  .balance-page-hero__meta {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    gap: 0.7rem;
    justify-content: flex-end;
    margin-left: auto;
  }

  .balance-page-hero__pill {
    display: inline-flex;
    align-items: center;
    padding: 0.7rem 1rem;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.18);
    color: #fff;
    font-weight: 700;
    backdrop-filter: blur(12px);
  }

  @media (max-width: 767.98px) {
    .balance-page-hero {
      padding: 1.35rem;
      flex-direction: column;
      border-radius: 24px;
    }

    .balance-page-hero h4 {
      font-size: 1.55rem;
    }

    .balance-page-hero__meta {
      justify-content: flex-start;
      margin-left: 0;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  window.addEventListener('open-confirm-modal', () => {
    const el = document.getElementById('confirmModal');
    if (!el) return;
    const modal = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(el) : null;
    modal && modal.show();
  });
  window.addEventListener('close-confirm-modal', () => {
    const el = document.getElementById('confirmModal');
    if (!el) return;
    const modal = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(el) : null;
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

  <div class="container-fluid balance-page-shell">
    <section class="balance-page-hero balance-page-hero--sender">
      <div>
        <span class="balance-page-hero__kicker">{{ __('Balance Register') }}</span>
        <h4>{{ __('Registers (Sender)') }}</h4>
        <p>{{ __('Review sender register balances, inspect ledger details, and launch top-up or transfer-back actions from a single control surface.') }}</p>
      </div>

      <div class="balance-page-hero__meta">
        <span class="balance-page-hero__pill">{{ __('Currency') }}: USD</span>
        <span class="balance-page-hero__pill">{{ __('Ledger Scope') }}: {{ __('Sender Registers') }}</span>
      </div>
    </section>

    @livewire('balance.sender-balance-livewire')
  </div>
@endsection
