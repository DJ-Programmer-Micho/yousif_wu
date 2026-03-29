<?php

namespace App\Http\Livewire\Sender;

use App\Models\Sender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SenderCreateLivewireV2 extends SenderCreateLivewire
{
    public int $currentStep = 1;
    public string $senderSearch = '';
    public bool $hasQuickPrefill = false;

    protected int $maxStep = 4;

    protected array $stepFieldMap = [
        1 => [
            'sender_first_name',
            'sender_last_name',
            'sender_phone_number',
            'sender_address',
        ],
        2 => [
            'country_id',
            'state_id',
            'amount',
            'commission',
            'total',
        ],
        3 => [
            'receiver_first_name',
            'receiver_last_name',
            'receiver_phone_number',
        ],
    ];

    public function mount(): void
    {
        parent::mount();
        $this->applySessionQuickPrefill();
        $this->hasQuickPrefill = $this->hasStepTwoPrefill();
        $this->currentStep = 1;
    }

    public function nextStep(): void
    {
        $this->validateStep($this->currentStep);

        if ($this->currentStep < $this->maxStep) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function loadSender(int $senderId): void
    {
        $sender = $this->senderSidebarQuery()->find($senderId);
        if (!$sender) {
            return;
        }

        $this->sender_first_name = mb_strtoupper((string) $sender->first_name, 'UTF-8');
        $this->sender_last_name = mb_strtoupper((string) $sender->last_name, 'UTF-8');
        $this->sender_phone_number = (string) $sender->phone;
        $this->sender_address = (string) $sender->address;

        foreach ($this->stepFieldMap[1] as $field) {
            $this->touched[$field] = true;
        }

        $this->currentStep = 1;

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Sender information loaded from previous history.'),
        ]);
    }

    public function submit()
    {
        $this->validateStep(1);
        $this->validateStep(2);
        $this->validateStep(3);
        $this->currentStep = 4;

        return parent::submit();
    }

    public function render()
    {
        return view('components.forms.sender-create-v2');
    }

    public function getSenderSidebarResultsProperty(): Collection
    {
        $term = trim($this->senderSearch);

        $rows = $this->senderSidebarQuery()
            ->when($term !== '', function (Builder $query) use ($term): void {
                $query->where(function (Builder $inner) use ($term): void {
                    foreach (preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY) ?: [$term] as $token) {
                        $inner->orWhere('first_name', 'like', '%' . $token . '%')
                            ->orWhere('last_name', 'like', '%' . $token . '%');
                    }

                    $inner->orWhere('phone', 'like', '%' . $term . '%');
                });
            })
            ->latest('id')
            ->limit($term !== '' ? 18 : 12)
            ->get(['id', 'first_name', 'last_name', 'phone', 'address', 'country', 'created_at']);

        return $rows
            ->unique(function (Sender $sender): string {
                return mb_strtolower(trim($sender->first_name . ' ' . $sender->last_name . '|' . $sender->phone));
            })
            ->take(6)
            ->values();
    }

    public function getSelectedCountryNameProperty(): ?string
    {
        if (!$this->country_id) {
            return null;
        }

        foreach ($this->availableCountries as $country) {
            if ((int) ($country['id'] ?? 0) === (int) $this->country_id) {
                return $country['en_name'];
            }
        }

        return null;
    }

    public function getSelectedStateNameProperty(): ?string
    {
        if (!$this->state_id) {
            return null;
        }

        foreach ($this->availableStates as $state) {
            if ((int) ($state['id'] ?? 0) === (int) $this->state_id) {
                return $state['en_name'];
            }
        }

        return null;
    }

    public function getProgressPercentProperty(): int
    {
        return (int) round(($this->currentStep / $this->maxStep) * 100);
    }

    public function getHasReceiverSnapshotProperty(): bool
    {
        return filled($this->receiver_first_name)
            || filled($this->receiver_last_name)
            || filled($this->receiver_phone_number);
    }

    protected function validateStep(int $step): void
    {
        $fields = $this->stepFieldMap[$step] ?? [];
        if (empty($fields)) {
            return;
        }

        if ($step === 2) {
            $this->computeCommission();
        }

        foreach ($fields as $field) {
            $this->touched[$field] = true;
        }

        $rules = $this->rules();
        $stepRules = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $rules)) {
                $stepRules[$field] = $rules[$field];
            }
        }

        $this->validate($stepRules);
    }

    protected function applySessionQuickPrefill(): void
    {
        $prefill = session('quick_send_prefill', []);
        if (!is_array($prefill)) {
            return;
        }

        $countryId = isset($prefill['country_id']) ? (int) $prefill['country_id'] : null;
        $amount = $prefill['amount'] ?? null;

        if ($countryId) {
            $this->country_id = $countryId;
            $this->hydrateCountryContext(false, false);
        }

        if (is_numeric($amount)) {
            $this->amount = (float) $amount;
            $this->computeCommission();
        }
    }

    protected function hasStepTwoPrefill(): bool
    {
        return (bool) $this->country_id || is_numeric($this->amount);
    }

    protected function senderSidebarQuery(): Builder
    {
        $query = Sender::query();

        if ((int) (auth()->user()->role ?? 0) === 2) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    protected function resetFormState(): void
    {
        parent::resetFormState();

        $this->currentStep = 1;
        $this->senderSearch = '';
        $this->hasQuickPrefill = false;
    }
}
