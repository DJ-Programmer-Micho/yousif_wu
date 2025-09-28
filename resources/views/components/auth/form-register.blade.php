  {{-- Modal --}}
  <div class="modal fade @if($showModal) show d-block @endif" tabindex="-1" @if($showModal) style="background:rgba(0,0,0,.5);" @endif>
    <div class="modal-dialog modal-lg">
      <div class="modal-content" wire:ignore.self>
        <div class="modal-header">
          <h5 class="modal-title">
            <b>{{ $editId ? __('Edit Register') : __('Add Register') }}</b>
          </h5>
          
        </div>

        <form wire:submit.prevent="save">
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-8 mb-1">
                <label class="form-label">{{ __('Name') }}</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-4 mb-1">
                <label class="form-label">{{ __('Status') }}</label>
                <select class="form-select form-control @error('status') is-invalid @enderror" wire:model.defer="status">
                  <option value=1>{{ __('Active') }}</option>
                  <option value=0>{{ __('Inactive') }}</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6 mb-1">
                <label class="form-label">{{ __('Email') }}</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6 mb-1">
                <label class="form-label">{{ $editId ? __('Password') : __('Password') }}</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model.defer="password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6 mb-1">
                  <label class="form-label">{{ __('Phone') }}</label>
                  <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model.defer="phone">
                  @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-1">
                </div>

              <div class="col-md-4 mb-1">
                <label class="form-label">{{ __('Country') }}</label>
                <input type="text" class="form-control @error('country') is-invalid @enderror" wire:model.defer="country">
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-4 mb-1">
                <label class="form-label">{{ __('State') }}</label>
                <input type="text" class="form-control @error('state') is-invalid @enderror" wire:model.defer="state">
                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-4 mb-1">
                <label class="form-label">{{ __('City') }}</label>
                <input type="text" class="form-control @error('city') is-invalid @enderror" wire:model.defer="city">
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-12 mb-3">
                <label class="form-label">{{ __('Address') }}</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" wire:model.defer="address">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              @php use Illuminate\Support\Facades\Storage; @endphp

              <div class="col-md-12 mb-1">
                <label class="form-label d-block">{{ __('Avatar (1:1)') }}</label>

                @if($avatarUpload)
                  <img src="{{ $avatarUpload->temporaryUrl() }}" class="rounded mb-2" style="width:100px;height:100px;object-fit:cover;">
                @elseif($currentAvatar)
                  <img src="{{ Storage::disk('s3')->url($currentAvatar) }}" class="rounded mb-2" style="width:100px;height:100px;object-fit:cover;">
                @endif

                <input type="file" class="form-control @error('avatarUpload') is-invalid @enderror" wire:model="avatarUpload" accept="image/*">
                @error('avatarUpload') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <div wire:loading wire:target="avatarUpload" class="small text-muted mt-1">
                  <i class="spinner-border spinner-border-sm"></i> {{ __('Uploading...') }}
                </div>
                <div class="form-text text-info">{{ __("We'll automatically crop to a square and optimize it.") }}</div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" wire:click="$set('showModal', false)">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
              <span wire:loading.remove>{{ __('Save') }}</span>
              <span wire:loading><i class="spinner-border spinner-border-sm me-1"></i>{{ __('Saving...') }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
