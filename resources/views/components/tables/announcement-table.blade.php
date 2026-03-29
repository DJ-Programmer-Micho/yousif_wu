<table class="table align-middle mb-0 announcement-table-modern">
  <thead>
    <tr>
      <th>
        <button type="button" class="register-sort-btn btn btn-primary rounded-pill" wire:click="sort('is_visible')">
          <span>{{ __('Status') }}</span>
          @if($sortBy === 'is_visible')
            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
          @endif
        </button>
      </th>

      <th>{{ __('Announcement') }}</th>

      <th>
        <button type="button" class="register-sort-btn btn btn-primary rounded-pill" wire:click="sort('show_from')">
          <span>{{ __('From') }}</span>
          @if($sortBy === 'show_from')
            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
          @endif
        </button>
      </th>

      <th>
        <button type="button" class="register-sort-btn btn btn-primary rounded-pill" wire:click="sort('show_until')">
          <span>{{ __('Until') }}</span>
          @if($sortBy === 'show_until')
            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
          @endif
        </button>
      </th>

      <th>
        <button type="button" class="register-sort-btn btn btn-primary rounded-pill" wire:click="sort('created_at')">
          <span>{{ __('Created') }}</span>
          @if($sortBy === 'created_at')
            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
          @endif
        </button>
      </th>

      <th>{{ __('By') }}</th>
      <th class="text-right">{{ __('Actions') }}</th>
    </tr>
  </thead>

  <tbody>
    @forelse($rows as $a)
      <tr>
        <td style="width:120px;">
          <span class="register-status-badge {{ $a->is_visible ? 'is-active' : 'is-inactive' }}">
            <i class="fas fa-circle mr-1" style="font-size:9px;"></i>
            {{ $a->is_visible ? __('Shown') : __('Hidden') }}
          </span>
        </td>

        <td style="min-width:320px; max-width:520px;">
          <div class="d-flex align-items-start" style="gap:.85rem;">
            <div class="register-avatar-placeholder" style="width:44px;height:44px;border-radius:14px;flex:0 0 44px;">
              <i class="fas fa-bullhorn"></i>
            </div>

            <div class="register-user-copy" style="min-width:0;">
              <strong class="d-block">{{ \Illuminate\Support\Str::limit(trim($a->body), 90) }}</strong>
              <span class="d-block mt-1">
                {{ \Illuminate\Support\Str::limit(trim($a->body), 140) }}
              </span>
            </div>
          </div>
        </td>

        <td>
          @if($a->show_from)
            <span class="announcement-date-chip">
              <i class="far fa-play-circle mr-1"></i>{{ $a->show_from->format('Y-m-d H:i') }}
            </span>
          @else
            <span class="register-muted">&mdash;</span>
          @endif
        </td>

        <td>
          @if($a->show_until)
            <span class="announcement-date-chip">
              <i class="far fa-stop-circle mr-1"></i>{{ $a->show_until->format('Y-m-d H:i') }}
            </span>
          @else
            <span class="register-muted">&mdash;</span>
          @endif
        </td>

        <td>{{ $a->created_at?->format('Y-m-d H:i') }}</td>

        <td>
          @if($a->creator?->name)
            <span class="announcement-author-chip">
              <i class="far fa-user mr-1"></i>{{ $a->creator->name }}
            </span>
          @else
            <span class="register-muted">&mdash;</span>
          @endif
        </td>

        <td class="text-right">
          <div class="register-action-group">
            <button class="register-action-btn edit" wire:click="openEdit({{ $a->id }})" title="{{ __('Edit') }}">
              <i class="fas fa-pen"></i>
            </button>

            <button class="register-action-btn toggle" wire:click="toggleVisible({{ $a->id }})" title="{{ __('Toggle visibility') }}">
              <i class="fas fa-eye{{ $a->is_visible ? '' : '-slash' }}"></i>
            </button>

            <button
              class="register-action-btn delete"
              onclick="confirm('{{ __('Delete this announcement? This action cannot be undone!') }}') || event.stopImmediatePropagation()"
              wire:click="delete({{ $a->id }})"
              title="{{ __('Delete') }}"
            >
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="7" class="text-center register-empty-state">
          <i class="fas fa-inbox mr-2"></i>{{ __('No announcements found') }}
        </td>
      </tr>
    @endforelse
  </tbody>
</table>

@once
  @push('css')
    <style>
      .announcement-table-modern thead th{
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

      .announcement-table-modern tbody td{
        border-top:1px solid rgba(226,232,240,.7);
        padding:1rem 1.1rem;
        vertical-align:middle;
        color:#0f172a;
      }

      .announcement-table-modern tbody tr{
        transition:background .2s ease;
      }

      .announcement-table-modern tbody tr:hover{
        background:rgba(99,102,241,.035);
      }

      .announcement-date-chip,
      .announcement-author-chip{
        display:inline-flex;
        align-items:center;
        padding:.45rem .8rem;
        border-radius:999px;
        font-size:.8rem;
        font-weight:700;
        white-space:nowrap;
      }

      .announcement-date-chip{
        background:rgba(99,102,241,.10);
        color:#4338ca;
      }

      .announcement-author-chip{
        background:rgba(15,23,42,.06);
        color:#334155;
      }
    </style>
  @endpush
@endonce