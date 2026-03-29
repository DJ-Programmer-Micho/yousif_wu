@once
  @push('css')
    <style>
      .register-admin-shell{
        display:flex;
        flex-direction:column;
        gap:1.5rem;
      }
      .register-hero-card,
      .register-filters-card,
      .register-table-card{
        border:1px solid rgba(255,255,255,.7);
        border-radius:24px;
        background:
          radial-gradient(circle at top right, rgba(236,72,153,.12), transparent 26%),
          radial-gradient(circle at left center, rgba(99,102,241,.14), transparent 34%),
          linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.92));
        box-shadow:0 24px 60px rgba(15,23,42,.08);
        backdrop-filter:blur(16px);
      }
      .register-hero-card{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1.5rem;
        padding:1.6rem 1.75rem;
      }
      .register-eyebrow{
        display:inline-flex;
        align-items:center;
        padding:.35rem .75rem;
        border-radius:999px;
        background:rgba(99,102,241,.12);
        color:#4338ca;
        font-size:.78rem;
        font-weight:800;
        letter-spacing:.04em;
        text-transform:uppercase;
      }
      .register-hero-title{
        margin:.8rem 0 .45rem;
        font-size:1.8rem;
        font-weight:800;
        color:#111827;
      }
      .register-hero-text{
        margin:0;
        max-width:640px;
        color:#64748b;
        line-height:1.7;
      }
      .register-hero-side{
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        gap:1rem;
        min-width:240px;
      }
      .register-stat-grid{
        display:grid;
        grid-template-columns:repeat(2, minmax(110px, 1fr));
        gap:.85rem;
        width:100%;
      }
      .register-stat-card{
        padding:.9rem 1rem;
        border-radius:18px;
        background:rgba(255,255,255,.72);
        border:1px solid rgba(148,163,184,.14);
        box-shadow:0 14px 28px rgba(15,23,42,.05);
      }
      .register-stat-card span{
        display:block;
        color:#64748b;
        font-size:.78rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.04em;
      }
      .register-stat-card strong{
        display:block;
        margin-top:.3rem;
        color:#0f172a;
        font-size:1.35rem;
        font-weight:800;
      }
      .register-add-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border:none;
        border-radius:16px;
        padding:.85rem 1.2rem;
        background:linear-gradient(135deg, #7c3aed, #ec4899);
        color:#fff;
        font-weight:700;
        box-shadow:0 18px 34px rgba(124,58,237,.24);
      }
      .register-add-btn:hover{
        color:#fff;
        transform:translateY(-1px);
      }
      .register-filters-card{
        padding:1.35rem 1.5rem 1.5rem;
      }
      .register-section-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:1rem;
        margin-bottom:1.1rem;
      }
      .register-section-head h5{
        margin:0 0 .3rem;
        font-size:1.05rem;
        font-weight:800;
        color:#111827;
      }
      .register-section-head p{
        margin:0;
        color:#64748b;
      }
      .register-filter-chip{
        display:inline-flex;
        align-items:center;
        padding:.45rem .8rem;
        border-radius:999px;
        background:rgba(16,185,129,.12);
        color:#047857;
        font-size:.8rem;
        font-weight:700;
        white-space:nowrap;
      }
      .register-label{
        display:block;
        margin-bottom:.5rem;
        color:#334155;
        font-weight:700;
      }
      .register-input-shell{
        display:flex;
        align-items:center;
        gap:.75rem;
        min-height:54px;
        padding:0 .95rem;
        border-radius:16px;
        border:1px solid rgba(148,163,184,.18);
        background:rgba(255,255,255,.82);
        box-shadow:inset 0 1px 0 rgba(255,255,255,.85);
      }
      .register-input-shell i{
        color:#8b5cf6;
        font-size:.95rem;
      }
      .register-input-shell .form-control{
        border:none;
        background:transparent;
        box-shadow:none;
        padding:0;
        height:auto;
        color:#111827;
      }
      .register-input-shell .form-control:focus{
        box-shadow:none;
      }
      .register-table-card{
        overflow:hidden;
      }
      .register-table-head,
      .register-table-footer{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        padding:1.2rem 1.5rem;
      }
      .register-table-head{
        border-bottom:1px solid rgba(226,232,240,.9);
      }
      .register-table-head h5{
        margin:0 0 .3rem;
        font-size:1.05rem;
        font-weight:800;
        color:#111827;
      }
      .register-table-head p{
        margin:0;
        color:#64748b;
      }
      .register-table-pill{
        display:inline-flex;
        align-items:center;
        padding:.48rem .85rem;
        border-radius:999px;
        background:rgba(99,102,241,.12);
        color:#4338ca;
        font-size:.8rem;
        font-weight:700;
      }
      .register-table-modern{
        margin:0;
      }
      .register-table-modern thead th{
        border:none;
        padding:1rem 1.1rem .9rem;
        font-size:.78rem;
        font-weight:800;
        letter-spacing:.05em;
        text-transform:uppercase;
        color:#64748b;
        background:rgba(248,250,252,.88);
        white-space:nowrap;
      }
      .register-table-modern tbody td{
        border-top:1px solid rgba(226,232,240,.7);
        padding:1rem 1.1rem;
        vertical-align:middle;
        color:#0f172a;
      }
      .register-table-modern tbody tr{
        transition:background .2s ease, transform .2s ease;
      }
      .register-table-modern tbody tr:hover{
        background:rgba(99,102,241,.035);
      }
      .register-sort-btn{
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        padding:0;
        border:none;
        background:transparent;
        color:inherit;
        font-weight:inherit;
        text-transform:inherit;
        letter-spacing:inherit;
      }
      .register-sort-btn:hover{
        color:#4338ca;
      }
      .register-user-cell{
        display:flex;
        align-items:center;
        gap:.85rem;
        min-width:220px;
      }
      .register-avatar,
      .register-avatar-placeholder{
        width:46px;
        height:46px;
        border-radius:16px;
        object-fit:cover;
        box-shadow:0 12px 24px rgba(15,23,42,.12);
      }
      .register-avatar-placeholder{
        display:flex;
        align-items:center;
        justify-content:center;
        background:linear-gradient(135deg, rgba(124,58,237,.18), rgba(236,72,153,.14));
        color:#6d28d9;
        font-weight:800;
      }
      .register-user-copy{
        display:flex;
        flex-direction:column;
        min-width:0;
      }
      .register-user-copy strong{
        color:#111827;
        font-weight:700;
      }
      .register-user-copy span{
        color:#94a3b8;
        font-size:.8rem;
      }
      .register-email-link{
        color:#3730a3;
        font-weight:600;
      }
      .register-muted{
        color:#94a3b8;
      }
      .register-status-badge{
        display:inline-flex;
        align-items:center;
        padding:.45rem .8rem;
        border-radius:999px;
        font-size:.8rem;
        font-weight:700;
      }
      .register-status-badge.is-active{
        background:rgba(16,185,129,.14);
        color:#047857;
      }
      .register-status-badge.is-inactive{
        background:rgba(239,68,68,.12);
        color:#b91c1c;
      }
      .register-action-group{
        display:inline-flex;
        align-items:center;
        gap:.45rem;
      }
      .register-action-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:38px;
        height:38px;
        border:none;
        border-radius:12px;
        background:rgba(255,255,255,.9);
        box-shadow:0 10px 20px rgba(15,23,42,.08);
      }
      .register-action-btn.edit{color:#4338ca}
      .register-action-btn.toggle{color:#b45309}
      .register-action-btn.delete{color:#b91c1c}
      .register-empty-state{
        padding:4rem 1rem !important;
        color:#94a3b8 !important;
      }
      .register-table-footer{
        border-top:1px solid rgba(226,232,240,.9);
      }
      .register-table-footer small{
        color:#64748b;
      }
      .register-modal .modal-dialog{
        max-width:1020px;
      }
      .register-modal-content{
        border:none;
        border-radius:28px;
        overflow:hidden;
        background:
          radial-gradient(circle at top right, rgba(236,72,153,.12), transparent 24%),
          radial-gradient(circle at left center, rgba(99,102,241,.14), transparent 32%),
          linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.94));
        box-shadow:0 30px 70px rgba(15,23,42,.18);
      }
      .register-modal-header{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:1rem;
        padding:1.35rem 1.5rem 1rem;
        border-bottom:1px solid rgba(226,232,240,.9);
      }
      .register-modal-kicker{
        display:inline-flex;
        align-items:center;
        padding:.34rem .72rem;
        border-radius:999px;
        background:rgba(99,102,241,.12);
        color:#4338ca;
        font-size:.76rem;
        font-weight:800;
        letter-spacing:.04em;
        text-transform:uppercase;
      }
      .register-modal-header h5{
        margin:.75rem 0 .35rem;
        font-size:1.2rem;
        font-weight:800;
        color:#111827;
      }
      .register-modal-header p{
        margin:0;
        color:#64748b;
      }
      .register-modal-close{
        width:40px;
        height:40px;
        border:none;
        border-radius:14px;
        background:rgba(15,23,42,.05);
        color:#475569;
        font-size:1.4rem;
        line-height:1;
      }
      .register-modal-body{
        padding:1.5rem;
      }
      .register-form-stack{
        display:flex;
        flex-direction:column;
        gap:1.25rem;
      }
      .register-form-section{
        padding:1.15rem;
        border-radius:22px;
        border:1px solid rgba(226,232,240,.9);
        background:rgba(255,255,255,.72);
        box-shadow:0 14px 28px rgba(15,23,42,.04);
      }
      .register-form-section-title{
        margin:0 0 .25rem;
        font-size:1rem;
        font-weight:800;
        color:#111827;
      }
      .register-form-section-text{
        margin:0 0 1rem;
        color:#64748b;
      }
      .register-form-label{
        display:block;
        margin-bottom:.5rem;
        color:#334155;
        font-weight:700;
      }
      .register-form-control{
        min-height:50px;
        border-radius:16px;
        border:1px solid rgba(148,163,184,.18);
        background:rgba(255,255,255,.88);
        box-shadow:none;
      }
      .register-form-control:focus{
        border-color:rgba(124,58,237,.35);
        box-shadow:0 0 0 .2rem rgba(124,58,237,.12);
      }
      .register-avatar-panel{
        display:flex;
        align-items:center;
        gap:1rem;
        padding:1rem;
        border-radius:20px;
        background:linear-gradient(135deg, rgba(124,58,237,.08), rgba(236,72,153,.08));
        border:1px dashed rgba(124,58,237,.2);
      }
      .register-avatar-preview{
        width:94px;
        height:94px;
        border-radius:22px;
        object-fit:cover;
        box-shadow:0 16px 30px rgba(15,23,42,.14);
      }
      .register-modal-footer{
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:.75rem;
        padding:1rem 1.5rem 1.5rem;
        border-top:1px solid rgba(226,232,240,.9);
      }
      .register-secondary-btn{
        border:none;
        border-radius:14px;
        padding:.82rem 1.2rem;
        background:rgba(241,245,249,.95);
        color:#334155;
        font-weight:700;
      }
      .register-primary-btn{
        border:none;
        border-radius:14px;
        padding:.82rem 1.25rem;
        background:linear-gradient(135deg, #7c3aed, #ec4899);
        color:#fff;
        font-weight:700;
        box-shadow:0 18px 34px rgba(124,58,237,.22);
      }
      @media (max-width: 991.98px){
        .register-hero-card,
        .register-section-head,
        .register-table-head,
        .register-table-footer,
        .register-modal-header,
        .register-avatar-panel{
          flex-direction:column;
          align-items:flex-start;
        }
        .register-hero-side{
          width:100%;
          align-items:stretch;
        }
      }
      @media (max-width: 767.98px){
        .register-hero-card,
        .register-filters-card,
        .register-modal-body{
          padding:1.1rem;
        }
        .register-table-head,
        .register-table-footer,
        .register-modal-header,
        .register-modal-footer{
          padding:1rem 1.1rem;
        }
        .register-stat-grid{
          grid-template-columns:1fr 1fr;
        }
        .register-table-modern thead th,
        .register-table-modern tbody td{
          padding:.85rem .8rem;
        }
      }
    </style>
  @endpush
@endonce

<div class="register-admin-shell">
  <div class="register-hero-card">
    <div>
      <span class="register-eyebrow">{{ __('Access Management') }}</span>
      <h4 class="register-hero-title">{{ __('Registers') }}</h4>
      <p class="register-hero-text">{{ __('Manage register accounts, contact details, and access status from one clean workspace.') }}</p>
    </div>

    <div class="register-hero-side">
      <div class="register-stat-grid">
        <div class="register-stat-card">
          <span>{{ __('Total') }}</span>
          <strong>{{ number_format($rows->total()) }}</strong>
        </div>
        <div class="register-stat-card">
          <span>{{ __('Visible') }}</span>
          <strong>{{ number_format($rows->count()) }}</strong>
        </div>
      </div>

      <button class="btn register-add-btn" wire:click="openCreate">
        <i class="fas fa-plus mr-2"></i>{{ __('Add Register') }}
      </button>
    </div>
  </div>

  <div class="register-filters-card">
    <div class="register-section-head">
      <div>
        <h5>{{ __('Filters & Search') }}</h5>
        <p>{{ __('Quickly narrow the register list by identity or status.') }}</p>
      </div>
      <span class="register-filter-chip">{{ __('Live search enabled') }}</span>
    </div>

    <div class="row">
      <div class="col-lg-7 mb-3 mb-lg-0">
        <label class="register-label">{{ __('Search') }}</label>
        <div class="register-input-shell">
          <i class="fas fa-search"></i>
          <input
            type="text"
            class="form-control"
            placeholder="{{ __('Name / Email / Phone') }}"
            wire:model.debounce.400ms="q"
          >
        </div>
      </div>

      <div class="col-lg-5">
        <label class="register-label">{{ __('Status') }}</label>
        <div class="register-input-shell">
          <i class="fas fa-signal"></i>
          <select class="form-control bg-transparent" wire:model="statusFilter">
            <option value="">{{ __('All') }}</option>
            <option value="1">{{ __('Active') }}</option>
            <option value="0">{{ __('Inactive') }}</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="register-table-card">
    <div class="register-table-head">
      <div>
        <h5>{{ __('Register Directory') }}</h5>
        <p>{{ __('Review, edit, activate, or remove register accounts without leaving the page.') }}</p>
      </div>
      <span class="register-table-pill">{{ __('Sortable columns') }}</span>
    </div>

    <div class="table-responsive">
      <table class="table register-table-modern align-middle mb-0">
        <thead>
          <tr>
            <th>{{ __('Profile') }}</th>
            <th>
              <button type="button" class="register-sort-btn" wire:click="sort('name')">
                <span>{{ __('Name') }}</span>
                @if($sortBy === 'name')
                  <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                @endif
              </button>
            </th>
            <th>
              <button type="button" class="register-sort-btn" wire:click="sort('email')">
                <span>{{ __('Email') }}</span>
                @if($sortBy === 'email')
                  <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                @endif
              </button>
            </th>
            <th>{{ __('Phone') }}</th>
            <th>
              <button type="button" class="register-sort-btn" wire:click="sort('status')">
                <span>{{ __('Status') }}</span>
                @if($sortBy === 'status')
                  <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                @endif
              </button>
            </th>
            <th>
              <button type="button" class="register-sort-btn" wire:click="sort('created_at')">
                <span>{{ __('Created') }}</span>
                @if($sortBy === 'created_at')
                  <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                @endif
              </button>
            </th>
            <th class="text-right">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $u)
            <tr>
              <td style="width:88px;">
                @php $av = optional($u->profile)->avatar; @endphp
                @if($av)
                  <img src="{{ app('cloudfront').$av }}" class="register-avatar" alt="avatar">
                @else
                  <div class="register-avatar-placeholder">
                    <span>{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                  </div>
                @endif
              </td>
              <td>
                <div class="register-user-cell">
                  <div class="register-user-copy">
                    <strong>{{ $u->name }}</strong>
                    <span>#{{ $u->id }}</span>
                  </div>
                </div>
              </td>
              <td>
                <a href="mailto:{{ $u->email }}" class="register-email-link">{{ $u->email }}</a>
              </td>
              <td>
                @if(optional($u->profile)->phone)
                  {{ optional($u->profile)->phone }}
                @else
                  <span class="register-muted">&mdash;</span>
                @endif
              </td>
              <td>
                <span class="register-status-badge {{ $u->status === 1 ? 'is-active' : 'is-inactive' }}">
                  {{ $u->status === 1 ? __('Active') : __('Inactive') }}
                </span>
              </td>
              <td>{{ $u->created_at?->format('Y-m-d') }}</td>
              <td class="text-right">
                <div class="register-action-group">
                  <button class="register-action-btn edit" wire:click="openEdit({{ $u->id }})" title="{{ __('Edit Register') }}">
                    <i class="fas fa-pen"></i>
                  </button>
                  <button class="register-action-btn toggle" wire:click="toggleStatus({{ $u->id }})" title="{{ __('Toggle status') }}">
                    <i class="fas fa-power-off"></i>
                  </button>
                  {{-- <button
                    class="register-action-btn delete"
                    onclick="confirm('{{ __('Delete this register? This action cannot be undone. And all data will be lost!') }}') || event.stopImmediatePropagation()"
                    wire:click="deleteUser({{ $u->id }})"
                    title="{{ __('Delete Register') }}"
                  >
                    <i class="fas fa-trash"></i>
                  </button> --}}
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center register-empty-state">
                <i class="fas fa-inbox mr-2"></i>{{ __('No registers found') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="register-table-footer">
      <small>
        {{ __('Showing') }} {{ $rows->firstItem() ?: 0 }}-{{ $rows->lastItem() ?: 0 }} {{ __('of') }} {{ number_format($rows->total()) }}
      </small>
      <div>{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
  </div>

  @include('components.auth.form-register')
</div>
