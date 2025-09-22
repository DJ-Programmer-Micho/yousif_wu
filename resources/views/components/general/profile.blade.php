<div class="profile-page">
  {{-- Top cover --}}
  <div class="card border-0 mb-3" style="background:linear-gradient(135deg,#667eea,#764ba2);">
    <div class="card-body py-4">
      <div class="d-flex align-items-center">
        {{-- Avatar --}}
        <div class="mr-3">
          <div class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center"
               style="width:76px;height:76px;">
            <span class="h3 m-0 text-primary">
              {{ strtoupper(substr($user->name ?? 'U',0,1)) }}
            </span>
          </div>
        </div>

        {{-- Name / role --}}
        <div class="text-white">
          <div class="d-flex align-items-center">
            <h4 class="mb-0 font-weight-bold">{{ $user->name ?? __('User') }}</h4>
            <span class="badge badge-light ml-2">{{ $roleLabel }}</span>
          </div>
          <div class="small opacity-75 mt-1">
            {{ __('Joined') }} {{ $joinedYear }} · <span class="text-nowrap">{{ $joinedHuman }}</span>
          </div>
        </div>

        {{-- Right side quick info --}}
        <div class="ml-auto text-right text-white">
          <div class="small mb-1">{{ __('Email') }}</div>
          <div class="font-weight-bold">{{ $user->email ?? '-' }}</div>
          @if(!empty($user->phone))
            <div class="small mt-2 mb-1">{{ __('Phone') }}</div>
            <div class="font-weight-bold">{{ $user->phone }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Stats row --}}
  <div class="row">
    {{-- Senders --}}
    <div class="col-md-6">
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small">{{ __('Executed Senders') }}</div>
              <div class="h4 mb-0 font-weight-bold">
                {{ number_format($sendersExecutedCount) }}
              </div>
            </div>
            <div class="text-right">
              <div class="text-muted small">{{ __('Total (USD)') }}</div>
              <div class="h4 mb-0 font-weight-bold">
                $ {{ number_format($sendersExecutedTotal, 2) }}
              </div>
            </div>
          </div>

          <div class="progress mt-3" style="height:8px;">
            @php
              $cap = max(1, $sendersExecutedCount + $receiversExecutedCount);
              $pct = round(($sendersExecutedCount / $cap) * 100);
            @endphp
            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $pct }}%;"></div>
          </div>
          <div class="small text-muted mt-1">{{ __('Share vs. total executed records') }}</div>
        </div>
      </div>
    </div>

    {{-- Receivers --}}
    <div class="col-md-6">
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small">{{ __('Executed Receivers') }}</div>
              <div class="h4 mb-0 font-weight-bold">
                {{ number_format($receiversExecutedCount) }}
              </div>
            </div>
            <div class="text-right">
              <div class="text-muted small">{{ __('Total (IQD)') }}</div>
              <div class="h4 mb-0 font-weight-bold">
                {{ number_format($receiversExecutedTotal, 0) }}
              </div>
            </div>
          </div>

          <div class="progress mt-3" style="height:8px;">
            @php
              $cap = max(1, $sendersExecutedCount + $receiversExecutedCount);
              $pct = round(($receiversExecutedCount / $cap) * 100);
            @endphp
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%;"></div>
          </div>
          <div class="small text-muted mt-1">{{ __('Share vs. total executed records') }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Recent activity --}}
  <div class="row">
    <div class="col-lg-6">
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
          <strong>{{ __('Recent Executed Senders') }}</strong>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="thead-light">
                <tr>
                  <th class="text-nowrap">{{ __('MTCN') }}</th>
                  <th class="text-right">{{ __('Total') }}</th>
                  <th class="text-nowrap">{{ __('Customer') }}</th>
                  <th class="text-nowrap">{{ __('Executed At') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentSenders as $s)
                  <tr>
                    <td class="text-nowrap">{{ $s['mtcn'] }}</td>
                    <td class="text-right">$ {{ number_format((float)$s['total'],2) }}</td>
                    <td class="text-nowrap">
                      {{ trim(($s['first_name'] ?? '').' '.($s['last_name'] ?? '')) }}
                    </td>
                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($s['updated_at'])->format('Y-m-d H:i') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-3">{{ __('No executed senders yet.Z') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
          <strong>{{ __('Recent Executed Receivers') }}</strong>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="thead-light">
                <tr>
                  <th class="text-nowrap">{{ __('MTCN') }}</th>
                  <th class="text-right">{{ __('Amount') }}</th>
                  <th class="text-nowrap">{{ __('Receiver') }}</th>
                  <th class="text-nowrap">{{ __('Executed At') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentReceivers as $r)
                  <tr>
                    <td class="text-nowrap">{{ $r['mtcn'] }}</td>
                    <td class="text-right">{{ number_format((float)$r['amount_iqd'],0) }} {{ __('IQD') }}</td>
                    <td class="text-nowrap">
                      {{ trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? '')) }}
                    </td>
                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($r['updated_at'])->format('Y-m-d H:i') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-3">{{ __('No executed receivers yet.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
