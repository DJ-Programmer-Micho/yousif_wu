@extends('layouts.app-clean')
@push('css')
    <link rel="stylesheet" href="{{ asset("assets/css/splash.css") }}">
@endpush
@section('app')
<div class="container py-4 mt-5">
  <form method="POST" action="{{ route('splash.save') }}">
    @csrf
    {{-- ===================== AGENCY ===================== --}}
    <div class="row">
      <div class="col-12 text-center mb-3">
        <h4 class="mb-0 text-white">Agency Operation</h4>
        <small class="text-white">Choose an agency</small>
      </div>

      {{-- Western Union (default selected) --}}
      <div class="col-md-4 mt-3 d-flex">
        <input
          id="agency_wu"
          type="radio"
          class="card-input sr-only"
          name="agency"
          value="western_union"
          {{ old('agency','western_union') === 'western_union' ? 'checked' : '' }}
        >
        <label for="agency_wu" class="card-option w-100">
          <div class="card text-center bg-dark h-100">
            <div class="card-body py-4">
              <h5 class="card-title text-white mb-3">Western Union</h5>
              <img src="https://www.westernunion.com/content/dam/wu/logo/logo.wu.big.svg"
                   alt="Western Union" class="mx-auto d-block" style="max-height:48px">
            </div>
          </div>
        </label>
      </div>

      {{-- Zain Cash --}}
      <div class="col-md-4 mt-3 d-flex">
        <input
          id="agency_zain"
          type="radio"
          class="card-input sr-only"
          name="agency"
          value="zain_cash"
          disabled
          {{ old('agency') === 'money_gram' ? 'checked' : '' }}
        >
        <label for="agency_zain" class="card-option w-100 is-disabled">
          <div class="card text-center bg-dark h-100">
            <div class="card-body py-4">
              <h5 class="card-title text-white mb-3">MoneyGram</h5>
              <img src="https://logos-world.net/wp-content/uploads/2023/02/MoneyGram-Logo.jpg"
                   alt="MoneyGram" class="mx-auto d-block" style="max-height:48px">
              <div class="small text-white-50 mt-2">Coming soon</div>
            </div>
          </div>
        </label>
      </div>

      {{-- Zain Cash --}}
      {{-- <div class="col-md-4 mt-3 d-flex">
        <input
          id="agency_zain"
          type="radio"
          class="card-input sr-only"
          name="agency"
          value="zain_cash"
          disabled
          {{ old('agency') === 'zain_cash' ? 'checked' : '' }}
        >
        <label for="agency_zain" class="card-option w-100 is-disabled">
          <div class="card text-center bg-dark h-100">
            <div class="card-body py-4">
              <h5 class="card-title text-white mb-3">Zain Cash</h5>
              <img src="https://vectorseek.com/wp-content/uploads/2024/05/Zain-Cash-Logo-Vector.svg-.png"
                   alt="Zain Cash" class="mx-auto d-block" style="max-height:48px">
            </div>
          </div>
        </label>
      </div> --}}

      {{-- FIB (disabled example) --}}
      {{-- <div class="col-md-4 mt-3 d-flex">
        <input
          id="agency_fib"
          type="radio"
          class="card-input sr-only"
          name="agency"
          value="fib"
          disabled
        >
        <label for="agency_fib" class="card-option w-100 is-disabled">
          <div class="card text-center bg-dark h-100">
            <div class="card-body py-4">
              <h5 class="card-title text-white mb-1">FIB</h5>
              <img src="https://fib.iq/wp-content/uploads/2025/05/FIB-desktop-icon.png"
                   alt="FIB" class="mx-auto d-block" style="max-height:48px">
              <div class="small text-white-50 mt-2">Coming soon</div>
            </div>
          </div>
        </label>
      </div> --}}
    </div>

    {{-- validation message (optional) --}}
    @error('agency')
      <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror

    {{-- ===================== LANGUAGE ===================== --}}
    <div class="row mt-5">
      <div class="col-12 text-center mb-3">
        <h4 class="mb-0 text-white">Language</h4>
        <small class="text-white">Choose interface language</small>
      </div>

      {{-- Arabic --}}
      <div class="col-md-4 mt-3 d-flex">
        <input
          id="lang_ar"
          type="radio"
          class="card-input sr-only"
          name="lang"
          value="ar"
          {{ old('lang','ar') === 'ar' ? 'checked' : '' }}
        >
        <label for="lang_ar" class="card-option w-100">
          <div class="card text-center bg-dark h-100">
            <div class="card-body py-4">
              <h5 class="card-title text-white mb-1">العربية</h5>
              <div class="text-white-50">AR</div>
            </div>
          </div>
        </label>
      </div>

      {{-- English --}}
      <div class="col-md-4 mt-3 d-flex">
        <input
          id="lang_en"
          type="radio"
          class="card-input sr-only"
          name="lang"
          value="en"
          {{ old('lang') === 'en' ? 'checked' : '' }}
        >
        <label for="lang_en" class="card-option w-100">
          <div class="card text-center bg-dark h-100">
            <div class="card-body py-4">
              <h5 class="card-title text-white mb-1">English</h5>
              <div class="text-white-50">EN</div>
            </div>
          </div>
        </label>
      </div>

      {{-- Kurdish (example third) --}}
      <div class="col-md-4 mt-3 d-flex">
        <input
          id="lang_ku"
          type="radio"
          class="card-input sr-only"
          name="lang"
          value="ku"
          {{ old('lang') === 'ku' ? 'checked' : '' }}
        >
        <label for="lang_ku" class="card-option w-100">
          <div class="card text-center bg-dark h-100">
            <div class="card-body py-4">
              <h5 class="card-title text-white mb-1">کوردی</h5>
              <div class="text-white-50">KU</div>
            </div>
          </div>
        </label>
      </div>
    </div>

    @error('lang')
      <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror

    {{-- Submit --}}
    <div class="text-center my-4">
      <button type="submit" class="btn btn-success px-4 py-2">
        Let's Go
      </button>
    </div>
  </form>
</div>
@endsection
