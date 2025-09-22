@extends('layouts.app')
@section('app')
  <div class="page-breadcrumb">
    <div class="row">
      <div class="col-12 align-self-center">
        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Admin Settings') }}</h4>
        <div class="d-flex align-items-center">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0 p-0">
              <li class="breadcrumb-item text-muted active">App</li>
              <li class="breadcrumb-item text-muted active">Settings</li>
              <li class="breadcrumb-item text-muted active" aria-current="page">Receiver Gate</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid mt-3">
    @livewire('general.setting-livewire')
  </div>
@endsection
