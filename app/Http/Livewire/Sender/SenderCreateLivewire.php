<?php

namespace App\Http\Livewire\Sender;

use App\Models\State;
use App\Models\Sender;
use App\Models\Country;
use Livewire\Component;
use App\Models\SenderBalance;
use App\Services\SenderTransferQuoteService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Mail\AdminSenderCreated;
use App\Notifications\Telegram\TeleNotifySenderNew;

class SenderCreateLivewire extends Component
{
    public $state_id = null;                 // NEW
    public array $availableStates = [];      // NEW
    public bool $stateRequired = false;

    protected array $usCaIso = ['us','ca'];  // NEW

    // Sender
    public $sender_first_name, $sender_last_name, $sender_phone_number, $sender_address;

    // Country (ID-based)
    public $country_id = null;

    // Money
    public $amount, $commission, $total;

    // Receiver (optional)
    public $receiver_phone_number, $receiver_first_name, $receiver_last_name;

    // Allowed countries for Select2
    public array $availableCountries = [];

    // Limits
    public ?float $minLimit = null;
    public ?float $maxLimit = null;

    public array $touched = [];
    protected array $blockedIds = [];

    public function mount(): void
    {
        $this->blockedIds = $this->quoteService()->getBlockedCountryIds();
        $this->availableCountries = $this->quoteService()->getAvailableCountries();

        $this->applyLimits();
        $this->applyQuickPrefill();
    }

    protected function rules(): array
    {
        $min = is_numeric($this->minLimit) ? $this->minLimit : 0.01;
        $max = is_numeric($this->maxLimit) ? $this->maxLimit : 999999;

        $remaining = $this->currentUserRemainingBalance();
        $totalRules = ['nullable','numeric','min:0'];
        if ($remaining !== null) {
            $totalRules[] = 'max:' . $remaining;
        }

        $rules = [
            'sender_first_name'      => ['required','string','min:2','max:60'],
            'sender_last_name'       => ['required','string','min:2','max:60'],
            'sender_phone_number'    => ['required','string','max:32','regex:/^\+?[0-9]{8,32}$/'],
            'sender_address'         => ['required','string','max:255'],

            'country_id'             => ['required','integer','exists:countries,id', Rule::notIn($this->blockedIds)],

            'amount'                 => ['required','numeric',"min:$min","max:$max"],
            'commission'             => ['required','numeric','min:0'],
            'total'                  => $totalRules,

            'receiver_phone_number'  => ['nullable','string','max:32','regex:/^\+?[0-9]{8,32}$/'],
            'receiver_first_name'    => ['nullable','string','min:2','max:60'],
            'receiver_last_name'     => ['nullable','string','min:2','max:60'],
        ];

        // Conditionally require state for US/CA and ensure it belongs to the selected country
        $iso = $this->selectedCountryIso();
        $stateExistsForCountry = Rule::exists('states', 'id')->where(fn($q) =>
            $q->where('country_id', (int) $this->country_id)
        );

        if ($iso && in_array(strtolower($iso), $this->usCaIso, true)) {
            $rules['state_id'] = ['required','integer', $stateExistsForCountry];
        } else {
            $rules['state_id'] = ['nullable','integer', $stateExistsForCountry];
        }

        return $rules;
    }

    protected function selectedCountryIso(): ?string
    {
        return $this->quoteService()->getCountryIso($this->country_id ? (int) $this->country_id : null);
    }

    protected function currentUserRemainingBalance(): ?float
    {
        return $this->quoteService()->getRemainingBalanceForUser(auth()->user());
    }

    public function updatedCountryId(): void
    {
        $this->hydrateCountryContext();
    }

    public function updatedAmount(): void
    {
        // Recompute commission from brackets for the new amount
        $this->computeCommission();

        // Mark total "touched" and validate it so the UI can show invalid state
        $this->touched['total'] = true;
        $this->validateOnly('total');
    }
    protected function computeCommission(): void
    {
        $quote = $this->quoteService()->quote($this->country_id ? (int) $this->country_id : null, $this->amount);

        $this->commission = $quote['commission'];
        $this->total = $quote['total'];
        $this->minLimit = $quote['min'];
        $this->maxLimit = $quote['max'];
    }

