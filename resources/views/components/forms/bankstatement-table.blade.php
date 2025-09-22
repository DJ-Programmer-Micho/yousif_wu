<div class="modern-card">
  <div class="modern-header text-white">
    <div class="header-content">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="d-flex align-items-center mb-3">
            <div class="avatar-modern bg-opacity-20 mx-2">
              <i class="fas fa-{{ $this->tab === 'senders' ? 'paper-plane' : 'inbox' }}"></i>
            </div>
            <div>
              <h3 class="mb-1 fw-bold">{{ __('Bank Statement') }}</h3>
              <p class="mb-0 opacity-75">
                {{ $this->tab === 'senders' ? __('All senders by status') : __('All receivers') }}
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="row g-2">
            <!-- Statistics Cards -->
            <div class="col-md-3">
              <div class="stats-card-small">
                <div class="text-center">
                  <div class="h5 mb-0 fw-bold">{{ number_format($statistics['total'] ?? 0) }}</div>
                  <small class="opacity-75">{{ __('Total') }}</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card-small">
                <div class="text-center">
                  <div class="h5 mb-0 fw-bold">{{ number_format($statistics['today'] ?? 0) }}</div>
                  <small class="opacity-75">{{ __('Today') }}</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card-small">
                <div class="text-center">
                  <div class="h5 mb-0 fw-bold">{{ number_format($statistics['this_week'] ?? 0) }}</div>
                  <small class="opacity-75">{{ __('This Week') }}</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card-small">
                <div class="text-center">
                  <div class="h5 mb-0 fw-bold">{{ number_format($statistics['this_month'] ?? 0) }}</div>
                  <small class="opacity-75">{{ __('This Month') }}</small>
                </div>
              </div>
            </div>
          </div>
          <div class="text-right mt-4">
              <a class="btn btn-success btn-sm" target="_blank"
                  href="{{ route('bank-statement.export', array_merge(
                        ['tab' => $this->tab],
                        $this->getActiveFilters(),
                        ['sortBy' => $this->sortBy, 'sortDirection' => $this->sortDirection]
                      )) }}"
                      >
            <i class="fas fa-file-excel mr-1"></i> {{ __('Export Excel') }}
          </a>
          </div>
        </div>
      </div>

      {{-- Enhanced Tabs --}}
      <div class="d-flex justify-content-between align-items-center mt-3" wire:ignore.self>
        <ul class="nav nav-pills">
          <li class="nav-item mx-1">
            <a class="nav-link {{ $this->tab==='senders'?'active':'' }}" type="button"
               wire:click.prevent="setTab('senders')">
              <i class="fas fa-paper-plane mx-1"></i> 
              <span>{{ __('Senders') }}</span>
              @if($this->tab === 'senders' && isset($statistics['total']))
                <span class="badge bg-light text-dark ms-1">{{ number_format($statistics['total']) }}</span>
              @endif
            </a>
          </li>
          <li class="nav-item ms-2">
            <a class="nav-link {{ $this->tab==='receivers'?'active':'' }}" type="button"
               wire:click.prevent="setTab('receivers')" >
              <i class="fas fa-inbox mx-1"></i> 
              <span>{{ __('Receivers') }}</span>
              @if($this->tab === 'receivers' && isset($statistics['total']))
                <span class="badge bg-light text-dark ms-1">{{ number_format($statistics['total']) }}</span>
              @endif
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    {{-- Enhanced Filter Section --}}
    <div class="card-header bg-white">
      {{-- Basic Filters Row --}}
      <div class="row g-3 align-items-end mb-3">
        {{-- Search --}}
        <div class="col-12 @if ($this->tab === 'senders') col-md-3 @else col-md-6 @endif">
          <label class="form-label mb-1">{{ __('Search') }}</label>
          <div class="input-group">
            <span class="input-group-text bg-light">
              <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control modern-input"
                   placeholder="{{ $this->tab==='senders' ? __('MTCN / Name / Phone…') : __('Name / Phone / Address…') }}"
                   wire:model.debounce.400ms="q">
            @if($this->q)
              <button class="btn btn-outline-secondary" type="button" wire:click="$set('q', '')">
                <i class="fas fa-times"></i>
              </button>
            @endif
          </div>
        </div>

