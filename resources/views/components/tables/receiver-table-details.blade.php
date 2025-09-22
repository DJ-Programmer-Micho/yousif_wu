{{-- resources/views/components/tables/receiver-table-details.blade.php --}}
<div>
  <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap">
    <div class="mb-2">
      <div class="small text-muted">{{ __('Totals') }} @if($dateFrom || $dateTo)<span class="badge badge-info ml-1">Filtered</span>@endif</div>
      <div>
        <span class="mr-3">{{ __('Incoming:') }} <b>{{ number_format($incoming) }}</b></span>
        <span class="mr-3">{{ __('Outgoing:') }} <b>{{ number_format($outgoing) }}</b></span>
        <span>{{ __('Running:') }} <b>{{ number_format($running) }}</b></span>
      </div>
    </div>

    <div class="d-flex align-items-end mb-2">
      <div class="form-group mb-0 mr-2">
        <label class="small mb-1">From</label>
        <input type="date" class="form-control form-control-sm" wire:model.lazy="dateFrom">
        @error('dateFrom') <small class="text-danger">{{ $message }}</small> @enderror
      </div>
      <div class="form-group mb-0 mr-2">
        <label class="small mb-1">To</label>
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

      @if((int)auth()->user()->role === 1)
        <button class="btn btn-sm btn-danger ml-2"
                onclick="confirm('{{ __('Reset running balance to zero?') }}') || event.stopImmediatePropagation()"
                wire:click="resetToZero"
                @if($running<=0) disabled @endif>
          {{ __('Reset to Zero') }}
        </button>

        <a class="btn btn-sm btn-outline-success ml-2"
          href="{{ route('balance.details.export', [
                'type'     => 'receiver',
                'userId'   => $user->id,
                'mode'     => $mode,
                'dateFrom' => $dateFrom,
                'dateTo'   => $dateTo,
          ]) }}">
          <i class="fas fa-file-excel mr-1"></i> {{ __('Export') }}
        </a>

      @endif
    </div>
  </div>

  @if((int)auth()->user()->role === 1)
    <div class="card mb-3">
      <div class="card-body p-2">
        <div class="d-flex align-items-end">
          <div class="form-group mb-0 mr-2">
            <label class="small mb-1">{{ __('Quick Deduct (IQD)') }}</label>
            <input type="number" min="1" class="form-control form-control-sm" wire:model.lazy="deductInlineAmount" placeholder="{{ __('Amount') }}">
            @error('deductInlineAmount') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
          <div class="form-group mb-0 mr-2 flex-fill">
            <label class="small mb-1">{{ __('Note') }}</label>
            <input type="text" class="form-control form-control-sm" wire:model.lazy="deductInlineNote" placeholder="Optional">
            @error('deductInlineNote') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
          <button class="btn btn-sm btn-danger"
                  wire:click="saveInlineDeduct"
                  @if($running<=0 || empty($deductInlineAmount)) disabled @endif>
            {{ __('Deduct') }}
          </button>
        </div>
      </div>
    </div>
  @endif

  <div class="table-responsive">
    <table class="table table-sm table-hover">
      <thead class="thead-light">
        <tr>
          <th>#</th>
          <th class="text-center">{{ __('Status') }}</th>
          <th class="text-center">{{ __('Amount (IQD)') }}</th>
          <th>{{ __('Receiver') }}</th>
          <th>{{ __('Admin') }}</th>
          <th>{{ __('Note') }}</th>
          <th>{{ __('Created') }}</th>
          @if((int)auth()->user()->role === 1)
            <th class="text-center">{{ __('Actions') }}</th>
          @endif
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
            <span class="text-{{ $r->status==='Incoming' ? 'success':'danger' }}">{{ number_format($r->amount) }}</span>
          </td>

          {{-- Receiver person --}}
          <td>
            @if($r->receiver)
              {{ trim(($r->receiver->first_name ?? '').' '.($r->receiver->last_name ?? '')) ?: '—' }}
            @else
              —
            @endif
          </td>

          {{-- Admin who deducted/reset --}}
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
        <tr><td colspan="{{ (int)auth()->user()->role === 1 ? 8 : 7 }}" class="text-center text-muted p-4">{{ __('No records.') }}</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div>
    {{ $rows->links() }}
  </div>
</div>