    public function updated($property): void
    {
        $this->touched[$property] = true;

        if (in_array($property, ['sender_first_name','sender_last_name','receiver_first_name','receiver_last_name'], true)) {
            $this->$property = mb_strtoupper((string)$this->$property, 'UTF-8');
        }

        // If user edits commission manually, still keep total in sync
        if ($property === 'commission') {
            $a = is_numeric($this->amount) ? (float)$this->amount : 0.0;
            $c = is_numeric($this->commission) ? (float)$this->commission : 0.0;
            $this->total = $a + $c;
            $this->touched['total'] = true;
            $this->validateOnly('total');
        }

        $this->validateOnly($property);
    }


    public function submit()
    {
        $this->computeCommission();
        $this->validate();

        $country = Country::query()
            ->select(['id', 'en_name', 'iso_code'])
            ->findOrFail((int) $this->country_id);

        $amount = (float) $this->amount;
        $fee    = (float) $this->commission;
        $total  = (float) $this->total;

        $currentUserId = (int) auth()->id();
        $currentRole   = (int) (auth()->user()->role ?? 0);

        // If your DB uses 'outcoming' instead of 'Outgoing', set this to 'outcoming'
        $OUTGOING_LABEL = 'Outgoing';

        try {
            DB::beginTransaction();

            // ===== 1) If this is a register (role=2), enforce remaining balance
            if ($currentRole === 2) {
                // lock current user's balance rows to avoid race conditions
                DB::table('sender_balances')
                    ->where('user_id', $currentUserId)
                    ->lockForUpdate()
                    ->get();

                $incoming = (float) DB::table('sender_balances')
                    ->where('user_id', $currentUserId)
                    ->where('status', 'Incoming')
                    ->sum('amount');

                // support both 'Outgoing' and 'outcoming' if your data has either
                $outgoing = (float) DB::table('sender_balances')
                    ->where('user_id', $currentUserId)
                    ->whereIn('status', ['Outgoing','outcoming'])
                    ->sum('amount');

                $remaining = $incoming - $outgoing;

                if ($total > $remaining + 1e-6) {
                    DB::rollBack();
                    $this->dispatchBrowserEvent('alert', [
                        'type' => 'error',
                        'message' => __('Insufficient balance to send this amount (including fees).'),
                    ]);
                    return;
                }
            }

            // ===== 2) Create Sender row
            $timePart = substr((string) (int) (microtime(true) * 1000), -7);
            $randPart = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
            $mtcn = $timePart.$randPart;

            $sender = Sender::create([
                'user_id'      => $currentUserId,
                'first_name'   => $this->sender_first_name,
                'last_name'    => $this->sender_last_name,
                'phone'        => $this->sender_phone_number,
                'address'      => $this->sender_address,
                // Preserve the existing storage format: the sender table keeps the country display name.
                'country'      => (string) $country->en_name,
                'state_id'     => $this->state_id,
                'amount'       => $amount,
                'tax'          => $fee,
                'total'        => $total,
                'r_first_name' => $this->receiver_first_name ?: null,
                'r_last_name'  => $this->receiver_last_name ?: null,
                'r_phone'      => $this->receiver_phone_number ?: null,
                'mtcn'         => $mtcn,
            ]);

            // ===== 3) For registers only, deduct immediately from balance (Outgoing/outcoming)
            if ($currentRole === 2) {
                SenderBalance::create([
                    'user_id'   => $currentUserId,
                    'amount'    => $total,
                    'status'    => $OUTGOING_LABEL, // or 'outcoming' if that’s your enum
                    'sender_id' => $sender->id,
                    'note'      => 'Sender created (auto-deduct)',
                ]);
            }

            DB::commit();

            // ===== 4) UI success (local)
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Sender has been added successfully'),
            ]);