{{-- Status (both tabs) with counts --}}
<div class="col-12 col-md-3">
  <label class="form-label mb-1">{{ __('Status') }}</label>
  <div class="form-control p-0 border-0">
    <select class="form-select modern-select w-100" wire:model="status">
      @foreach($this->statusOptions as $opt)
        <option value="{{ $opt['value'] }}">
          {{ $opt['label'] }} ({{ number_format($opt['count']) }})
        </option>
      @endforeach
    </select>
  </div>
</div>


        {{-- Register (admin only) --}}
        @if ($this->isAdmin)
          <div class="col-12 @if ($this->tab === 'senders') col-md-3 @else col-md-6 @endif">
            <label class="form-label mb-1">{{ __('Register') }}</label>
            <div class="form-control p-0 border-0">
            <select class="form-select modern-select w-100" wire:model.number="registerId">
              <option value="0">{{ __('All') }}</option>
              @foreach($this->registerOptions as $u)
                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
              @endforeach
            </select>
            </div>
          </div>
        @endif

        {{-- Per Page --}}
        <div class="col-12 col-md-1 mt-1">
          <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="clearFilters">
            <i class="fas fa-broom me-1"></i> {{ __('Clear All') }}
          </button>
      </div>

      {{-- Advanced Filters Toggle --}}
      <div class="col-12 col-md-2 mt-1">
        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="toggleAdvancedFilters">
          <i class="fas fa-{{ $this->showAdvancedFilters ? 'minus' : 'plus' }} me-1"></i>
          {{ $this->showAdvancedFilters ? __('Hide Advanced Filters') : __('Show Advanced Filters') }}
        </button>
        
        <div class="d-flex gap-2">

        </div>
      </div>

      {{-- Advanced Filters Section --}}
      @if($this->showAdvancedFilters)
        <div class="advanced-filters mt-3 pt-3 border-top">
          <div class="row g-3">
            {{-- Quick Date Filter Buttons --}}
            <div class="col-12">
              <label class="form-label mb-2">{{ __('Quick Date Filters') }}</label>
              <div class="btn-group flex-wrap" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="setDateFilter('today')">{{ __('Today') }}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="setDateFilter('yesterday')">{{ __('Yesterday') }}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="setDateFilter('last_7_days')">{{ __('Last 7 Days') }}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="setDateFilter('last_30_days')">{{ __('Last 30 Days') }}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="setDateFilter('this_month')">{{ __('This Month') }}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="setDateFilter('last_month')">{{ __('Last Month') }}</button>
              </div>
            </div>

            {{-- Date Range --}}
            <div class="col-md-3">
              <label class="form-label mb-1">{{ __('Date From') }}</label>
              <input type="date" class="form-control modern-input" wire:model="dateFrom">
            </div>
            <div class="col-md-3">
              <label class="form-label mb-1">{{ __('Date To') }}</label>
              <input type="date" class="form-control modern-input" wire:model="dateTo">
            </div>

            {{-- Amount Range --}}
            <div class="col-md-3">
              <label class="form-label mb-1">
                {{ $this->tab === 'senders' ? __('Amount From ($)') : __('Amount From (IQD)') }}
              </label>
              <input type="number" step="0.01" class="form-control modern-input" 
                     wire:model.debounce.400ms="amountFrom" placeholder="0.00">
            </div>
            <div class="col-md-3">
              <label class="form-label mb-1">
                {{ $this->tab === 'senders' ? __('Amount To ($)') : __('Amount To (IQD)') }}
              </label>
              <input type="number" step="0.01" class="form-control modern-input" 
                     wire:model.debounce.400ms="amountTo" placeholder="0.00">
            </div>
          </div>
        </div>
      @endif
    </div>

    {{-- TABLE --}}
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            @if ($this->tab === 'senders')
              <tr>
                <th class="text-center sortable" wire:click="sortBy('created_at')">
                  {{ __('Date') }}
                  @if($this->sortBy === 'created_at')
                    <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @endif
                </th>
                <th class="text-center">{{ __('MTCN') }}</th>
                <th class="text-center sortable" wire:click="sortBy('first_name')">
                  {{ __('Sender') }}
                  @if($this->sortBy === 'first_name')
                    <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @endif
                </th>
                <th class="text-center sortable" wire:click="sortBy('country')">
                  {{ __('Country') }}
                  @if($this->sortBy === 'country')
                    <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @endif
                </th>
                <th class="text-center sortable" wire:click="sortBy('amount')">
                  {{ __('Amount') }}
                  @if($this->sortBy === 'amount')
                    <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @endif
                </th>
                <th class="text-center sortable" wire:click="sortBy('tax')">
                  {{ __('Fee') }}
                  @if($this->sortBy === 'tax')
                    <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @endif
                </th>
                <th class="text-center sortable" wire:click="sortBy('total')">
                  {{ __('Total') }}
                  @if($this->sortBy === 'total')
                    <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @endif
                </th>
                <th class="text-center">{{ __('Receiver') }}</th>
                @if ($this->isAdmin)
                  <th class="text-center">{{ __('Register') }}</th>
                @endif
                <th class="text-center">{{ __('Actions') }}</th>
              </tr>
            @else
