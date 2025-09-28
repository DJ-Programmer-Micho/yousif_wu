@extends('layouts.app')
@push('css')
<style>
  .profile-page .card { border-radius: .75rem; }

.avatar-wrap { width:76px;height:76px }
.avatar-overlay{
    position:absolute; inset:auto 0 0 0; margin:auto;
    transform:translateY(50%); opacity:0; transition:all .2s ease;
    padding:.15rem .5rem; font-size:.75rem;
  }
.avatar-wrap:hover .avatar-overlay{ opacity:1; transform:translateY(0) }
</style>
@endpush
@push('scripts')
@endpush
@section('app')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ __('Profile Section') }}</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item text-muted active">{{ __('Application') }}</li>
                            <li class="breadcrumb-item text-muted active">{{ __('Profile') }}</li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">{{ __('Profile Viewer') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-3">
        @livewire('general.profile-livewire')
    </div>
@endsection
