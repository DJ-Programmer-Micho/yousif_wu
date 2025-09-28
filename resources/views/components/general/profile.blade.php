<div class="profile-page">
  @php
    use Illuminate\Support\Facades\Storage;
    $avatarKey = optional(optional($user)->profile)->avatar;
    $avatarUrl = $avatarKey ? Storage::disk('s3')->url($avatarKey) : null;
    $initial = strtoupper(substr($user->name ?? 'U',0,1));
  @endphp
  {{-- Top cover --}}
  <div class="card border-0 mb-3" style="background:linear-gradient(135deg,#667eea,#764ba2);">
    <div class="card-body py-4">
      <div class="d-flex align-items-center">

        {{-- Avatar --}}
        <div class="mr-3">
          <div class="avatar-wrap position-relative">
            @if($avatarUrl)
              <img src="{{ $avatarUrl }}" alt="avatar"
                   class="rounded-circle border border-white"
                   style="width:76px;height:76px;object-fit:cover;">
            @else
              <div class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center"
                   style="width:76px;height:76px;">
                <span class="h3 m-0 text-primary">{{ $initial }}</span>
              </div>
            @endif

            {{-- hover overlay --}}
            <button class="btn btn-sm btn-light avatar-overlay"
                    wire:click="$set('showAvatarModal', true)">
              {{ __('Change') }}
            </button>
          </div>
        </div>

        {{-- Name / role --}}
        <div class="text-white">
          <div class="d-flex align-items-center flex-wrap">
            <h4 class="mb-0 font-weight-bold mr-2">{{ $user->name ?? __('User') }}</h4>
            <span class="badge badge-light">{{ $roleLabel }}</span>
            <button class="btn btn-sm btn-outline-light ml-3"
                    wire:click="$toggle('showEditBasics')">
              {{ __('Edit details') }}
            </button>
          </div>
          <div class="small opacity-75 mt-1">
            {{ __('Joined') }} {{ $joinedYear }} · <span class="text-nowrap">{{ $joinedHuman }}</span>
          </div>
        </div>

        {{-- Right quick info --}}
        <div class="ml-auto text-right text-white">
          <div class="small mb-1">{{ __('Email') }}</div>
          <div class="font-weight-bold">{{ $user->email ?? '-' }}</div>
          @if(!empty($user->phone))
            <div class="small mt-2 mb-1">{{ __('Phone') }}</div>
            <div class="font-weight-bold">{{ $user->phone }}</div>
          @endif
        </div>
      </div>

      {{-- Inline edit basics --}}
      @if($showEditBasics)
        <div class="bg-white rounded mt-3 p-3">
          <form wire:submit.prevent="saveBasics" class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="small text-muted">{{ __('Full name') }}</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name">
              @error('name') <div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
              <label class="small text-muted">{{ __('Phone') }}</label>
              <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model.defer="phone">
              @error('phone') <div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-5">
              <label class="small text-muted">{{ __('Address') }}</label>
              <input type="text" class="form-control @error('address') is-invalid @enderror" wire:model.defer="address">
              @error('address') <div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 mt-2">
              <button class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Save changes') }}</span>
                <span wire:loading><span class="spinner-border spinner-border-sm mr-1"></span>{{ __('Saving...') }}</span>
              </button>
              <button type="button" class="btn btn-light ml-2" wire:click="$set('showEditBasics', false)">{{ __('Cancel') }}</button>
            </div>
          </form>
        </div>
      @endif
    </div>
  </div>

  {{-- Stats row --}}
  <div class="row">
    {{-- Senders --}}
    <div class="col-md-6">
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small">{{ __('Executed Senders') }}</div>
              <div class="h4 mb-0 font-weight-bold">
                {{ number_format($sendersExecutedCount) }}
              </div>
            </div>
            <div class="text-right">
              <div class="text-muted small">{{ __('Total (USD)') }}</div>
              <div class="h4 mb-0 font-weight-bold">
                $ {{ number_format($sendersExecutedTotal, 2) }}
              </div>
            </div>
          </div>

          <div class="progress mt-3" style="height:8px;">
            @php
              $cap = max(1, $sendersExecutedCount + $receiversExecutedCount);
              $pct = round(($sendersExecutedCount / $cap) * 100);
            @endphp
            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $pct }}%;"></div>
          </div>
          <div class="small text-muted mt-1">{{ __('Share vs. total executed records') }}</div>
        </div>
      </div>
    </div>

    {{-- Receivers --}}
    <div class="col-md-6">
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small">{{ __('Executed Receivers') }}</div>
              <div class="h4 mb-0 font-weight-bold">
                {{ number_format($receiversExecutedCount) }}
              </div>
            </div>
            <div class="text-right">
              <div class="text-muted small">{{ __('Total (IQD)') }}</div>
              <div class="h4 mb-0 font-weight-bold">
                {{ number_format($receiversExecutedTotal, 0) }}
              </div>
            </div>
          </div>

          <div class="progress mt-3" style="height:8px;">
            @php
              $cap = max(1, $sendersExecutedCount + $receiversExecutedCount);
              $pct = round(($receiversExecutedCount / $cap) * 100);
            @endphp
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%;"></div>
          </div>
          <div class="small text-muted mt-1">{{ __('Share vs. total executed records') }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Recent activity --}}
  <div class="row">
    <div class="col-lg-6">
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
          <strong>{{ __('Recent Executed Senders') }}</strong>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="thead-light">
                <tr>
                  <th class="text-nowrap">{{ __('MTCN') }}</th>
                  <th class="text-right">{{ __('Total') }}</th>
                  <th class="text-nowrap">{{ __('Customer') }}</th>
                  <th class="text-nowrap">{{ __('Executed At') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentSenders as $s)
                  <tr>
                    <td class="text-nowrap">{{ $s['mtcn'] }}</td>
                    <td class="text-right">$ {{ number_format((float)$s['total'],2) }}</td>
                    <td class="text-nowrap">
                      {{ trim(($s['first_name'] ?? '').' '.($s['last_name'] ?? '')) }}
                    </td>
                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($s['updated_at'])->format('Y-m-d H:i') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-3">{{ __('No executed senders yet.Z') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
          <strong>{{ __('Recent Executed Receivers') }}</strong>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="thead-light">
                <tr>
                  <th class="text-nowrap">{{ __('MTCN') }}</th>
                  <th class="text-right">{{ __('Amount') }}</th>
                  <th class="text-nowrap">{{ __('Receiver') }}</th>
                  <th class="text-nowrap">{{ __('Executed At') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentReceivers as $r)
                  <tr>
                    <td class="text-nowrap">{{ $r['mtcn'] }}</td>
                    <td class="text-right">{{ number_format((float)$r['amount_iqd'],0) }} {{ __('IQD') }}</td>
                    <td class="text-nowrap">
                      {{ trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? '')) }}
                    </td>
                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($r['updated_at'])->format('Y-m-d H:i') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-3">{{ __('No executed receivers yet.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Avatar modal --}}
