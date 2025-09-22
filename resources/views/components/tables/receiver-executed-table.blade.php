<div class="modern-card">
  <div class="modern-header text-white">
    <div class="header-content">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <div class="d-flex align-items-center mb-3">
            <div class="avatar-modern bg-opacity-20 mx-2">
              <i class="fas fa-check"></i>
            </div>
            <div>
              <h3 class="mb-1 fw-bold">{{ __('Executed Receivers') }}</h3>
              <p class="mb-0 opacity-75">{{ __('Manage and track all executed receiver entries') }}</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="small opacity-75 mb-1">{{ __('Total Executed') }}</div>
                <div class="h4 mb-0 fw-bold">{{ $allreceivers }}</div>
                <div class="small opacity-75">
                  <span class="status-indicator bg-success"></span>
                  {{ __('Executed') }}
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
        <div class="col-md-8">
          <label class="form-label mb-1">{{ __('Search') }}</label>
          <input type="text" class="form-control modern-input"
                 placeholder="{{ __('MTCN / Name / Phone / Address…') }}"
                 wire:model.debounce.400ms="q">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">{{ __('Per Page') }}</label>
          <select class="form-control" wire:model="perPage">
            <option>10</option><option>25</option><option>50</option>
          </select>
        </div>
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
              <th class="text-center"><i class="fas fa-user text-primary mx-1"></i>{{ __('Receiver') }}</th>
              <th class="text-center"><i class="fas fa-phone text-primary mx-1"></i>{{ __('Phone') }}</th>
              <th class="text-center"><i class="fas fa-coins text-success mx-1"></i>{{ __('Amount (IQD)') }}</th>
              @if ($this->isAdmin)
                <th class="text-center"><i class="fas fa-id-badge text-primary mx-1"></i>{{ __('Register') }}</th>
              @endif
              <th class="text-center"><i class="fas fa-cogs text-secondary mx-1"></i>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
          @forelse($rows as $r)
            @php
              $rInit = trim(($r->first_name[0] ?? '').($r->last_name[0] ?? ''));
              $amtClass = (int)$r->amount_iqd > 1_000_000 ? 'amount-display-g'
                        : ((int)$r->amount_iqd >= 200_000 ? 'amount-display-b' : 'amount-display-r');
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
                  <div class="avatar-modern bg-success bg-opacity-10 text-white">{{ $rInit !== '' ? $rInit : '—' }}</div>
                  <div class="text-start">
                    <div class="fw-semibold" style="font-size:14px">
                      {{ trim(($r->first_name ?? '').' '.($r->last_name ?? '')) ?: '—' }}
                    </div>
                    <small class="text-muted">{{ $r->address ?: '—' }}</small>
                  </div>
                </div>
              </td>
              <td class="text-center">{{ $r->phone ?: '—' }}</td>
              <td class="text-center {{ $amtClass }}"><span style="font-size: 14px">{{ number_format((int)$r->amount_iqd) }}</span></td>

              @if ($this->isAdmin)
                <td class="text-center text-nowrap">
                  {{ optional($r->user)->name ?? '—' }}
                </td>
              @endif

              <td class="text-center text-nowrap">
                <div class="btn-group">
                  <a class="btn btn-sm btn-outline-dark" target="_blank"
                     title="{{ __('Receipt') }}"
                     href="{{ route('receipts.receiver.dompdf.receiverShow', ['receiver'=>$r->id, 'type'=>'both']) }}">
                    <i class="far fa-file-pdf"></i>
                  </a>
                </div>
                  @if ($this->isAdmin)
                    @livewire('action.receiver-action-livewire', ['receiverId' => $r->id], key('r-'.$r->id))
                  @endif                
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ $this->isAdmin ? 7 : 6 }}" class="text-center text-muted py-4">
                {{ __('No executed receivers found.') }}
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
