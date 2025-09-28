<?php

namespace App\Http\Livewire\Sender;

use App\Models\Sender;
use App\Models\Country;
use Livewire\Component;
use App\Models\CountryTax;
use App\Models\CountryRule;
use App\Models\CountryLimit;
use App\Models\SenderBalance;
use App\Models\TaxBracketSet;
use App\Models\GeneralCountryTax;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralCountryLimit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Mail\AdminSenderCreated;
use App\Notifications\Telegram\TeleNotifySenderNew;

class SenderCreateLivewire extends Component
{
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
        $this->blockedIds = CountryRule::pluck('country_id')->all();

        // General limits fallback
        if ($g = GeneralCountryLimit::latest('id')->first()) {
            $this->minLimit = $g->min_value !== null ? (float)$g->min_value : null;
            $this->maxLimit = $g->max_value !== null ? (float)$g->max_value : null;
        }

        // Allowed countries only
        $this->availableCountries = Country::query()
            ->when($this->blockedIds, fn($q)=>$q->whereNotIn('id', $this->blockedIds))
            ->orderBy('en_name')
            ->get(['id','en_name','iso_code','ar_name','ku_name','flag_path'])
            ->toArray();
    }

    protected function rules(): array
    {
        $min = is_numeric($this->minLimit) ? $this->minLimit : 0.01;
        $max = is_numeric($this->maxLimit) ? $this->maxLimit : 999999;

        // compute remaining balance only for registers; null means "no balance cap"
        $remaining = $this->currentUserRemainingBalance();

        $totalRules = ['nullable','numeric','min:0'];
        if ($remaining !== null) {
            // if user is a register, enforce remaining balance on TOTAL
            $totalRules[] = 'max:' . $remaining;  // numeric max
        }

        return [
            'sender_first_name'      => ['required','string','min:2','max:60'],
            'sender_last_name'       => ['required','string','min:2','max:60'],
            'sender_phone_number'    => ['required','string','max:32','regex:/^\+?[0-9]{8,32}$/'],
            'sender_address'         => ['required','string','max:255'],

            'country_id'             => ['required','integer','exists:countries,id', 'not_in:'.implode(',', $this->blockedIds)],

            'amount'                 => ['required','numeric',"min:$min","max:$max"],
            'commission'             => ['required','numeric','min:0'],
            'total'                  => $totalRules,

            'receiver_phone_number'  => ['nullable','string','max:32','regex:/^\+?[0-9]{8,32}$/'],
            'receiver_first_name'    => ['nullable','string','min:2','max:60'],
            'receiver_last_name'     => ['nullable','string','min:2','max:60'],
        ];
    }


    protected function currentUserRemainingBalance(): ?float
    {
        $user = auth()->user();
        if (!$user || (int) $user->role !== 2) return null;         // only registers are limited
        if (!Schema::hasTable('sender_balances')) return null;       // guard if migration not run yet

        $incoming = (float) SenderBalance::where('user_id', $user->id)
            ->where('status', 'Incoming')
            ->sum('amount');

        // support both "Outgoing" and legacy "outcoming"
        $outgoing = (float) SenderBalance::where('user_id', $user->id)
            ->whereIn('status', ['Outgoing','outcoming'])
            ->sum('amount');

        return $incoming - $outgoing;
    }

    public function updatedCountryId($value): void
    {
        // (your limits logic exactly as you had)
        if ($this->country_id) {
            if ($cl = CountryLimit::where('country_id', $this->country_id)->first()) {
                $this->minLimit = $cl->min_value !== null ? (float)$cl->min_value : null;
                $this->maxLimit = $cl->max_value !== null ? (float)$cl->max_value : null;
            } elseif ($g = GeneralCountryLimit::latest('id')->first()) {
                $this->minLimit = $g->min_value !== null ? (float)$g->min_value : null;
                $this->maxLimit = $g->max_value !== null ? (float)$g->max_value : null;
            } else {
                $this->minLimit = $this->maxLimit = null;
            }
        } else {
            if ($g = GeneralCountryLimit::latest('id')->first()) {
                $this->minLimit = $g->min_value !== null ? (float)$g->min_value : null;
                $this->maxLimit = $g->max_value !== null ? (float)$g->max_value : null;
            } else {
                $this->minLimit = $this->maxLimit = null;
            }
        }

        // Re-validate amount (if present), recompute commission, validate total
        if ($this->amount !== null && $this->amount !== '') {
            $this->validateOnly('amount');
        }
        $this->computeCommission();
        $this->touched['total'] = true;
        $this->validateOnly('total');

        // Let sibling component know the country changed
        $this->emit('countryChanged', $this->country_id ? (int)$this->country_id : null);
    }



    public function updatedAmount(): void
    {
        // Recompute commission from brackets for the new amount
        $this->computeCommission();

        // Mark total "touched" and validate it so the UI can show invalid state
        $this->touched['total'] = true;
        $this->validateOnly('total');
    }


    protected function normalizeBrackets($brackets): array
    {
        // Decode JSON string if needed
        if (is_string($brackets)) {
            $decoded = json_decode($brackets, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $brackets = $decoded;
            } else {
                return [];
            }
        }

        if (!is_array($brackets)) return [];

        $out = [];
        foreach ($brackets as $row) {
            // Accept [min,max,fee]
            if (is_array($row) && array_keys($row) === [0,1,2]) {
                $min = (float)$row[0];
                $max = $row[1] === null || $row[1] === '' ? null : (float)$row[1];
                $fee = (float)$row[2];
                $out[] = [$min, $max, $fee];
                continue;
            }
            // Accept associative {min,max,fee} (keys may vary in case)
            if (is_array($row)) {
                $min = $row['min'] ?? $row['from'] ?? $row['start'] ?? null;
                $max = $row['max'] ?? $row['to']   ?? $row['end']   ?? null;
                $fee = $row['fee'] ?? $row['tax']  ?? $row['value'] ?? null;
                if ($min !== null && $fee !== null) {
                    $min = (float)$min;
                    $max = ($max === null || $max === '' || $max === 0) ? null : (float)$max; // open-ended
                    $fee = (float)$fee;
                    $out[] = [$min, $max, $fee];
                }
            }
        }

        // Sort by min ascending to be safe
        usort($out, fn($a,$b) => $a[0] <=> $b[0]);

        return $out;
    }

    protected function commissionFromBrackets(?array $brackets, float $amount): float
    {
        if (!$brackets) return 0.0;
        foreach ($brackets as $row) {
            if (!is_array($row) || count($row) !== 3) continue;
            [$min,$max,$fee] = $row;
            $min = (float)$min;
            $max = $max === null ? null : (float)$max; // null = open-ended
            if ($amount >= $min && ($max === null || $amount <= $max)) {
                return (float)$fee;
            }
        }
        return 0.0;
    }

    protected function computeCommission(): void
    {
        $this->commission = 0.0;
        $this->total = null;

        if (!is_numeric($this->amount)) return;
        $amount = (float)$this->amount;

        // Prefer country-specific brackets
        $brackets = null;
        if ($this->country_id) {
            $assign = CountryTax::with('set')->where('country_id', $this->country_id)->first();
            if ($assign && $assign->set) {
                $brackets = $assign->set->brackets_json;
            }
        }

        // Fallback general
        if (!$brackets) {
            $g = GeneralCountryTax::find(1); // if id is not always 1, use latest or config
            if ($g) $brackets = $g->brackets_json;
        }

        // Normalize shapes/JSON
        $normalized = $this->normalizeBrackets($brackets);

        $this->commission = $this->commissionFromBrackets($normalized, $amount);
        $this->total = $amount + $this->commission;
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
        $this->validate();

        // Use ISO2 code (not en_name) for the 'country' 2-char column
        $iso2 = (string) Country::whereKey($this->country_id)->value('en_name');

        // Ensure commission/total are up-to-date
        $this->computeCommission();
        $amount = (float) $this->amount;
        $fee    = (float) $this->commission;
        $total  = $amount + $fee;

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
                'country'      => $iso2, // ISO2
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
                        $iso2,
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
            $this->reset([
                'sender_first_name','sender_last_name','sender_phone_number','sender_address',
                'country_id','amount','commission','total',
                'receiver_phone_number','receiver_first_name','receiver_last_name',
            ]);
            $this->touched = [];

        } catch (\Throwable $e) {
            DB::rollBack();
            // \Log::error('Sender submit failed', ['ex'=>$e]);
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
}
