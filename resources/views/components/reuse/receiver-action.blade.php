@php
  $status = (string) ($receiver->status ?? '');
@endphp

<div class="d-inline-block">
  <div class="btn-group" role="group" aria-label="Receiver actions">
    @if ($status === 'Pending')
      <button class="btn btn-sm btn-outline-success"
              onclick="confirm('{{ __('Mark as Executed?') }}') || event.stopImmediatePropagation()"
              wire:click="markExecuted" wire:loading.attr="disabled" wire:target="markExecuted"
              data-toggle="tooltip" title="{{ __('Execute') }}">
        <i class="fas fa-check"></i>
      </button>
      <button class="btn btn-sm btn-outline-danger"
              onclick="confirm('{{ __('Mark as Rejected?') }}') || event.stopImmediatePropagation()"
              wire:click="markRejected" wire:loading.attr="disabled" wire:target="markRejected"
              data-toggle="tooltip" title="{{ __('Reject') }}">
        <i class="fas fa-times"></i>
      </button>

    @elseif ($status === 'Executed')
      <button class="btn btn-sm btn-outline-primary"
              onclick="confirm('{{ __('Mark as Pending?') }}') || event.stopImmediatePropagation()"
              wire:click="markPending" wire:loading.attr="disabled" wire:target="markPending"
              data-toggle="tooltip" title="{{ __('Move to Pending') }}">
        <i class="fas fa-retweet"></i>
      </button>
      <button class="btn btn-sm btn-outline-danger"
              onclick="confirm('{{ __('Mark as Rejected?') }}') || event.stopImmediatePropagation()"
              wire:click="markRejected" wire:loading.attr="disabled" wire:target="markRejected"
              data-toggle="tooltip" title="{{ __('Reject') }}">
        <i class="fas fa-times"></i>
      </button>

    @else {{-- Rejected --}}
      <button class="btn btn-sm btn-outline-primary"
              onclick="confirm('{{ __('Mark as Pending?') }}') || event.stopImmediatePropagation()"
              wire:click="markPending" wire:loading.attr="disabled" wire:target="markPending"
              data-toggle="tooltip" title="{{ __('Move to Pending') }}">
        <i class="fas fa-retweet"></i>
      </button>
      <button class="btn btn-sm btn-outline-success"
              onclick="confirm('{{ __('Mark as Executed?') }}') || event.stopImmediatePropagation()"
              wire:click="markExecuted" wire:loading.attr="disabled" wire:target="markExecuted"
              data-toggle="tooltip" title="{{ __('Execute') }}">
        <i class="fas fa-check"></i>
      </button>
    @endif
  </div>
</div>
