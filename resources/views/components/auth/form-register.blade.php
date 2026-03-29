@php use Illuminate\Support\Facades\Storage; @endphp

<div class="modal fade register-modal @if($showModal) show d-block @endif" tabindex="-1" @if($showModal) style="background:rgba(15,23,42,.45);" @endif>
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content register-modal-content" wire:ignore.self>
      <div class="modal-header register-modal-header">
        <div>
          <span class="register-modal-kicker">{{ $editId ? __('Update Profile') : __('New Register') }}</span>
          <h5 class="modal-title">{{ $editId ? __('Edit Register') : __('Add Register') }}</h5>
          <p>{{ __('Keep access details, contact data, and location info organized in one place.') }}</p>
        </div>

        <button type="button" class="register-modal-close" wire:click="$set('showModal', false)" aria-label="{{ __('Close') }}">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form wire:submit.prevent="save">
        <div class="modal-body register-modal-body">
          <div class="register-form-stack">
            <div class="register-form-section">
              <h6 class="register-form-section-title">{{ __('Identity & Access') }}</h6>
              <p class="register-form-section-text">{{ __('Set the basic account identity and login state for this register.') }}</p>

              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="register-form-label">{{ __('Name') }}</label>
                  <input type="text" class="form-control register-form-control @error('name') is-invalid @enderror" wire:model.defer="name">
                  @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                  <label class="register-form-label">{{ __('Status') }}</label>
                  <select class="form-control register-form-control @error('status') is-invalid @enderror" wire:model.defer="status">
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                  </select>
                  @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                  <label class="register-form-label">{{ __('Email') }}</label>
                  <input type="email" class="form-control register-form-control @error('email') is-invalid @enderror" wire:model.defer="email">
                  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                  <label class="register-form-label">{{ __('Password') }}</label>
                  <input type="password" class="form-control register-form-control @error('password') is-invalid @enderror" wire:model.defer="password">
                  @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="register-form-section">
              <h6 class="register-form-section-title">{{ __('Contact Details') }}</h6>
              <p class="register-form-section-text">{{ __('Store direct communication details for faster follow-up and access recovery.') }}</p>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="register-form-label">{{ __('Phone') }}</label>
                  <input type="text" class="form-control register-form-control @error('phone') is-invalid @enderror" wire:model.defer="phone">
                  @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="register-form-section">
              <h6 class="register-form-section-title">{{ __('Location') }}</h6>
              <p class="register-form-section-text">{{ __('Capture the register location for reporting, routing, and administrative records.') }}</p>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="register-form-label">{{ __('Country') }}</label>
                  <input type="text" class="form-control register-form-control @error('country') is-invalid @enderror" wire:model.defer="country">
                  @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                  <label class="register-form-label">{{ __('State') }}</label>
                  <input type="text" class="form-control register-form-control @error('state') is-invalid @enderror" wire:model.defer="state">
                  @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                  <label class="register-form-label">{{ __('City') }}</label>
                  <input type="text" class="form-control register-form-control @error('city') is-invalid @enderror" wire:model.defer="city">
                  @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                  <label class="register-form-label">{{ __('Address') }}</label>
                  <input type="text" class="form-control register-form-control @error('address') is-invalid @enderror" wire:model.defer="address">
                  @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="register-form-section">
              <h6 class="register-form-section-title">{{ __('Avatar') }}</h6>
              <p class="register-form-section-text">{{ __('Upload a square profile image for the register account.') }}</p>

              <div class="register-avatar-panel">
                @if($avatarUpload)
                  <img src="{{ $avatarUpload->temporaryUrl() }}" class="register-avatar-preview" alt="{{ __('Avatar Preview') }}">
                @elseif($currentAvatar)
                  <img src="{{ Storage::disk('s3')->url($currentAvatar) }}" class="register-avatar-preview" alt="{{ __('Current Avatar') }}">
                @else
                  <div class="register-avatar-placeholder register-avatar-preview">
                    <i class="fas fa-user"></i>
                  </div>
                @endif

                <div class="flex-fill w-100">
                  <label class="register-form-label d-block">{{ __('Avatar (1:1)') }}</label>
                  <input type="file" class="form-control register-form-control @error('avatarUpload') is-invalid @enderror" wire:model="avatarUpload" accept="image/*">
                  @error('avatarUpload') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                  <div wire:loading wire:target="avatarUpload" class="small text-muted mt-2">
                    <i class="spinner-border spinner-border-sm mr-1"></i>{{ __('Uploading...') }}
                  </div>
                  <div class="form-text text-info mt-2">{{ __("We'll automatically crop to a square and optimize it.") }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer register-modal-footer">
          <button type="button" class="btn register-secondary-btn" wire:click="$set('showModal', false)">{{ __('Cancel') }}</button>
          <button type="submit" class="btn register-primary-btn" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Save') }}</span>
            <span wire:loading><i class="spinner-border spinner-border-sm mr-1"></i>{{ __('Saving...') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
