@include('components.forms._country-general-ui')

<div class="card country-admin-shell">
  <div class="country-admin-header">
    <div>
      <span class="country-admin-eyebrow">{{ __('Country Overview') }}</span>
      <h5 class="country-admin-title">{{ __('Limits, Taxes, and Restrictions') }}</h5>
      <p class="country-admin-subtitle">{{ __('A read-only operational view of the general fallback settings, per-country exceptions, and blocked destinations.') }}</p>
    </div>
    <div class="country-admin-badge-card">
      <span>{{ __('Exception Sets') }}</span>
      <strong>{{ count($setBlocks) }}</strong>
    </div>
  </div>

  <div class="country-admin-body">
    <div class="country-admin-stat-grid">
      <div class="country-admin-stat">
        <span class="country-admin-stat-label">{{ __('General Limit') }}</span>
        <strong class="country-admin-stat-value">
          @if($generalLimit)
            {{ number_format((float) $generalLimit->min_value, 2) }} - {{ number_format((float) $generalLimit->max_value, 2) }}
          @else
            --
          @endif
        </strong>
      </div>
      <div class="country-admin-stat">
        <span class="country-admin-stat-label">{{ __('Limit Exceptions') }}</span>
        <strong class="country-admin-stat-value">{{ count($limitExceptions) }}</strong>
      </div>
      <div class="country-admin-stat">
        <span class="country-admin-stat-label">{{ __('Blocked Countries') }}</span>
        <strong class="country-admin-stat-value">{{ count($banned) }}</strong>
      </div>
    </div>

    <div class="country-admin-grid">
      <div class="country-admin-section-card">
        <div class="country-admin-section-head">
          <div>
            <h6 class="country-admin-section-title">{{ __('Transfer Limits') }}</h6>
            <p class="country-admin-section-subtitle">{{ __('Shows the default fallback range and any country-specific overrides.') }}</p>
          </div>
          <span class="country-admin-pill country-admin-pill-primary">{{ __('Read Only') }}</span>
        </div>

        <div class="country-admin-note">
          @if($generalLimit)
            {{ __('General limit for all countries without an exception:') }} {{ number_format((float)$generalLimit->min_value, 2) }} - {{ number_format((float)$generalLimit->max_value, 2) }}
          @else
            {{ __('No general limit configured.') }}
          @endif
        </div>

        <div class="country-admin-table-wrap mt-3">
          <div class="table-responsive">
            <table class="table country-admin-table">
              <thead>
                <tr>
                  <th style="width:72px;">#</th>
                  <th>{{ __('Country (Exception)') }}</th>
                  <th class="text-right">{{ __('Min') }}</th>
                  <th class="text-right">{{ __('Max') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($limitExceptions as $i => $row)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                      <div class="country-admin-country">
                        <img src="{{ app('cloudfrontflagsx2').'/'.$row->country->flag_path }}" class="country-admin-flag" alt="">
                        <div class="country-admin-country-text">
                          <span class="country-admin-country-name">{{ $row->country->en_name }}</span>
                        </div>
                      </div>
                    </td>
                    <td class="text-right font-weight-bold">{{ number_format((float)$row->min_value, 2) }}</td>
                    <td class="text-right font-weight-bold">{{ number_format((float)$row->max_value, 2) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="country-admin-empty">{{ __('No per-country exceptions. All countries use the general limit.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="country-admin-section-card is-danger">
        <div class="country-admin-section-head">
          <div>
            <h6 class="country-admin-section-title">{{ __('Blocked Countries') }}</h6>
            <p class="country-admin-section-subtitle">{{ __('Informational view of destinations currently excluded from transfer operations.') }}</p>
          </div>
          <span class="country-admin-pill country-admin-pill-danger">{{ __('Caution') }}</span>
        </div>

        <div class="country-admin-table-wrap">
          <div class="table-responsive">
            <table class="table country-admin-table">
              <thead>
                <tr>
                  <th style="width:72px;">#</th>
                  <th>{{ __('Country') }}</th>
                  <th>{{ __('Status') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($banned as $i => $row)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                      <div class="country-admin-country">
                        <img src="{{ app('cloudfrontflagsx2').'/'.$row->country->flag_path }}" class="country-admin-flag" alt="">
                        <div class="country-admin-country-text">
                          <span class="country-admin-country-name">{{ $row->country->en_name }}</span>
                        </div>
                      </div>
                    </td>
                    <td><span class="country-admin-pill country-admin-pill-danger">{{ __('Blocked') }}</span></td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="country-admin-empty">{{ __('No banned countries.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="country-admin-section-card mt-4">
      <div class="country-admin-section-head">
        <div>
          <h6 class="country-admin-section-title">{{ __('Transaction Fees') }}</h6>
          <p class="country-admin-section-subtitle">{{ __('General fee brackets are shown first, followed by each country-specific bracket set and its assigned destinations.') }}</p>
        </div>
        <span class="country-admin-pill country-admin-pill-muted">{{ __('Reference') }}</span>
      </div>

      <div class="country-admin-note">
        {{ __('All countries not listed under an assigned tax set use the General Tax below.') }}
      </div>

      <div class="country-admin-table-wrap mt-3">
        <div class="table-responsive">
          <table class="table country-admin-table">
            <thead>
              <tr>
                <th class="text-center">{{ __('From') }}</th>
                <th class="text-center">{{ __('To') }}</th>
                <th class="text-center">{{ __('Fee') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($generalBrackets as $r)
                @php [$min, $max, $fee] = $r; @endphp
                <tr>
                  <td class="text-center">{{ number_format((float)$min, 2) }}</td>
                  <td class="text-center">{{ $max === null ? 'INF' : number_format((float)$max, 2) }}</td>
                  <td class="text-center font-weight-bold">{{ number_format((float)$fee, 2) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="country-admin-empty">{{ __('No general tax configured.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-4">
        @forelse($setBlocks as $block)
          <div class="country-admin-section-card mb-3">
            <div class="country-admin-section-head">
              <div>
                <h6 class="country-admin-section-title">{{ $block['name'] }}</h6>
                <p class="country-admin-section-subtitle">{{ __('Countries using this set:') }} {{ $block['count'] }}</p>
              </div>
              <span class="country-admin-pill country-admin-pill-primary">{{ $block['count'] }} countr{{ $block['count'] === 1 ? 'y' : 'ies' }}</span>
            </div>

            <div class="country-admin-grid">
              <div>
                <div class="country-admin-table-wrap">
                  <div class="table-responsive">
                    <table class="table country-admin-table">
                      <thead>
                        <tr>
                          <th class="text-center">{{ __('From') }}</th>
                          <th class="text-center">{{ __('To') }}</th>
                          <th class="text-center">{{ __('Fee') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($block['brackets'] as $br)
                          @php [$bMin,$bMax,$bFee] = $br; @endphp
                          <tr>
                            <td class="text-center">{{ number_format((float)$bMin, 2) }}</td>
                            <td class="text-center">{{ $bMax === null ? 'INF' : number_format((float)$bMax, 2) }}</td>
                            <td class="text-center font-weight-bold">{{ number_format((float)$bFee, 2) }}</td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="3" class="country-admin-empty">{{ __('No brackets configured for this set.') }}</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div>
                <div class="country-admin-table-wrap">
                  <div class="table-responsive">
                    <table class="table country-admin-table">
                      <thead>
                        <tr>
                          <th style="width:72px;">#</th>
                          <th>{{ __('Country') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($block['countries'] as $i => $c)
                          <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                              <div class="country-admin-country">
                                <img src="{{ app('cloudfrontflagsx2').'/'.$c->flag_path }}" class="country-admin-flag" alt="">
                                <div class="country-admin-country-text">
                                  <span class="country-admin-country-name">{{ $c->en_name }}</span>
                                </div>
                              </div>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="2" class="country-admin-empty">{{ __('No countries assigned.') }}</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="country-admin-empty">{{ __('No country-specific tax assignments. All countries use the general tax above.') }}</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
