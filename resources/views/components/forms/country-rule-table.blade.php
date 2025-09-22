<div>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">{{ __('Country Rules (Not Allowed to Transfer)') }}</h5>
    <button class="btn btn-primary btn-sm" wire:click="createOpen">{{ __('Add Country') }}</button>
  </div>

  @if (session('success')) <div class="alert alert-success py-2">{{ session('success') }}</div> @endif

  <div class="form-row mb-2">
    <div class="col-auto">
      <input class="form-control form-control-sm" placeholder="{{ __('Search country...') }}" wire:model.debounce.400ms="search">
    </div>
    <div class="col-auto">
      <select class="form-control form-control-sm" wire:model="perPage">
        <option>10</option><option>25</option><option>50</option>
      </select>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-sm table-hover">
      <thead class="thead-light">
        <tr>
          <th>#</th>
          <th>{{ __('Country') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
      @foreach($rules as $i => $row)
        <tr>
          <td>{{ $rules->firstItem() + $i }}</td>
          <td>
            <img src="{{ app('cloudfrontflagsx2').'/'.$row->country->flag_path }}" style="height:12px" class="mr-1">
            {{ $row->country->en_name }}
          </td>
          <td><span class="badge badge-danger">{{ __('Not allowed to transfer') }}</span></td>
          <td>
            <button class="btn btn-outline-secondary btn-sm" wire:click="editOpen({{ $row->id }})">{{ __('View') }}</button>
            <button class="btn btn-outline-danger btn-sm" wire:click="confirmDelete({{ $row->id }})">{{ __('Delete') }}</button>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  {{ $rules->links() }}
  @include('components.forms.country-rule-create')
</div>
