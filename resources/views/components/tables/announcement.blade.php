<div>
  {{-- Header + actions --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ __('Announcements') }}</h4>
    <button class="btn btn-primary btn-sm" wire:click="openCreate">
      <i class="fas fa-plus me-1"></i> {{ __('Add Announcement') }}
    </button>
  </div>

  {{-- Filters --}}
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label mb-1">{{ __('Search') }}</label>
          <input type="text" class="form-control" placeholder="{{ __('Type to search in text...') }}"
                 wire:model.debounce.400ms="q">
        </div>
        <div class="col-md-3">
          <label class="form-label mb-1">{{ __('Visibility') }}</label>
          <select class="form-select form-control" wire:model="visibleFilter">
            <option value="">{{ __('All') }}</option>
            <option value="1">{{ __('Shown') }}</option>
            <option value="0">{{ __('Hidden') }}</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">{{ __('Per Page') }}</label>
          <select class="form-select form-control" wire:model="perPage">
            <option>10</option>
            <option>15</option>
            <option>25</option>
            <option>50</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="card">
    <div class="table-responsive">
      @include('components.tables.announcement-table', ['rows' => $rows])
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
      <small class="text-muted">
        {{ __('Showing') }} {{ $rows->firstItem() ?: 0 }}–{{ $rows->lastItem() ?: 0 }} {{ __('of') }} {{ number_format($rows->total()) }}
      </small>
      <div>{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
  </div>

  {{-- Modal --}}
  <div class="modal fade @if($showModal) show d-block @endif" tabindex="-1" @if($showModal) style="background:rgba(0,0,0,.5);" @endif>
    <div class="modal-dialog modal-lg">
      <div class="modal-content" wire:ignore.self>
        <div class="modal-header">
          <h5 class="modal-title">
            <b>{{ $editId ? __('Edit Announcement') : __('Add Announcement') }}</b>
          </h5>
        </div>

        <form wire:submit.prevent="save">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">{{ __('Text') }}</label>
              <textarea rows="8" class="form-control @error('body') is-invalid @enderror" wire:model.defer="body"
                        placeholder="{{ __('Type the announcement here...') }}"></textarea>
              @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label d-block">{{ __('Visibility') }}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_visible" wire:model.defer="is_visible">
                  <label for="is_visible" class="form-check-label">
                    {{ $is_visible ? __('Shown') : __('Hidden') }}
                  </label>
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label">{{ __('Show From (optional)') }}</label>
                <input type="datetime-local" class="form-control @error('show_from') is-invalid @enderror"
                       wire:model.defer="show_from">
                @error('show_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-4">
                <label class="form-label">{{ __('Show Until (optional)') }}</label>
                <input type="datetime-local" class="form-control @error('show_until') is-invalid @enderror"
                       wire:model.defer="show_until">
                @error('show_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
</div>
