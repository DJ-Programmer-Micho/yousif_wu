@if($country || ($limits && ($limits['min'] || $limits['max'])) || $brackets)
  <div class="card border-0 shadow-sm mt-2">
  <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold"><b>{{ __('Country Info') }}</b></h5>
    </div>
    <hr class="mt-0"/>
    <div class="card-body pt-0">
      {{-- Header: flag + country name (if selected) --}}
      @if($country)
        <div class="d-flex align-items-center mb-2">
          @php
            $flag = app('cloudfrontflagsx2').'/'.$country->flagx2_path;
          @endphp
          <img src="{{ $flag }}" alt="flag" style="height:16px" class="me-2">
          <span class="fw-semibold mx-2">
            {{ $country->en_name }}
            <small class="text-muted">({{ strtoupper($country->iso_code) }})</small>
          </span>
        </div>
      @endif

      {{-- Limits --}}
      @if($limits && ($limits['min'] !== null || $limits['max'] !== null))
        <div class="mb-3">
          <div class="small text-muted">{{ __('Transfer limits (USD)') }}</div>
          <div class="fw-medium">
            @if($limits['min'] !== null && $limits['max'] !== null)
              $ {{ number_format($limits['min'], 2) }} – $ {{ number_format($limits['max'], 2) }}
            @elseif($limits['min'] !== null)
              Min: $ {{ number_format($limits['min'], 2) }}
            @elseif($limits['max'] !== null)
              Max: $ {{ number_format($limits['max'], 2) }}
            @endif
          </div>
        </div>
      @endif

      {{-- Brackets table --}}
      @if(!empty($brackets))
        <div class="table-responsive">
          <table class="table table-sm table-bordered mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-center">{{ __('From (USD)') }}</th>
                <th class="text-center">{{ __('To (USD)') }}</th>
                <th class="text-center">{{ __('Fee (USD)') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($brackets as $row)
                @php [$bMin, $bMax, $bFee] = $row; @endphp
                <tr>
                  <td class="text-center">$ {{ number_format((float)$bMin, 2) }}</td>
                  <td class="text-center">$ {{ number_format((float)$bMax, 2) }}</td>
                  <td class="text-center"><b>$ {{ number_format((float)$bFee, 2) }}</b></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-muted small">{{ __('No brackets configured. Using defaults if applicable.') }}</div>
      @endif
    </div>
  </div>
@endif