            // ===== 5) External notifications (non-transactional)
            try {
                Notification::route('toTelegram', null)
                    ->notify(new TeleNotifySenderNew(
                        $sender->id,
                        $mtcn,
                        $this->sender_first_name . ' ' .$this->sender_last_name,
                        $this->sender_phone_number,
                        (string) $country->en_name,
                        (float)$this->amount,
                        (float)$this->commission,
                        $total,
                        $this->receiver_first_name .' '.$this->receiver_last_name,
                        $this->receiver_phone_number ?: null,
                        auth()->user()->name
                    ));

                $this->dispatchBrowserEvent('alert', [
                    'type' => 'success',
                    'message' => __('Push Activated'),
                ]);
            } catch (\Exception $e) {
                // log instead of dd($e)
                // \Log::warning('Telegram notify failed', ['ex'=>$e]);
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'warning',
                    'message' => __('Did not saved in cloud!'),
                ]);
                // continue; we don't abort the submission
            }

            try {
                $adminEmail = config('mail.admin_address', env('ADMIN_EMAIL'));
                // $adminEmail = app('master_email');
                if ($adminEmail) {
                    Notification::route('mail', $adminEmail)
                        ->notify(new AdminSenderCreated($sender, auth()->user()->name));
                }

                $this->dispatchBrowserEvent('alert', [
                    'type' => 'success',
                    'message' => __('Submited in System'),
                ]);
            } catch (\Exception $e) {
                // \Log::warning('Admin email failed', ['ex'=>$e]);
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'warning',
                    'message' => __('Did not saved in cloud!'),
                ]);
            }

            // ===== 6) Open receipts
            $this->dispatchBrowserEvent('open-receipts', [
                'urls' => [
                    route('receipts.dompdf.show', ['sender' => $sender->id, 'type' => 'both'])
                ],
            ]);

            // ===== 7) Reset form
            $this->resetFormState();

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Something went wrong!'),
            ]);
            return;
        }
    }



    public function render()
    {
        return view('components.forms.sender-create');
    }

    public function getInputClass($field): string
    {
        $base = 'form-control';
        $hasError  = $this->getErrorBag()->has($field);
        $isTouched = isset($this->touched[$field]) && $this->touched[$field];
        if ($hasError && $isTouched) return $base.' is-invalid';
        if ($isTouched && !$hasError) return $base.' is-valid';
        return $base;
    }

    protected function quoteService(): SenderTransferQuoteService
    {
        return app(SenderTransferQuoteService::class);
    }

    protected function applyLimits(): void
    {
        $limits = $this->quoteService()->getLimits($this->country_id ? (int) $this->country_id : null);
        $this->minLimit = $limits['min'];
        $this->maxLimit = $limits['max'];
    }

    protected function applyQuickPrefill(): void
    {
        $prefillCountryId = request()->integer('prefill_country');
        $prefillAmount = request()->query('prefill_amount');

        if ($prefillCountryId) {
            $this->country_id = $prefillCountryId;
            $this->hydrateCountryContext(false, false);
        }

        if (is_numeric($prefillAmount)) {
            $this->amount = (float) $prefillAmount;
            $this->computeCommission();
        }
    }

    protected function hydrateCountryContext(bool $emit = true, bool $validate = true): void
    {
        $this->applyLimits();

        $this->state_id = null;
        $this->availableStates = [];
        $this->stateRequired = false;

        $iso = $this->selectedCountryIso();
        if ($iso && in_array(strtolower($iso), $this->usCaIso, true)) {
            $this->stateRequired = true;
            $this->availableStates = State::where('country_id', (int) $this->country_id)
                ->orderBy('en_name')
                ->get(['id', 'code', 'en_name', 'ar_name', 'ku_name'])
                ->toArray();
        }

        if ($validate && $this->amount !== null && $this->amount !== '') {
            $this->validateOnly('amount');
        }

        $this->computeCommission();

        if ($validate) {
            $this->touched['total'] = true;
            $this->validateOnly('total');
        }

        if ($emit) {
            $this->emit('countryChanged', $this->country_id ? (int) $this->country_id : null);
        }
    }

    protected function resetFormState(): void
    {
        $this->reset([
            'state_id',
            'sender_first_name',
            'sender_last_name',
            'sender_phone_number',
            'sender_address',
            'country_id',
            'amount',
            'commission',
            'total',
            'receiver_phone_number',
            'receiver_first_name',
            'receiver_last_name',
        ]);

        $this->availableStates = [];
        $this->stateRequired = false;
        $this->touched = [];

        $this->applyLimits();
        $this->computeCommission();
        $this->emit('countryChanged', null);
    }
}
