<div>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">{{ __('Country Limits') }}</h5>
    <button class="btn btn-primary btn-sm" wire:click="createOpen">{{ __('Add Country Limit') }}</button>
  </div>
  @include('components.forms.country-limit-create')
  @if (session('success'))
    <div class="alert alert-success py-2">{{ session('success') }}</div>
  @endif

  <div class="form-row mb-2">
    <div class="col-auto">
      <input type="text" class="form-control form-control-sm" placeholder="Search country"
             wire:model.debounce.400ms="search">
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
          <th class="text-right">{{ __('Minimum') }}</th>
          <th class="text-right">{{ __('Maximum') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
      @foreach ($limits as $i => $row)
        <tr>
          <td>{{ $limits->firstItem() + $i }}</td>
          <td>
            <img src="{{ app('cloudfrontflagsx2').'/'.$row->country->flag_path }}" class="mr-1" style="height:12px">
            {{ $row->country->en_name }}
          </td>
          <td class="text-right">{{ number_format($row->min_value, 2) }}</td>
          <td class="text-right">{{ number_format($row->max_value, 2) }}</td>
          <td>
            <button class="btn btn-outline-secondary btn-sm" wire:click="editOpen({{ $row->id }})">{{ __('Edit') }}</button>
            <button class="btn btn-outline-danger btn-sm" wire:click="confirmDeleteLimit({{ $row->id }})">{{ __('Delete') }}</button>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  {{ $limits->links() }}
