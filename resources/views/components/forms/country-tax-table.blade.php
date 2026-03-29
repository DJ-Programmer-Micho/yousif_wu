@include('components.forms._country-general-ui')

<div class="card country-admin-shell">
  <div class="country-admin-header">
    <div>
      <span class="country-admin-eyebrow">{{ __('Country Tax') }}</span>
      <h5 class="country-admin-title">{{ __('Tax Sets & Assignments') }}</h5>
      <p class="country-admin-subtitle">{{ __('Manage reusable tax bracket sets and assign them to specific countries while preserving the existing fallback behavior.') }}</p>
    </div>
    <div class="country-admin-badge-card">
      <span>{{ __('Assignments') }}</span>
      <strong>{{ $assignments->count() }}</strong>
    </div>
  </div>

  <div class="country-admin-body">
    @if (session('success'))
      <div class="alert alert-success country-admin-alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger country-admin-alert">{{ session('error') }}</div>
    @endif

    <div class="country-admin-grid">
      <div class="country-admin-section-card">
        <div class="country-admin-section-head">
          <div>
            <h6 class="country-admin-section-title">{{ __('Tax Bracket Sets') }}</h6>
            <p class="country-admin-section-subtitle">{{ __('Reusable fee ladders that can be linked to one or many destinations.') }}</p>
          </div>
          <button class="btn btn-primary btn-sm" wire:click="openCreateSet">{{ __('New Set') }}</button>
        </div>

        <div class="country-admin-table-wrap">
          <div class="table-responsive">
            <table class="table country-admin-table">
              <thead>
                <tr>
                  <th>{{ __('Name') }}</th>
                  <th>{{ __('Brackets') }}</th>
                  <th class="text-right" style="width:210px;">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($sets as $s)
                  @php $useCount = $setUsageCounts[$s->id] ?? 0; @endphp
                  <tr>
                    <td class="font-weight-bold">{{ $s->name }}</td>
                    <td><code class="country-admin-code">{{ json_encode($s->brackets_json) }}</code></td>
                    <td>
                      <div class="country-admin-action-group">
                        <button class="btn btn-outline-secondary btn-sm" wire:click="openEditSet({{ $s->id }})">{{ __('Edit') }}</button>
                        @if($useCount == 0)
                          <button class="btn btn-outline-danger btn-sm" wire:click="confirmDeleteSet({{ $s->id }})">{{ __('Delete') }}</button>
                        @else
                          <button
                            class="btn btn-outline-danger btn-sm"
                            disabled
                            title="Unassign from {{ $useCount }} countr{{ $useCount>1?'ies':'y' }} first"
                          >
                            {{ __('Delete') }}
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="country-admin-empty">{{ __('No tax bracket sets created yet.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="country-admin-section-card is-success">
        <div class="country-admin-section-head">
          <div>
            <h6 class="country-admin-section-title">{{ __('Assignments') }}</h6>
            <p class="country-admin-section-subtitle">{{ __('Each country can point to one set. Countries without an assignment continue using the general tax.') }}</p>
          </div>
          <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateAssign">{{ __('Assign') }}</button>
        </div>

        <div class="country-admin-table-wrap">
          <div class="table-responsive">
            <table class="table country-admin-table">
              <thead>
                <tr>
                  <th>{{ __('Country') }}</th>
                  <th>{{ __('Set') }}</th>
                  <th class="text-right" style="width:210px;">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($assignments as $a)
                  <tr>
                    <td>
                      <div class="country-admin-country">
                        <img src="{{ app('cloudfrontflagsx2').'/'.$a->country->flag_path }}" class="country-admin-flag" alt="">
                        <div class="country-admin-country-text">
                          <span class="country-admin-country-name">{{ $a->country->en_name }}</span>
                        </div>
                      </div>
                    </td>
                    <td><span class="country-admin-pill country-admin-pill-primary">{{ $a->set->name }}</span></td>
                    <td>
                      <div class="country-admin-action-group">
                        <button class="btn btn-outline-secondary btn-sm" wire:click="openEditAssign({{ $a->id }})">{{ __('Edit') }}</button>
                        <button class="btn btn-outline-danger btn-sm" wire:click="confirmDeleteAssign({{ $a->id }})">{{ __('Delete') }}</button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="country-admin-empty">{{ __('No country-specific assignments yet. All countries are currently falling back to the general tax.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    @include('components.forms.country-tax-create')
  </div>
</div>
