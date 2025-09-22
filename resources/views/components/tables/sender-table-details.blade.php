{{-- resources/views/components/tables/sender-table-details.blade.php --}}
<div>
  <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap">
    <div class="mb-2">
      <div class="text-dark"><b>{{ __('Totals') }}</b> @if($dateFrom || $dateTo)<span class="badge badge-info ml-1">Filtered</span>@endif</div>
      <div class="d-flex">
        <span class="table-success text-success p-1">{{ __('Incoming:') }} <b>$ {{ number_format($incoming, 2) }}</b></span>
        <span class="table-danger text-danger p-1">{{ __('Outgoing:') }} <b>$ {{ number_format($outgoing, 2) }}</b></span>
        <span class="table-secondary p-1">{{ __('Remaining:') }} <b>$ {{ number_format($remaining, 2) }}</b></span>
      </div>
    </div>

    <div class="d-flex align-items-end mb-2">
      <div class="form-group mb-0 mr-2">
        <label class="small mb-1">{{ __('From') }}</label>
        <input type="date" class="form-control form-control-sm" wire:model.lazy="dateFrom">
        @error('dateFrom') <small class="text-danger">{{ $message }}</small> @enderror
      </div>
      <div class="form-group mb-0 mr-2">
        <label class="small mb-1">{{ __('To') }}</label>
        <input type="date" class="form-control form-control-sm" wire:model.lazy="dateTo">
        @error('dateTo') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="btn-group btn-group-sm ml-2">
        <button class="btn btn-outline-secondary {{ $mode==='all'?'active':'' }}" wire:click="setMode('all')">{{ __('All') }}</button>
        <button class="btn btn-outline-secondary {{ $mode==='incoming'?'active':'' }}" wire:click="setMode('incoming')">{{ __('Incoming') }}</button>
        <button class="btn btn-outline-secondary {{ $mode==='outgoing'?'active':'' }}" wire:click="setMode('outgoing')">{{ __('Outgoing') }}</button>
      </div>

      <button class="btn btn-sm btn-light ml-2" wire:click="clearDateFilter" @if(!$dateFrom && !$dateTo) disabled @endif>
        {{ __('Clear') }}
      </button>

      <a class="btn btn-sm btn-outline-success ml-2"
        href="{{ route('balance.details.export', [
              'type'     => 'sender',
              'userId'   => $user->id,
              'mode'     => $mode,
              'dateFrom' => $dateFrom,
              'dateTo'   => $dateTo,
        ]) }}">
        <i class="fas fa-file-excel mr-1"></i> {{ __('Export') }}
      </a>

    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-sm table-hover">
      <thead class="thead-light">
        <tr>
          <th>#</th>
          <th class="text-center">{{ __('Status') }}</th>
          <th class="text-center">{{ __('Amount (USD)') }}</th>
          <th>{{ __('Sender') }}</th>
          <th>{{ __('Admin') }}</th>
          <th>{{ __('Note') }}</th>
          <th>{{ __('Created') }}</th>
        </tr>
      </thead>
      <tbody>
      @forelse($rows as $i => $r)
        <tr>
          <td>{{ $rows->firstItem() + $i }}</td>

          <td class="text-center table-{{ $r->status==='Incoming' ? 'success':'danger' }}">
            <span class="text-{{ $r->status==='Incoming' ? 'success':'danger' }}">{{ $r->status }}</span>
          </td>

          <td class="text-center table-{{ $r->status==='Incoming' ? 'success':'danger' }}">
            <span class="text-{{ $r->status==='Incoming' ? 'success':'danger' }}">$ {{ number_format($r->amount, 2) }}</span>
          </td>

          {{-- Sender (person who made the transfer) --}}
          <td>
            @if($r->sender)
              {{ trim(($r->sender->first_name ?? '').' '.($r->sender->last_name ?? '')) ?: '—' }}
            @else
              —
            @endif
          </td>

          {{-- Admin (who topped up) --}}
          <td>{{ $r->admin->name ?? '—' }}</td>

          <td>{{ $r->note ?? '—' }}</td>
          <td>{{ $r->created_at?->format('Y-m-d H:i') }}</td>

          @if((int)auth()->user()->role === 1)
            <td class="text-center">
              <button class="btn btn-sm btn-outline-danger"
                      title="{{ __('Delete this entry') }}"
                      onclick="confirm('{{ __('Delete this entry? This will immediately change the running balance.') }}') || event.stopImmediatePropagation()"
                      wire:click="deleteEntry({{ $r->id }})"
                      wire:loading.attr="disabled"
                      wire:target="deleteEntry({{ $r->id }})">
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          @endif
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted p-4">{{ __('No records.') }}</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div>
    {{ $rows->links() }}
  </div>
</div>
