@extends('layouts.app')
@push('css')
<style>
    .webview-container {
        border: 1px solid #ccc;
        height: 100vh; /* Adjust height as needed */
        overflow: hidden; /* Hide scrollbars if you want */
    }
    iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
</style>
@endpush
@push('scripts')
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('MTCN Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Utilities') }}</li>
                            {{-- <li class="breadcrumb-item text-muted active">MTCN</li> --}}
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('MTCN') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        <div class="webview-container">
            <iframe src="https://www.westernunion.com/web/global-service/track-transfer"></iframe>
        </div>
    </div>
@endsection