<div class="modal fade @if($showAvatarModal) show d-block @endif" tabindex="-1" @if($showAvatarModal) style="background:rgba(0,0,0,.5);" @endif>
  <div class="modal-dialog">
    <div class="modal-content" wire:ignore.self>
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Change profile photo') }}</h5>
        <button type="button" class="close" aria-label="Close" wire:click="$set('showAvatarModal', false)">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form wire:submit.prevent="updateAvatar">
        <div class="modal-body">
          @if($avatarUpload)
            <div class="mb-2">
              <img src="{{ $avatarUpload->temporaryUrl() }}" class="rounded" style="width:120px;height:120px;object-fit:cover;">
            </div>
          @elseif($avatarUrl)
            <div class="mb-2">
              <img src="{{ $avatarUrl }}" class="rounded" style="width:120px;height:120px;object-fit:cover;">
            </div>
          @endif

          <input type="file" class="form-control @error('avatarUpload') is-invalid @enderror"
                 wire:model="avatarUpload" accept="image/*">
          @error('avatarUpload') <div class="invalid-feedback">{{ $message }}</div>@enderror

          <div wire:loading wire:target="avatarUpload" class="small text-muted mt-2">
            <span class="spinner-border spinner-border-sm mr-1"></span>{{ __('Uploading...') }}
          </div>

          <div class="form-text text-info mt-2">{{ __("We'll crop to square and optimize it.") }}</div>
        </div>

        <div class="modal-footer">
          @if($avatarKey)
            <button type="button" class="btn btn-outline-danger mr-auto" wire:click="removeAvatar" wire:loading.attr="disabled">
              <span wire:loading.remove>{{ __('Remove photo') }}</span>
              <span wire:loading><span class="spinner-border spinner-border-sm mr-1"></span>{{ __('Removing...') }}</span>
            </button>
          @endif

          <button type="button" class="btn btn-light" wire:click="$set('showAvatarModal', false)">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Save') }}</span>
            <span wire:loading><span class="spinner-border spinner-border-sm mr-1"></span>{{ __('Saving...') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

</div>
