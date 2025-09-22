<div>
  {{-- Header + actions --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ __('Registers') }}</h4>
    <button class="btn btn-primary btn-sm" wire:click="openCreate">
      <i class="fas fa-plus me-1"></i> {{ __('Add Register') }}
    </button>
  </div>

  {{-- Filters --}}
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label mb-1">{{ __('Search') }}</label>
          <input type="text" class="form-control" placeholder="{{ __('Name / Email / Phone') }}"
                 wire:model.debounce.400ms="q">
        </div>
        <div class="col-md-3">
          <label class="form-label mb-1">{{ __('Status') }}</label>
          <select class="form-select form-control" wire:model="statusFilter">
            <option value="">{{ __('All') }}</option>
            <option value="1">{{ __('Active') }}</option>
            <option value="0">{{ __('Inactive') }}</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th></th>
            <th class="sortable" wire:click="sort('name')">
              {{ __('Name') }}
              @if($sortBy==='name') <i class="fas fa-sort-{{ $sortDirection==='asc'?'up':'down' }} ms-1"></i> @endif
            </th>
            <th class="sortable" wire:click="sort('email')">
              {{ __('Email') }}
              @if($sortBy==='email') <i class="fas fa-sort-{{ $sortDirection==='asc'?'up':'down' }} ms-1"></i> @endif
            </th>
            <th>{{ __('Phone') }}</th>
            <th class="sortable" wire:click="sort('status')">
              {{ __('Status') }}
              @if($sortBy==='status') <i class="fas fa-sort-{{ $sortDirection==='asc'?'up':'down' }} ms-1"></i> @endif
            </th>
            <th class="sortable" wire:click="sort('created_at')">
              {{ __('Created') }}
              @if($sortBy==='created_at') <i class="fas fa-sort-{{ $sortDirection==='asc'?'up':'down' }} ms-1"></i> @endif
            </th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $u)
            <tr>
              <td style="width:52px">
                @php $av = optional($u->profile)->avatar; @endphp
                @if($av)
                  <img src="{{ asset('storage/'.$av) }}" class="rounded-circle" alt="avatar" style="width:40px;height:40px;object-fit:cover;">
                @else
                  <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    <span class="text-muted">{{ strtoupper(mb_substr($u->name,0,1)) }}</span>
                  </div>
                @endif
              </td>
              <td>{{ $u->name }}</td>
              <td><a href="mailto:{{ $u->email }}">{{ $u->email }}</a></td>
              <td>{{ optional($u->profile)->phone ?: '—' }}</td>
              <td>
                <span class="badge {{ $u->status===1 ? 'bg-success':'bg-danger' }} text-white">
                  {{ $u->status===1 ? __('Active'):__('Inactive') }}
                </span>
              </td>
              <td>{{ $u->created_at?->format('Y-m-d') }}</td>
              <td class="text-end">
                <div class="btn-group">
                  <button class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $u->id }})" title="{{ __('Edit Register') }}">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-warning" wire:click="toggleStatus({{ $u->id }})" title="{{ __('Toggle status') }}">
                    <i class="fas fa-toggle-on"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger"
                          onclick="confirm('{{ __('Delete this register? This action cannot be undone. And All data will be lost!') }}') || event.stopImmediatePropagation()"
                          wire:click="deleteUser({{ $u->id }})">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-5">
                <i class="fas fa-inbox me-2"></i> {{ __('No registers found') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
      <small class="text-muted">
        {{ __('Showing') }} {{ $rows->firstItem() ?: 0 }}–{{ $rows->lastItem() ?: 0 }} {{ __('of') }} {{ number_format($rows->total()) }}
      </small>
      <div>{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
  </div>
  @include('components.auth.form-register')

</div>
