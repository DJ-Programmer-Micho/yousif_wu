@include('components.forms._country-general-ui')

<div class="card country-admin-shell">
  <div class="country-admin-header">
    <div>
      <span class="country-admin-eyebrow">{{ __('Country Limits') }}</span>
      <h5 class="country-admin-title">{{ __('Destination Limit Exceptions') }}</h5>
      <p class="country-admin-subtitle">{{ __('Manage country-specific minimum and maximum transfer limits without changing the fallback logic.') }}</p>
    </div>
    <div class="country-admin-badge-card">
      <span>{{ __('Active Rows') }}</span>
      <strong>{{ $limits->total() }}</strong>
    </div>
  </div>

  <div class="country-admin-body">
    <div class="country-admin-toolbar">
      <div class="country-admin-toolbar-left">
        <button class="btn btn-primary" wire:click="createOpen">{{ __('Add Country Limit') }}</button>
      </div>
      <div class="country-admin-toolbar-right">
        <input
          type="text"
          class="form-control country-admin-search"
          placeholder="{{ __('Search country') }}"
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

    @include('components.forms.country-limit-create')

    <div class="country-admin-table-wrap">
      <div class="table-responsive">
        <table class="table country-admin-table">
          <thead>
            <tr>
              <th style="width:72px;">#</th>
              <th>{{ __('Country') }}</th>
              <th class="text-right">{{ __('Minimum') }}</th>
              <th class="text-right">{{ __('Maximum') }}</th>
              <th class="text-right" style="width:220px;">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($limits as $i => $row)
              <tr>
                <td>{{ $limits->firstItem() + $i }}</td>
                <td>
                  <div class="country-admin-country">
                    <img src="{{ app('cloudfrontflagsx2').'/'.$row->country->flag_path }}" class="country-admin-flag" alt="">
                    <div class="country-admin-country-text">
                      <span class="country-admin-country-name">{{ $row->country->en_name }}</span>
                    </div>
                  </div>
                </td>
                <td class="text-right font-weight-bold">{{ number_format($row->min_value, 2) }}</td>
                <td class="text-right font-weight-bold">{{ number_format($row->max_value, 2) }}</td>
                <td>
                  <div class="country-admin-action-group">
                    <button class="btn btn-outline-secondary btn-sm" wire:click="editOpen({{ $row->id }})">{{ __('Edit') }}</button>
                    <button class="btn btn-outline-danger btn-sm" wire:click="confirmDeleteLimit({{ $row->id }})">{{ __('Delete') }}</button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="country-admin-empty">{{ __('No country limit exceptions yet. Every destination is currently using the general fallback values.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="country-admin-note">
      {{ __('Use destination exceptions only when a country must behave differently from the global limit configuration.') }}
    </div>

    <div class="mt-3">
      {{ $limits->links('pagination::bootstrap-4') }}
    </div>
  </div>
</div>
