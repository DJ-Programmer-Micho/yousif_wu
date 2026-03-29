@include('components.forms._country-general-ui')

<div class="card country-admin-shell">
  <div class="country-admin-header is-danger">
    <div>
      <span class="country-admin-eyebrow">{{ __('Country Rules') }}</span>
      <h5 class="country-admin-title">{{ __('Blocked Destinations') }}</h5>
      <p class="country-admin-subtitle">{{ __('Manage the countries that must remain unavailable for transfer selection.') }}</p>
    </div>
    <div class="country-admin-badge-card">
      <span>{{ __('Blocked') }}</span>
      <strong>{{ $rules->total() }}</strong>
    </div>
  </div>

  <div class="country-admin-body">
    <div class="country-admin-toolbar">
      <div class="country-admin-toolbar-left">
        <button class="btn btn-primary" wire:click="createOpen">{{ __('Add Country') }}</button>
      </div>
      <div class="country-admin-toolbar-right">
        <input
          class="form-control country-admin-search"
          placeholder="{{ __('Search country...') }}"
          wire:model.debounce.400ms="search"
        >
        <select class="form-control country-admin-per-page" wire:model="perPage">
          <option>10</option>
          <option>25</option>
          <option>50</option>
        </select>
      </div>
    </div>

    @if (session('success'))
      <div class="alert alert-success country-admin-alert">{{ session('success') }}</div>
    @endif

    <div class="country-admin-table-wrap">
      <div class="table-responsive">
        <table class="table country-admin-table">
          <thead>
            <tr>
              <th style="width:72px;">#</th>
              <th>{{ __('Country') }}</th>
              <th>{{ __('Status') }}</th>
              <th class="text-right" style="width:220px;">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rules as $i => $row)
              <tr>
                <td>{{ $rules->firstItem() + $i }}</td>
                <td>
                  <div class="country-admin-country">
                    <img src="{{ app('cloudfrontflagsx2').'/'.$row->country->flag_path }}" class="country-admin-flag" alt="">
                    <div class="country-admin-country-text">
                      <span class="country-admin-country-name">{{ $row->country->en_name }}</span>
                    </div>
                  </div>
                </td>
                <td><span class="country-admin-pill country-admin-pill-danger">{{ __('Not allowed to transfer') }}</span></td>
                <td>
                  <div class="country-admin-action-group">
                    <button class="btn btn-outline-secondary btn-sm" wire:click="editOpen({{ $row->id }})">{{ __('View') }}</button>
                    <button class="btn btn-outline-danger btn-sm" wire:click="confirmDelete({{ $row->id }})">{{ __('Delete') }}</button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="country-admin-empty">{{ __('No countries are currently blocked.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="country-admin-note is-muted">
      {{ __('Blocked destinations are informationally simple but operationally strict: once listed here, they should no longer appear in normal transfer entry forms.') }}
    </div>

    <div class="mt-3">
      {{ $rules->links('pagination::bootstrap-4') }}
    </div>

    @include('components.forms.country-rule-create')
  </div>
</div>
