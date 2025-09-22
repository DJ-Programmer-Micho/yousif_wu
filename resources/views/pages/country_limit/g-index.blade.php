@extends('layouts.app')
@push('css')
<link rel="stylesheet" href="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
<link rel="stylesheet" href="{{ asset('assets/js/utils/country_select/country_select.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/js/utils/country_select/countrySelect.min.js') }}"></script>
<script src="{{ asset('assets/js/utils/teleSelect/intlTelInput.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('General Country Limit Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Utilities') }}</li>
                            <li class="breadcrumb-item text-muted active">{{ __('Countries') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('General Country Limit') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('country.general-country-limit-livewire')
    </div>
@endsection