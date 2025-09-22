@extends('layouts.app')
@push('css')
@endpush
@push('scripts')
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('General Country Tax Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Utilities') }}</li>
                            <li class="breadcrumb-item text-muted active">{{ __('Countries') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('General Country Tax') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('country.general-country-tax-livewire')
    </div>
@endsection
