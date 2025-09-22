<div>
  @if (session('success')) <div class="alert alert-success py-2">{{ session('success') }}</div> @endif
  @if (session('error'))   <div class="alert alert-danger  py-2">{{ session('error') }}</div>   @endif

  <div class="row">
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>{{ __('Tax Bracket Sets') }}</strong>
          <button class="btn btn-primary btn-sm" wire:click="openCreateSet">{{ __('New Set') }}</button>
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="thead-light"><tr><th>{{ __('Name') }}</th><th>{{ __('Brackets') }}</th><th class="text-right">{{ __('Actions') }}</th></tr></thead>
            <tbody>
            @foreach($sets as $s)
              @php $useCount = $setUsageCounts[$s->id] ?? 0; @endphp
              <tr>
                <td>{{ $s->name }}</td>
                <td class="text-monospace" style="max-width:420px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  {{ json_encode($s->brackets_json) }}
                </td>
                <td class="text-right">
                  <button class="btn btn-outline-secondary btn-sm" wire:click="openEditSet({{ $s->id }})">{{ __('Edit') }}</button>
                  @if($useCount == 0)
                    <button class="btn btn-outline-danger btn-sm" wire:click="confirmDeleteSet({{ $s->id }})">{{ __('Delete') }}</button>
                  @else
                    <button class="btn btn-outline-danger btn-sm" disabled title="Unassign from {{ $useCount }} countr{{ $useCount>1?'ies':'y' }} first">{{ __('Delete') }}</button>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>{{ __('Assignments (Country → Set)') }}</strong>
          <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateAssign">{{ __('Assign') }}</button>
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="thead-light"><tr><th>{{ __('Country') }}</th><th>{{ __('Set') }}</th><th class="text-right">{{ __('Actions') }}</th></tr></thead>
            <tbody>
            @foreach($assignments as $a)
              <tr>
                <td>
                  <img src="{{ app('cloudfrontflagsx2').'/'.$a->country->flag_path }}" style="height:12px" class="mr-1">
                  {{ $a->country->en_name }}
                </td>
                <td>{{ $a->set->name }}</td>
                <td class="text-right">
                  <button class="btn btn-outline-secondary btn-sm" wire:click="openEditAssign({{ $a->id }})">{{ __('Edit') }}</button>
                  <button class="btn btn-outline-danger btn-sm" wire:click="confirmDeleteAssign({{ $a->id }})">{{ __('Delete') }}</button>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <small class="text-muted">{{ __('A country can have only one assignment. General taxes apply only to countries without an assignment.') }}</small>
    </div>
  </div>

  @include('components.forms.country-tax-create')
</div>
