<table class="table table-hover align-middle mb-0">
  <thead class="table-light">
    <tr>
      <th class="sortable" wire:click="sort('is_visible')">
        {{ __('Status') }}
        @if($sortBy==='is_visible') <i class="fas fa-sort-{{ $sortDirection==='asc'?'up':'down' }} ms-1"></i> @endif
      </th>
      <th>{{ __('Text') }}</th>
      <th class="sortable" wire:click="sort('show_from')">
        {{ __('From') }}
        @if($sortBy==='show_from') <i class="fas fa-sort-{{ $sortDirection==='asc'?'up':'down' }} ms-1"></i> @endif
      </th>
      <th class="sortable" wire:click="sort('show_until')">
        {{ __('Until') }}
        @if($sortBy==='show_until') <i class="fas fa-sort-{{ $sortDirection==='asc'?'up':'down' }} ms-1"></i> @endif
      </th>
      <th class="sortable" wire:click="sort('created_at')">
        {{ __('Created') }}
        @if($sortBy==='created_at') <i class="fas fa-sort-{{ $sortDirection==='asc'?'up':'down' }} ms-1"></i> @endif
      </th>
      <th>{{ __('By') }}</th>
      <th class="text-end">{{ __('Actions') }}</th>
    </tr>
  </thead>
  <tbody>
    @forelse($rows as $a)
      <tr>
        <td>
          <span class="badge {{ $a->is_visible ? 'bg-success' : 'bg-secondary' }}">
            {{ $a->is_visible ? __('Shown') : __('Hidden') }}
          </span>
        </td>
        <td style="max-width: 520px;">
          <div class="text-truncate" title="{{ trim($a->body) }}">{{ $a->body }}</div>
        </td>
        <td>{{ $a->show_from ? $a->show_from->format('Y-m-d H:i') : '—' }}</td>
        <td>{{ $a->show_until ? $a->show_until->format('Y-m-d H:i') : '—' }}</td>
        <td>{{ $a->created_at?->format('Y-m-d H:i') }}</td>
        <td>{{ $a->creator?->name ?: '—' }}</td>
        <td class="text-end">
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $a->id }})" title="{{ __('Edit') }}">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-warning" wire:click="toggleVisible({{ $a->id }})" title="{{ __('Toggle visibility') }}">
              <i class="fas fa-eye{{ $a->is_visible ? '' : '-slash' }}"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger"
                    onclick="confirm('{{ __('Delete this announcement? This action cannot be undone!') }}') || event.stopImmediatePropagation()"
                    wire:click="delete({{ $a->id }})">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="7" class="text-center text-muted py-5">
          <i class="fas fa-inbox me-2"></i> {{ __('No announcements found') }}
        </td>
      </tr>
    @endforelse
  </tbody>
</table>