<tr>
  <th class="text-center sortable" wire:click="sortBy('created_at')">
    {{ __('Date') }}
    @if($this->sortBy === 'created_at')
      <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
    @endif
  </th>

  <th class="text-center sortable" wire:click="sortBy('status')">
    {{ __('Status') }}
    @if($this->sortBy === 'status')
      <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
    @endif
  </th>

  <th class="text-center sortable" wire:click="sortBy('first_name')">
    {{ __('Receiver') }}
    @if($this->sortBy === 'first_name')
      <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
    @endif
  </th>
  <th class="text-center">{{ __('Phone') }}</th>
  <th class="text-center">{{ __('Address') }}</th>
  <th class="text-center sortable" wire:click="sortBy('amount_iqd')">
    {{ __('Amount (IQD)') }}
    @if($this->sortBy === 'amount_iqd')
      <i class="fas fa-sort-{{ $this->sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
    @endif
  </th>
  @if ($this->isAdmin)
    <th class="text-center">{{ __('Register') }}</th>
  @endif
  <th class="text-center">{{ __('Actions') }}</th>
</tr>

            @endif
          </thead>

          <tbody>
          @forelse($rows as $r)
            @if ($this->tab === 'senders')
              @php
                $amt = (float) $r->amount;
                $amtClass = $amt > 1000 ? 'amount-display-g' : ($amt >= 201 ? 'amount-display-b' : 'amount-display-r');
                $sInit = trim(($r->first_name[0] ?? '').($r->last_name[0] ?? ''));
                $rInit = trim(($r->r_first_name[0] ?? '').($r->r_last_name[0] ?? ''));
                $statusClass = match($r->status) {
                  'Pending' => 'bg-warning text-dark',
                  'Executed' => 'bg-success text-white',
                  'Rejected' => 'bg-danger text-white',
                  default => 'bg-secondary text-white'
                };
              @endphp
              <tr class="table-row-hover">
                <td class="text-nowrap text-center">
                  <div class="badge badge-modern bg-light text-dark">
                    {{ $r->created_at?->tz('Asia/Baghdad')->format('Y-m-d') }}
                  </div>
                </td>
                  <td class="text-center align-middle">
                    <div class="d-inline-flex flex-column align-items-center">
                      <div class="badge badge-modern bg-opacity-10 text-primary" style="font-size:12px;">
                        <b>{{ $this->formatMtcn($r->mtcn) }}</b>
                      </div>
                      <span class="badge {{ $statusClass }} mt-1" style="border-radius:10px; font-size:11px;">
                        {{ __($r->status) }}
                      </span>
                    </div>
                  </td>
                <td class="text-start">
                  <div class="d-inline-flex align-items-center gap-2">
                    <div class="avatar-modern bg-primary bg-opacity-10 text-white">{{ $rInit !== '' ? $rInit : '—' }}</div>
                    <div class="text-start">
                      <div class="fw-semibold" style="font-size:14px">
                        {{ trim(($r->first_name ?? '').' '.($r->last_name ?? '')) ?: '—' }}
                      </div>
                      <small class="text-muted d-flex align-items-center">
                        {{ $r->phone ?: '—' }}
                      </small>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  @if(isset($this->countryMap[$r->country]))
                    <div class="badge badge-modern bg-light text-dark d-flex align-items-center justify-content-center gap-1">
                      @php
                        $countryData = collect($this->availableCountries)->firstWhere('en_name', $r->country);
                        $flagUrl = $countryData ? app('cloudfrontflagsx2').'/'.$countryData['flag_path'] : null;
                      @endphp
                      @if($flagUrl)
                        <img src="{{ $flagUrl }}" alt="{{ $r->country }}" style="height: 12px;">
                      @endif
                      {{ $this->countryMap[$r->country] }}
                    </div>
                  @else
                    <div class="badge badge-modern bg-light text-dark">{{ $r->country }}</div>
                  @endif
                </td>

                {{-- ✅ FIXED AMOUNT / FEE / TOTAL CELLS --}}
                <td class="text-center {{ $amtClass }}">
                  <span class="fw-bold" style="font-size:14px">
                    ${{ number_format((float)$r->amount, 2) }}
                  </span>
                </td>
                <td class="text-center {{ $amtClass }}">
                  <span style="font-size:14px">
                    ${{ number_format((float)$r->tax, 2) }}
                  </span>
                </td>
                <td class="text-center {{ $amtClass }} fw-semibold"><span style="font-size: 14px">$&nbsp;{{ number_format((float)$r->total, 2) }}</span></td>
                {{-- ✅ END FIX --}}

                <td class="text-start">
                  <div class="d-inline-flex align-items-center gap-2">
                    <div class="avatar-modern bg-primary bg-opacity-10 text-white">{{ $rInit !== '' ? $rInit : '—' }}</div>
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

              <td class="text-center ">
                <div class="btn-group">
                  @if ($r->status == "Executed")
                  <a class="btn btn-sm btn-outline-primary" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="Customer Receipt"
                     href="{{ route('receipts.dompdf.senderShow', ['sender'=>$r->id, 'type'=>'customer']) }}">
                    <i class="far fa-copyright"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-secondary" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="Register/Agent Receipt"
                     href="{{ route('receipts.dompdf.senderShow', ['sender'=>$r->id, 'type'=>'agent']) }}">
                    <i class="far fa-registered"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-dark" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="Both Receipts"
                     href="{{ route('receipts.dompdf.senderShow', ['sender'=>$r->id, 'type'=>'both']) }}">
                    <i class="far fa-copy"></i>
                  </a>
                  @else
                  <a class="btn btn-sm btn-outline-primary" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="Customer Receipt"
                     href="{{ route('receipts.dompdf.show', ['sender'=>$r->id, 'type'=>'customer']) }}">
                    <i class="far fa-copyright"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-secondary" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="Register/Agent Receipt"
                     href="{{ route('receipts.dompdf.show', ['sender'=>$r->id, 'type'=>'agent']) }}">
                    <i class="far fa-registered"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-dark" target="_blank"
                     data-bs-toggle="tooltip" data-bs-placement="top" title="Both Receipts"
                     href="{{ route('receipts.dompdf.show', ['sender'=>$r->id, 'type'=>'both']) }}">
                    <i class="far fa-copy"></i>
                  </a>
                  @endif
                </div>
                  
                    @if ($this->isAdmin)
                      @livewire('action.sender-action-livewire', ['senderId' => $r->id], key('s-'.$r->id))
                    @endif
                  

              </td>
              </tr>
            @else
              @php
                $rInit = trim(($r->first_name[0] ?? '').($r->last_name[0] ?? ''));
                $amountIqd  = (float) $r->amount_iqd;
                $amountClass = $amountIqd > 1_000_000 ? 'amount-display-g'
                              : ($amountIqd >= 500_000 ? 'amount-display-b' : 'amount-display-r');
                $statusClass = match($r->status) {
                  'Pending'  => 'bg-warning text-dark',
                  'Executed' => 'bg-success text-white',
                  'Rejected' => 'bg-danger text-white',
                  default    => 'bg-secondary text-white'
                };
              @endphp
                <tr class="table-row-hover">
                  <td class="text-nowrap text-center">
                    <div class="badge badge-modern bg-light text-dark">
                      {{ $r->created_at?->tz('Asia/Baghdad')->format('Y-m-d') }}
                    </div>
                  </td>

                  {{-- NEW: Status --}}
                  <td class="text-center align-middle">
                    <div class="d-inline-flex flex-column align-items-center">
                      <div class="badge badge-modern bg-opacity-10 text-primary" style="font-size:12px;">
                        <b>{{ $this->formatMtcn($r->mtcn) }}</b>
                      </div>
                      <span class="badge {{ $statusClass }} mt-1" style="border-radius:10px; font-size:11px;">
                        {{ __($r->status) }}
                      </span>
                    </div>
                  </td>

                  <td class="text-start">
                    <div class="d-inline-flex align-items-center gap-2">
                      <div class="avatar-modern bg-primary bg-opacity-10 text-white">{{ $rInit !== '' ? $rInit : '—' }}</div>
                      <div class="text-start">
                        <div class="fw-semibold" style="font-size:14px">
                          {{ trim(($r->first_name ?? '').' '.($r->last_name ?? '')) ?: '—' }}
                        </div>
                      </div>
                    </div>
                  </td>
                <td class="text-center">
                  @if($r->phone)
                    <div class="d-flex align-items-center justify-content-center">
                      <span>{{ $r->phone }}</span>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($r->address)
                    <div class="text-truncate text-center" title="{{ $r->address }}">
                      <i class="fas fa-map-marker-alt text-muted me-1"></i>
                      {{ $r->address }}
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-center {{ $amountClass }}">
                  <span class="">
                    {{ number_format($amountIqd, 0) }} {{ __('IQD') }}
                  </span>
                </td>
                @if ($this->isAdmin)
                  <td class="text-center text-nowrap">
                    <div class="badge badge-modern bg-secondary text-white">
                      {{ optional($r->user)->name ?? '—' }}
                    </div>
                  </td>
                @endif
                <td class="text-center ">
                  <div class="btn-group">
                    <a class="btn btn-sm btn-outline-primary" target="_blank"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Customer Receipt"
                      href="{{ route('receipts.receiver.dompdf.show', ['receiver'=>$r->id, 'type'=>'customer']) }}">
                      <i class="far fa-copyright"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-secondary" target="_blank"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Register/Agent Receipt"
                      href="{{ route('receipts.receiver.dompdf.show', ['receiver'=>$r->id, 'type'=>'agent']) }}">
                      <i class="far fa-registered"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-dark" target="_blank"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Both Receipts"
                      href="{{ route('receipts.receiver.dompdf.show', ['receiver'=>$r->id, 'type'=>'both']) }}">
                      <i class="far fa-copy"></i>
                    </a>
                  </div>  
                  @if ($this->isAdmin)
                    @livewire('action.receiver-action-livewire', ['receiverId' => $r->id], key('r-'.$r->id))
                  @endif
                </td>
              </tr>
            @endif
          @empty
            <tr>
              <td colspan="{{ $this->tab==='senders' ? ($this->isAdmin ? 10 : 9) : ($this->isAdmin ? 6 : 5) }}"
                  class="text-center text-muted py-5">
                <div class="d-flex flex-column align-items-center">
                  <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                  <h5 class="text-muted">{{ __('No records found') }}</h5>
                  <p class="text-muted">{{ __('Try adjusting your filters or search criteria') }}</p>
                  @if($this->q || $this->status || $this->country || $this->registerId || $this->dateFrom || $this->dateTo || $this->amountFrom || $this->amountTo)
                    <button type="button" class="btn btn-outline-primary btn-sm" wire:click="clearFilters">
                      <i class="fas fa-broom me-1"></i> {{ __('Clear All Filters') }}
                    </button>
                  @endif
                </div>
              </td>

            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>



  </div>
  </div>

  {{-- Loading Overlay --}}
  <div wire:loading class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
       style="background: rgba(255,255,255,0.8); z-index: 9999;">
    <div class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden"></span>
      </div>
      <div class="mt-2 text-muted"></div>
    </div>
  </div>
</div>
