<div class="modern-card">

  <div class="modern-header text-white">
      <div class="header-content">
          <div class="row align-items-center">
              <div class="col-lg-8">
                  <div class="d-flex align-items-center mb-3">
                      <div class="avatar-modern  bg-opacity-20 mx-2">
                          <i class="fas fa-check"></i>
                      </div>
                      <div>
                          <h3 class="mb-1 fw-bold">{{ __('Executed Transfers') }}</h3>
                          <p class="mb-0 opacity-75">{{ __('Manage and track all Executed money transfers') }}</p>
                      </div>
                  </div>
              </div>
              <div class="col-lg-4">
                  <div class="stats-card">
                      <div class="d-flex align-items-center justify-content-between">
                          <div>
                              <div class="small opacity-75 mb-1">{{ __('Total Executed') }}</div>
                              <div class="h4 mb-0 fw-bold">{{ $allsenders }}</div>
                              <div class="small opacity-75">
                                  <span class="status-indicator bg-warning"></span>
                                  {{ __('Active transfers') }}
                              </div>
                          </div>
                          <div class="avatar-modern bg-opacity-20">
                              <i class="fas fa-check-double"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <div class="row g-3 align-items-end">

        {{-- Search --}}
        <div class="col-md-6">
          <label class="form-label mb-1">{{ __('Search') }}</label>
          <input type="text" class="form-control modern-input"
                 placeholder="{{ __('MTCN / Name / Phone…') }}"
                 wire:model.debounce.400ms="q">
        </div>

        {{-- Country (Select2, value=en_name) --}}
        <div class="col-md-4">
          <label class="form-label mb-1">{{ __('Country') }}</label>
          <div class="form-control p-0 border-0" wire:ignore>
            <input type="hidden" id="pending_country_wire" wire:model="country">
            <select id="pendingCountrySelect" class="form-control" data-placeholder="{{ __('All countries') }}">
              <option value="">{{ __('All countries') }}</option>
              @foreach($this->availableCountries as $c)
                <option value="{{ $c['en_name'] }}"
                        data-flag="{{ app('cloudfrontflagsx2').'/'.$c['flag_path'] }}"
                        data-iso="{{ $c['iso_code'] }}"
                        data-ar="{{ $c['ar_name'] }}"
                        data-ku="{{ $c['ku_name'] }}">
                  {{ $c['label'] }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Optional: Clear filters button --}}
        <div class="col-md-2">
          <label class="form-label mb-1 d-block">&nbsp;</label>
          <button type="button" class="btn btn-outline-secondary w-100" wire:click="clearFilters">
            <i class="fas fa-broom me-1"></i> {{ __('Clear') }}
          </button>
        </div>

      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-center"><i class="fas fa-calendar text-primary mx-1"></i>{{ __('Date') }}</th>
              <th class="text-center"><i class="fas fa-hashtag text-primary mx-1"></i>{{ __('MTCN') }}</th>
              <th class="text-center"><i class="fas fa-user text-primary mx-1"></i>{{ __('Sender') }}</th>
              <th class="text-center"><i class="fas fa-flag text-primary mx-1"></i>{{ __('Country') }}</th>
              <th class="text-center"><i class="fas fa-dollar-sign text-success mx-1"></i>{{ __('Amount') }}</th>
              <th class="text-center"><i class="fas fa-percent text-warning mx-1"></i>{{ __('Fee') }}</th>
              <th class="text-center"><i class="fas fa-calculator text-info mx-1"></i>{{ __('Total') }}</th>
              <th class="text-center"><i class="fas fa-user-check text-primary mx-1"></i>{{ __('Receiver') }}</th>
              @if ($this->isAdmin)
                <th class="text-center"><i class="fas fa-id-badge text-primary mx-1"></i>{{ __('Register') }}</th>
              @endif
              <th class="text-center"><i class="fas fa-cogs text-secondary mx-1"></i>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
          @forelse($rows as $r)
            @php
              $amt = (float) $r->amount;
              $amtClass = $amt > 1000 ? 'amount-display-g'
                        : ($amt >= 201 ? 'amount-display-b' : 'amount-display-r');

              $sInit = trim(($r->first_name[0] ?? '').($r->last_name[0] ?? ''));
              $rInit = trim(($r->r_first_name[0] ?? '').($r->r_last_name[0] ?? ''));
            @endphp
            <tr>
              <td class="text-nowrap text-center">
                <div class="badge badge-modern bg-light text-dark">
                  {{ $r->created_at?->tz('Asia/Baghdad')->format('Y-m-d') }}
                </div>
              </td>

              <td class="text-nowrap text-center">
                <div class="badge badge-modern bg-opacity-10 text-success" style="font-size: 12px">
                  <b>{{ $this->formatMtcn($r->mtcn) }}</b>
                </div>
              </td>

              <td class="text-start">
                <div class="d-inline-flex align-items-center gap-2">
                  <div class="avatar-modern bg-success bg-opacity-10 text-white">{{ $sInit !== '' ? $sInit : '—' }}</div>
                  <div class="text-start">
                    <div class="fw-semibold" style="font-size:14px">
                      {{ trim(($r->first_name ?? '').' '.($r->last_name ?? '')) ?: '—' }}
                    </div>
                    <small class="text-muted">{{ $r->phone ?: '—' }}</small>
                  </div>
                </div>
              </td>

              <td class="text-center">
                <div class="badge badge-modern bg-light text-dark text-center">
                      @php
                        $countryData = collect($this->availableCountries)->firstWhere('en_name', $r->country);
                        $flagUrl = $countryData ? app('cloudfrontflagsx2').'/'.$countryData['flag_path'] : null;
                      @endphp
                      @if($flagUrl)
                        <img src="{{ $flagUrl }}" alt="{{ $r->country }}" style="height: 12px;">
                      @endif
                  {{ $this->countryMap[$r->country] ?? $r->country }}
                </div>
              </td>

              <td class="text-center {{ $amtClass }}"><span style="font-size: 14px">$&nbsp;{{ number_format((float)$r->amount, 2) }}</span></td>
              <td class="text-center {{ $amtClass }}"><span style="font-size: 14px">$&nbsp;{{ number_format((float)$r->tax, 2) }}</span></td>
              <td class="text-center {{ $amtClass }} fw-semibold"><span style="font-size: 14px">$&nbsp;{{ number_format((float)$r->total, 2) }}</span></td>

              <td class="text-start">
                <div class="d-inline-flex align-items-center gap-2">
                  <div class="avatar-modern bg-success bg-opacity-10 text-white">{{ $rInit !== '' ? $rInit : '—' }}</div>
                  <div class="text-start">
                    <div class="fw-semibold" style="font-size:14px">
                      {{ trim(($r->r_first_name ?? '').' '.($r->r_last_name ?? '')) ?: '—' }}
                    </div>
                    <small class="text-muted">{{ $r->r_phone ?: '—' }}</small>
                  </div>
                </div>
              </td>

              @if ($this->isAdmin)
                <td class="text-center text-nowrap">
                  {{ optional($r->user)->name ?? '—' }}
                </td>
              @endif

              <td class="text-center text-nowrap">
                <div class="btn-group">
                  <a class="btn btn-sm btn-outline-primary" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Customer Receipt') }}"
                     href="{{ route('receipts.dompdf.senderShow', ['sender'=>$r->id, 'type'=>'customer']) }}">
                    <i class="far fa-copyright"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-secondary" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Register/Agent Receipt') }}"
                     href="{{ route('receipts.dompdf.senderShow', ['sender'=>$r->id, 'type'=>'agent']) }}">
                    <i class="far fa-registered"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-dark" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Both Receipts') }}"
                     href="{{ route('receipts.dompdf.senderShow', ['sender'=>$r->id, 'type'=>'both']) }}">
                    <i class="far fa-copy"></i>
                  </a>
                </div>
                  @if ($this->isAdmin)
                     @livewire('action.sender-action-livewire', ['senderId' => $r->id], key('s-'.$r->id))
                  @endif                
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ $this->isAdmin ? 10 : 9 }}" class="text-center text-muted py-4">
                {{ __('No Executed transfers found.') }}
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
      <div class="small text-muted">
        {{ __('Showing') }} {{ $rows->firstItem() }}–{{ $rows->lastItem() }} {{ __('of') }} {{ $rows->total() }}
      </div>
      <div>
        {{ $rows->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>