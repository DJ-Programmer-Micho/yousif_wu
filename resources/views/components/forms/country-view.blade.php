<div class="row p-0">

  {{-- ========================== Form Group 1: Limits ========================== --}}
  <div class="col col-12 col-md-6 ">
    <div class="card mb-3 p-0">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5><b>{{ __('Transfer Limits') }}</b></h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <div class="small text-muted"><b>{{ __('All Countries (General Limit)') }}</b></div>
          <div class="h6 mb-0 text-info"><b>
            @if($generalLimit)
              $ {{ number_format((float)$generalLimit->min_value, 2) }}
              –
              $ {{ number_format((float)$generalLimit->max_value, 2) }}
            @else
              <span class="text-muted">{{ __('No general limit configured') }}</span>
            @endif
            </b>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th style="width:60px;">#</th>
                <th>{{ __('Country (Exception)') }}</th>
                <th class="text-right" style="width:160px;">{{ __('Min') }}</th>
                <th class="text-right" style="width:160px;">{{ __('Max') }}</th>
              </tr>
            </thead>
            <tbody>
            @forelse($limitExceptions as $i => $row)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                  <img src="{{ app('cloudfrontflagsx2').'/'.$row->country->flag_path }}" class="mr-1" style="height:12px">
                  {{ $row->country->en_name }}
                </td>
                <td class="text-right">$ {{ number_format((float)$row->min_value, 2) }}</td>
                <td class="text-right">$ {{ number_format((float)$row->max_value, 2) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-3">{{ __('No per-country exceptions. All countries use the General Limit.') }}</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
    {{-- ========================== Form Group 3: Banned ========================== --}}
  <div class="col col-12 col-md-6">
    <div class="card border-danger mb-3 p-0">
      <div class="card-header d-flex justify-content-between align-items-center" style="border-color:#dc3545;">
        <div class="d-flex align-items-center">
          <span class="badge badge-danger mr-2">{{ __('Caution') }}</span>
          <strong>{{ __('Banned / Not-Allowed Countries') }}</strong>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="alert alert-warning mb-0 rounded-0">
          <b>{{ __('These countries are currently blocked from selection and transactions. Informational only.') }}</b>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th style="width:60px;">#</th>
                <th>{{ __('Country') }}</th>
                <th style="width:180px;">{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
            @forelse($banned as $i => $row)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                  <img src="{{ app('cloudfrontflagsx2').'/'.$row->country->flag_path }}" class="mr-1" style="height:12px">
                  {{ $row->country->en_name }}
                </td>
                <td><span class="badge badge-danger">{{ __('Blocked') }}</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-3">{{ __('No banned countries.') }}</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  {{-- ========================== Form Group 2: Taxes =========================== --}}
  <div class="col col-12 ">
  <div class="card mb-3 p-0">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>{{ __('Transaction Fees') }}</strong>
    </div>

    <div class="card-body">

      {{-- General (default) tax --}}
      <div class="mb-2">
        <div class="small text-info"><b>{{ __('All Countries (General Tax)') }}</b></div>
      </div>

      <div class="table-responsive mb-3 border">
        <table class="table table-sm table-bordered mb-0" >
          <thead class="thead-light">
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
              <td class="text-center">$ {{ number_format((float)$min, 2) }}</td>
              <td class="text-center">$ {{ $max === null ? '∞' : number_format((float)$max, 2) }}</td>
              <td class="text-center">$ {{ number_format((float)$fee, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-info">{{ __('No general tax configured.') }}</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      {{-- Exceptions: grouped by Tax Bracket Set, with brackets + countries --}}
      <div class="mb-2">
        <div class="small text-info"><b>{{ __('Exceptions by Tax Bracket Set') }}</b></div>
      </div>

      @forelse($setBlocks as $block)
        <div class="card mb-2" style="border: 1px solid #cc0022">
          <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <strong>{{ $block['name'] }}</strong>
            <span class="badge badge-secondary">{{ $block['count'] }} countr{{ $block['count'] === 1 ? 'y' : 'ies' }}</span>
          </div>

          <div class="card-body">
            {{-- Set brackets --}}
            <div class="table-responsive mb-2">
              <table class="table table-sm table-bordered mb-0" >
                <thead class="thead-light">
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
                    <td class="text-center">$ {{ number_format((float)$bMin, 2) }}</td>
                    <td class="text-center">$ {{ $bMax === null ? '∞' : number_format((float)$bMax, 2) }}</td>
                    <td class="text-center">$ {{ number_format((float)$bFee, 2) }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-center text-muted">{{ __('No brackets configured for this set.') }}</td></tr>
                @endforelse
                </tbody>
              </table>
            </div>

            {{-- Countries under this set --}}
            <div class="table-responsive mb-0">
              <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('Country') }}</th>
                  </tr>
                </thead>
                <tbody>
                @forelse($block['countries'] as $i => $c)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                      <img src="{{ app('cloudfrontflagsx2').'/'.$c->flag_path }}" class="mr-1" style="height:12px">
                      {{ $c->en_name }}
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="2" class="text-center text-muted">{{ __('No countries assigned.') }}</td></tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @empty
        <div class="text-muted">{{ __('No country-specific tax assignments. All countries use the General Tax above.') }}</div>
      @endforelse

      <small class="text-muted d-block mt-2">
        {{ __('All countries not listed under a set use the') }} <b>{{ __('General Tax') }}</b>.
      </small>

    </div>
  </div>
  </div>
</div>
