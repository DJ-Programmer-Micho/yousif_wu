@once
  @push('css')
    <style>
      .announcement-admin-shell{
        display:flex;
        flex-direction:column;
        gap:1.5rem;
      }

      .announcement-hero-card,
      .announcement-filters-card,
      .announcement-table-card{
        border:1px solid rgba(255,255,255,.7);
        border-radius:24px;
        background:
          radial-gradient(circle at top right, rgba(236,72,153,.12), transparent 26%),
          radial-gradient(circle at left center, rgba(99,102,241,.14), transparent 34%),
          linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.92));
        box-shadow:0 24px 60px rgba(15,23,42,.08);
        backdrop-filter:blur(16px);
      }

      .announcement-hero-card{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1.5rem;
        padding:1.6rem 1.75rem;
      }

      .announcement-eyebrow{
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

      .announcement-hero-title{
        margin:.8rem 0 .45rem;
        font-size:1.8rem;
        font-weight:800;
        color:#111827;
      }

      .announcement-hero-text{
        margin:0;
        max-width:680px;
        color:#64748b;
        line-height:1.7;
      }

      .announcement-hero-side{
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        gap:1rem;
        min-width:260px;
      }

      .announcement-stat-grid{
        display:grid;
        grid-template-columns:repeat(2, minmax(120px, 1fr));
        gap:.85rem;
        width:100%;
      }

      .announcement-stat-card{
        padding:.9rem 1rem;
        border-radius:18px;
        background:rgba(255,255,255,.72);
        border:1px solid rgba(148,163,184,.14);
        box-shadow:0 14px 28px rgba(15,23,42,.05);
      }

      .announcement-stat-card span{
        display:block;
        color:#64748b;
        font-size:.78rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.04em;
      }

      .announcement-stat-card strong{
        display:block;
        margin-top:.3rem;
        color:#0f172a;
        font-size:1.35rem;
        font-weight:800;
      }

      .announcement-add-btn{
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

      .announcement-add-btn:hover{
        color:#fff;
        transform:translateY(-1px);
      }

      .announcement-filters-card{
        padding:1.35rem 1.5rem 1.5rem;
      }

      .announcement-section-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:1rem;
        margin-bottom:1.1rem;
      }

      .announcement-section-head h5{
        margin:0 0 .3rem;
        font-size:1.05rem;
        font-weight:800;
        color:#111827;
      }

      .announcement-section-head p{
        margin:0;
        color:#64748b;
      }

      .announcement-filter-chip{
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

      .announcement-label{
        display:block;
        margin-bottom:.5rem;
        color:#334155;
        font-weight:700;
      }

      .announcement-input-shell{
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

      .announcement-input-shell i{
        color:#8b5cf6;
        font-size:.95rem;
      }

      .announcement-input-shell .form-control,
      .announcement-input-shell .form-select{
        border:none;
        background:transparent;
        box-shadow:none;
        padding:0;
        height:auto;
        color:#111827;
      }

      .announcement-table-card{
        overflow:hidden;
      }

      .announcement-table-head,
      .announcement-table-footer{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        padding:1.2rem 1.5rem;
      }

      .announcement-table-head{
        border-bottom:1px solid rgba(226,232,240,.9);
      }

      .announcement-table-head h5{
        margin:0 0 .3rem;
        font-size:1.05rem;
        font-weight:800;
        color:#111827;
      }

      .announcement-table-head p{
        margin:0;
        color:#64748b;
      }

      .announcement-table-pill{
        display:inline-flex;
        align-items:center;
        padding:.48rem .85rem;
        border-radius:999px;
        background:rgba(99,102,241,.12);
        color:#4338ca;
        font-size:.8rem;
        font-weight:700;
      }

      .announcement-table-footer{
        border-top:1px solid rgba(226,232,240,.9);
      }

      .announcement-table-footer small{
        color:#64748b;
      }

      .announcement-modal .modal-dialog{
        max-width:960px;
        margin:1.75rem auto;
      }

      .announcement-modal-content{
        display:flex;
        flex-direction:column;
        border:none;
        border-radius:28px;
        max-height:calc(100vh - 3.5rem);
        overflow:hidden;
        background:
          radial-gradient(circle at top right, rgba(236,72,153,.12), transparent 24%),
          radial-gradient(circle at left center, rgba(99,102,241,.14), transparent 32%),
          linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.94));
        box-shadow:0 30px 70px rgba(15,23,42,.18);
      }

      .announcement-modal-form{
        display:flex;
        flex:1 1 auto;
        flex-direction:column;
        min-height:0;
      }

      .announcement-modal-header{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:1rem;
        padding:1.35rem 1.5rem 1rem;
        border-bottom:1px solid rgba(226,232,240,.9);
      }

      .announcement-modal-kicker{
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

      .announcement-modal-header h5{
        margin:.75rem 0 .35rem;
        font-size:1.2rem;
        font-weight:800;
        color:#111827;
      }

      .announcement-modal-header p{
        margin:0;
        color:#64748b;
      }

      .announcement-modal-close{
        width:40px;
        height:40px;
        border:none;
        border-radius:14px;
        background:rgba(15,23,42,.05);
        color:#475569;
        font-size:1.4rem;
        line-height:1;
      }

      .announcement-modal-body{
        flex:1 1 auto;
        min-height:0;
        padding:1.5rem;
        overflow-y:auto;
        overscroll-behavior:contain;
      }

      .announcement-form-stack{
        display:flex;
        flex-direction:column;
        gap:1.25rem;
      }

      .announcement-form-section{
        padding:1.15rem;
        border-radius:22px;
        border:1px solid rgba(226,232,240,.9);
        background:rgba(255,255,255,.72);
        box-shadow:0 14px 28px rgba(15,23,42,.04);
      }

      .announcement-form-section-title{
        margin:0 0 .25rem;
        font-size:1rem;
        font-weight:800;
        color:#111827;
      }

      .announcement-form-section-text{
        margin:0 0 1rem;
        color:#64748b;
      }

      .announcement-form-label{
        display:block;
        margin-bottom:.5rem;
        color:#334155;
        font-weight:700;
      }

      .announcement-form-control{
        min-height:52px;
        border-radius:16px;
        border:1px solid rgba(148,163,184,.18);
        background:rgba(255,255,255,.88);
        box-shadow:none;
      }

      .announcement-form-control:focus{
        border-color:rgba(124,58,237,.35);
        box-shadow:0 0 0 .2rem rgba(124,58,237,.12);
      }

      .announcement-textarea{
        min-height:180px;
        resize:vertical;
      }

      .announcement-switch-shell{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        padding:1rem 1.1rem;
        border-radius:18px;
        background:linear-gradient(135deg, rgba(124,58,237,.08), rgba(236,72,153,.08));
        border:1px dashed rgba(124,58,237,.18);
      }

      .announcement-switch-shell strong{
        display:block;
        color:#111827;
        font-weight:800;
      }

      .announcement-switch-shell span{
        display:block;
        margin-top:.2rem;
        color:#64748b;
        font-size:.92rem;
      }

      .announcement-modal-footer{
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:.75rem;
        padding:1rem 1.5rem 1.5rem;
        border-top:1px solid rgba(226,232,240,.9);
      }

      .announcement-secondary-btn{
        border:none;
        border-radius:14px;
        padding:.82rem 1.2rem;
        background:rgba(241,245,249,.95);
        color:#334155;
        font-weight:700;
      }

      .announcement-primary-btn{
        border:none;
        border-radius:14px;
        padding:.82rem 1.25rem;
        background:linear-gradient(135deg, #7c3aed, #ec4899);
        color:#fff;
        font-weight:700;
        box-shadow:0 18px 34px rgba(124,58,237,.22);
      }

      @media (max-width: 991.98px){
        .announcement-hero-card,
        .announcement-section-head,
        .announcement-table-head,
        .announcement-table-footer,
        .announcement-modal-header{
          flex-direction:column;
          align-items:flex-start;
        }

        .announcement-hero-side{
          width:100%;
          align-items:stretch;
        }
      }

      @media (max-width: 767.98px){
        .announcement-hero-card,
        .announcement-filters-card,
        .announcement-modal-body{
          padding:1.1rem;
        }

        .announcement-table-head,
        .announcement-table-footer,
        .announcement-modal-header,
        .announcement-modal-footer{
          padding:1rem 1.1rem;
        }

        .announcement-stat-grid{
          grid-template-columns:1fr 1fr;
        }

        .announcement-modal .modal-dialog{
          margin:1rem .5rem;
        }

        .announcement-modal-content{
          max-height:calc(100vh - 2rem);
          border-radius:22px;
        }
      }
    </style>
  @endpush
@endonce

<div class="announcement-admin-shell">
  <div class="announcement-hero-card">
    <div>
      <span class="announcement-eyebrow">{{ __('Content Management') }}</span>
      <h4 class="announcement-hero-title">{{ __('Announcements') }}</h4>
      <p class="announcement-hero-text">
        {{ __('Manage dashboard news, visibility timing, and important updates from one modern workspace.') }}
      </p>
    </div>

    <div class="announcement-hero-side">
      <div class="announcement-stat-grid">
        <div class="announcement-stat-card">
          <span>{{ __('Total') }}</span>
          <strong>{{ number_format($rows->total()) }}</strong>
        </div>
        <div class="announcement-stat-card">
          <span>{{ __('Visible') }}</span>
          <strong>{{ number_format($rows->where('is_visible', true)->count()) }}</strong>
        </div>
      </div>

      <button class="btn announcement-add-btn" wire:click="openCreate">
        <i class="fas fa-plus mr-2"></i>{{ __('Add Announcement') }}
      </button>
    </div>
  </div>

  <div class="announcement-filters-card">
    <div class="announcement-section-head">
      <div>
        <h5>{{ __('Filters & Search') }}</h5>
        <p>{{ __('Quickly narrow the list by content text, visibility, and page size.') }}</p>
      </div>
      <span class="announcement-filter-chip">{{ __('Live search enabled') }}</span>
    </div>

    <div class="row">
      <div class="col-lg-5 mb-3 mb-lg-0">
        <label class="announcement-label">{{ __('Search') }}</label>
        <div class="announcement-input-shell">
          <i class="fas fa-search"></i>
          <input
            type="text"
            class="form-control"
            placeholder="{{ __('Type to search in text...') }}"
            wire:model.debounce.400ms="q"
          >
        </div>
      </div>

      <div class="col-lg-4 mb-3 mb-lg-0">
        <label class="announcement-label">{{ __('Visibility') }}</label>
        <div class="announcement-input-shell">
          <i class="fas fa-eye"></i>
          <select class="form-select form-control bg-transparent" wire:model="visibleFilter">
            <option value="">{{ __('All') }}</option>
            <option value="1">{{ __('Shown') }}</option>
            <option value="0">{{ __('Hidden') }}</option>
          </select>
        </div>
      </div>

      <div class="col-lg-3">
        <label class="announcement-label">{{ __('Per Page') }}</label>
        <div class="announcement-input-shell">
          <i class="fas fa-list-ol"></i>
          <select class="form-select form-control bg-transparent" wire:model="perPage">
            <option>10</option>
            <option>15</option>
            <option>25</option>
            <option>50</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="announcement-table-card">
    <div class="announcement-table-head">
      <div>
        <h5>{{ __('Announcement Directory') }}</h5>
        <p>{{ __('Review, edit, schedule, show, hide, or delete announcements without leaving the page.') }}</p>
      </div>
      <span class="announcement-table-pill">{{ __('Sortable columns') }}</span>
    </div>

    <div class="table-responsive">
      @include('components.tables.announcement-table', ['rows' => $rows])
    </div>

    <div class="announcement-table-footer">
      <small>
        {{ __('Showing') }} {{ $rows->firstItem() ?: 0 }}-{{ $rows->lastItem() ?: 0 }}
        {{ __('of') }} {{ number_format($rows->total()) }}
      </small>
      <div>{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
  </div>

  <div class="modal fade announcement-modal @if($showModal) show d-block @endif"
       tabindex="-1"
       @if($showModal) style="background:rgba(15,23,42,.45);" @endif>
    <div class="modal-dialog">
      <div class="modal-content announcement-modal-content" wire:ignore.self>
        <div class="announcement-modal-header">
          <div>
            <span class="announcement-modal-kicker">
              {{ $editId ? __('Update entry') : __('New entry') }}
            </span>
            <h5>{{ $editId ? __('Edit Announcement') : __('Add Announcement') }}</h5>
            <p>{{ __('Create a message, define visibility, and control the display window.') }}</p>
          </div>

          <button type="button" class="announcement-modal-close" wire:click="$set('showModal', false)">×</button>
        </div>

        <form wire:submit.prevent="save" class="announcement-modal-form">
          <div class="announcement-modal-body">
            <div class="announcement-form-stack">
              <div class="announcement-form-section">
                <h6 class="announcement-form-section-title">{{ __('Announcement Content') }}</h6>
                <p class="announcement-form-section-text">{{ __('Write the dashboard message users will see in the news bar.') }}</p>

                <label class="announcement-form-label">{{ __('Text') }}</label>
                <textarea
                  rows="8"
                  class="form-control announcement-form-control announcement-textarea @error('body') is-invalid @enderror"
                  wire:model.defer="body"
                  placeholder="{{ __('Type the announcement here...') }}"
                ></textarea>
                @error('body') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>

              <div class="announcement-form-section">
                <h6 class="announcement-form-section-title">{{ __('Visibility & Schedule') }}</h6>
                <p class="announcement-form-section-text">{{ __('Control whether this item is shown now and when it becomes active.') }}</p>

                <div class="announcement-switch-shell mb-3">
                  <div>
                    <strong>{{ __('Visibility Status') }}</strong>
                    <span>{{ $is_visible ? __('This announcement is currently shown.') : __('This announcement is currently hidden.') }}</span>
                  </div>

                  <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="is_visible" wire:model.defer="is_visible">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3 mb-md-0">
                    <label class="announcement-form-label">{{ __('Show From (optional)') }}</label>
                    <input
                      type="datetime-local"
                      class="form-control announcement-form-control @error('show_from') is-invalid @enderror"
                      wire:model.defer="show_from"
                    >
                    @error('show_from') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="announcement-form-label">{{ __('Show Until (optional)') }}</label>
                    <input
                      type="datetime-local"
                      class="form-control announcement-form-control @error('show_until') is-invalid @enderror"
                      wire:model.defer="show_until"
                    >
                    @error('show_until') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="announcement-modal-footer">
            <button type="button" class="btn announcement-secondary-btn" wire:click="$set('showModal', false)">
              {{ __('Cancel') }}
            </button>

            <button type="submit" class="btn announcement-primary-btn" wire:loading.attr="disabled">
              <span wire:loading.remove>{{ __('Save Announcement') }}</span>
              <span wire:loading>
                <i class="spinner-border spinner-border-sm mr-1"></i>{{ __('Saving...') }}
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
