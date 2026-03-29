<?php

namespace App\Http\Livewire\General;

use App\Services\ExchangeRateFeedService;
use App\Services\SenderTransferQuoteService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class QuickSendMoneySidebarLivewire extends Component
{
    public ?int $country_id = null;
    public $amount = null;
    public float $commission = 0.0;
    public ?float $total = null;
    public ?float $receiverGets = null;
    public ?float $minLimit = null;
    public ?float $maxLimit = null;
    public ?float $remainingBalance = null;

    public bool $isAdmin = false;

    public array $availableCountries = [];
    public array $blockedIds = [];
    public array $exchangeRates = [];

    public ?string $exchangeUpdatedAt = null;
    public ?string $exchangeError = null;
    public ?string $exchangeTableHtml = null;
    public bool $exchangeIsStale = false;

    public function mount(): void
    {
        $user = auth()->user();

        $this->isAdmin = (int) ($user->role ?? 0) === 1;
        $this->blockedIds = $this->quoteService()->getBlockedCountryIds();
        $this->availableCountries = $this->quoteService()->getAvailableCountries();
        $this->remainingBalance = $this->quoteService()->getRemainingBalanceForUser($user);

        $this->applyQuote();
        $this->loadExchangeRates();
    }

    protected function rules(): array
    {
        $min = is_numeric($this->minLimit) ? (float) $this->minLimit : 0.01;
        $max = is_numeric($this->maxLimit) ? (float) $this->maxLimit : 999999999;

        return [
            'country_id' => ['required', 'integer', 'exists:countries,id', Rule::notIn($this->blockedIds)],
            'amount' => [
                'required',
                'numeric',
                "min:$min",
                "max:$max",
                function ($attribute, $value, $fail) {
                    if ($this->hasInsufficientBalance) {
                        $fail(__('Insufficient balance to send this amount (including fees).'));
                    }
                },
            ],
        ];
    }

    public function updated($property): void
    {
        if (!in_array($property, ['amount', 'country_id'], true)) {
            return;
        }

        $this->applyQuote();

        if ($property === 'country_id') {
            $this->validateOnly('country_id');

            if ($this->amount !== null && $this->amount !== '') {
                $this->validateOnly('amount');
            }

            return;
        }

        if ($this->amount !== null && $this->amount !== '') {
            $this->validateOnly('amount');
        }
    }

    public function submit()
    {
        $this->applyQuote();
        $this->validate();

        session()->flash('quick_send_prefilled', __('Quick Send details were carried over. Complete the sender information below.'));

        return redirect()->to(route('sender', [
            'prefill_amount' => number_format((float) $this->amount, 2, '.', ''),
            'prefill_country' => $this->country_id,
        ]));
    }

    public function refreshExchangeRates(): void
    {
        $this->loadExchangeRates(true);
        $this->dispatchBrowserEvent('exchange-rates-reload');
    }

    public function getHasInsufficientBalanceProperty(): bool
    {
        return $this->remainingBalance !== null
            && $this->total !== null
            && $this->total > $this->remainingBalance + 1e-6;
    }

    public function getCanSubmitProperty(): bool
    {
        if (!$this->country_id || !is_numeric($this->amount) || (float) $this->amount <= 0) {
            return false;
        }

        $amount = (float) $this->amount;

        if ($this->minLimit !== null && $amount < $this->minLimit) {
            return false;
        }

        if ($this->maxLimit !== null && $amount > $this->maxLimit) {
            return false;
        }

        return !$this->hasInsufficientBalance;
    }

    public function getBalanceTitleProperty(): string
    {
        return $this->remainingBalance !== null
            ? __('Register Balance')
            : __('Sending Allowance');
    }

    public function getBalanceValueProperty(): string
    {
        if ($this->remainingBalance === null) {
            return __('Unlimited');
        }

        return '$' . number_format($this->remainingBalance, 2);
    }

    public function getRemainingAfterTransferProperty(): ?float
    {
        if ($this->remainingBalance === null || $this->total === null) {
            return null;
        }

        return round($this->remainingBalance - $this->total, 2);
    }

    public function render()
    {
        return view('components.general.quick-send-money-sidebar');
    }

    protected function applyQuote(): void
    {
        $quote = $this->quoteService()->quote($this->country_id, $this->amount);

        $this->commission = $quote['commission'];
        $this->total = $quote['total'];
        $this->receiverGets = $quote['receiver_gets'];
        $this->minLimit = $quote['min'];
        $this->maxLimit = $quote['max'];
    }

    protected function loadExchangeRates(bool $forceRefresh = false): void
    {
        $feed = $this->exchangeRateService()->getFeed($forceRefresh);

        $this->exchangeRates = $feed['rates'] ?? [];
        $this->exchangeUpdatedAt = $feed['updated_at'] ?? null;
        $this->exchangeError = $feed['error'] ?? null;
        $this->exchangeTableHtml = $feed['table_html'] ?? null;
        $this->exchangeIsStale = (bool) ($feed['is_stale'] ?? false);
    }

    protected function quoteService(): SenderTransferQuoteService
    {
        return app(SenderTransferQuoteService::class);
    }

    protected function exchangeRateService(): ExchangeRateFeedService
    {
        return app(ExchangeRateFeedService::class);
    }
}
