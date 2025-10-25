<?php

namespace App\Http\Livewire\Action;

use App\Models\Sender;
use Livewire\Component;
use App\Models\SenderBalance;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Telegram\TeleNotifySenderAction;

class SenderActionLivewire extends Component
{
    public int $senderId;

    // modal fields (unique per-row component instance)
    public ?int $execId = null;

    // Sender fields
    public string $oldMtcn = '';
    public string $newMtcn = '';

    public string $oldFirstName = '';
    public string $newFirstName = '';

    public string $oldLastName = '';
    public string $newLastName = '';

    // Receiver fields (NEW)
    public string $oldReceiverFirstName = '';
    public string $newReceiverFirstName = '';

    public string $oldReceiverLastName = '';
    public string $newReceiverLastName = '';

    // Payout fields
    public ?string $payoutAmount = null;    // decimal text, e.g. "6,500.50"
    public ?string $payoutCurrency = 'USD'; // ISO 4217 (3 letters)

    public ?float $execTotal = null;

    public bool $isAdmin = false;

    protected function rules(): array
    {
        return [
            'newMtcn'       => ['required','digits:10'],
            'newFirstName'  => ['required','string','max:100'],
            'newLastName'   => ['required','string','max:100'],

            // Receiver fields are optional (nullable)
            'newReceiverFirstName' => ['nullable','string','max:100'],
            'newReceiverLastName'  => ['nullable','string','max:100'],

            'payoutAmount'   => ['required','regex:/^\s*\d{1,18}([,]?\d{3})*(\.\d{1,2})?\s*$/'],
            'payoutCurrency' => ['required','string','size:3','alpha'], // Changed to size:3 and alpha
        ];
    }

    public function mount(int $senderId): void
    {
        $this->senderId = $senderId;
        $this->isAdmin = ((int) auth()->user()->role) === 1;
    }

    // Debug method to check what's being submitted
    public function updatedNewMtcn($value)
    {
        // \Log::info('MTCN updated', ['value' => $value]);
    }

    public function updatedPayoutAmount($value)
    {
        // \Log::info('Payout amount updated', ['value' => $value]);
    }


    private function normalizeMoney(?string $raw): ?string
    {
        if ($raw === null || $raw === '') return null;
        // remove commas/spaces; keep digits and dot
        $v = preg_replace('/[^\d\.]/', '', str_replace(',', '', $raw));
        if ($v === '' || !is_numeric($v)) return null;
        return number_format((float)$v, 2, '.', '');
    }

    // ----- UI helpers -----

    public function getSenderProperty(): ?Sender
    {
        return Sender::find($this->senderId);
    }

    private function modalId(): string
    {
        // unique modal DOM id for this row instance
        return 'executionProcess-'.$this->senderId;
    }
    
    public function closeModal(): void
    {
        $this->execId = null;
        $this->resetValidation();
        $this->dispatchBrowserEvent('modal:close', ['id' => $this->modalId()]);
    }

    // ----- Actions (Pending -> modal) -----
    public function askExecute(): void
    {
        if (!$this->isAdmin) abort(403);

        $s = Sender::query()
            ->where('id', $this->senderId)
            ->whereIn('status', ['Pending','Rejected']) // allow Rejected too
            ->first();

        if (!$s) {
            $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
            return;
        }

        $this->execId       = $s->id;
        $this->oldMtcn      = (string) $s->mtcn;
        $this->oldFirstName = (string) $s->first_name;
        $this->oldLastName  = (string) $s->last_name;

        $this->newMtcn      = (string) $s->mtcn;
        $this->newFirstName = mb_strtoupper((string) $s->first_name, 'UTF-8');
        $this->newLastName  = mb_strtoupper((string) $s->last_name,  'UTF-8');

        // Load receiver fields (NEW)
        $this->oldReceiverFirstName = (string) ($s->r_first_name ?? '');
        $this->oldReceiverLastName  = (string) ($s->r_last_name ?? '');
        $this->newReceiverFirstName = mb_strtoupper((string) ($s->r_first_name ?? ''), 'UTF-8');
        $this->newReceiverLastName  = mb_strtoupper((string) ($s->r_last_name ?? ''), 'UTF-8');

        $this->execTotal = (float) $s->total;

        $this->payoutAmount   = number_format((float)$s->total, 2); // prefill with total
        $this->payoutCurrency = $this->payoutCurrency ?: 'USD';     // or IQD per your logic

        $this->dispatchBrowserEvent('modal:open', ['id' => $this->modalId()]);
    }

    public function markExecutedConfirmed(): void
    {
        if (!$this->isAdmin) abort(403);
        
        // Add debugging
        // \Log::info('markExecutedConfirmed called', [
        //     'execId' => $this->execId,
        //     'newMtcn' => $this->newMtcn,
        //     'payoutAmount' => $this->payoutAmount,
        // ]);

        $this->validate();

        DB::beginTransaction();
        try {
            $sender = Sender::query()
                ->where('id', $this->execId)
                ->whereIn('status', ['Pending','Rejected']) // allow either
                ->lockForUpdate()
                ->first();

            if (!$sender) {
                DB::rollBack();
                // \Log::warning('Sender not found or status invalid', ['execId' => $this->execId]);
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'warning',
                    'message' => __('Not allowed or already processed'),
                ]);
                $this->closeModal();
                return;
            }

            // Update sender AND receiver fields (NEW)
            $sender->update([
                'mtcn'         => $this->newMtcn,
                'first_name'   => $this->newFirstName,
                'last_name'    => $this->newLastName,
                'r_first_name' => $this->newReceiverFirstName ?: null,
                'r_last_name'  => $this->newReceiverLastName ?: null,
            ]);

            $amount   = $this->normalizeMoney($this->payoutAmount);
            $currency = strtoupper(trim((string)$this->payoutCurrency));

            // \Log::info('Payout normalized', ['amount' => $amount, 'currency' => $currency]);

            // Safety check (rules already required them)
            if (!$amount || !$currency) {
                throw new \RuntimeException('Invalid payout fields.');
            }

            $payouts = $sender->payouts ?? [];
            $payouts[] = [
                'amount'       => $amount,      
                'currency'     => $currency,    
                'by'           => auth()->id(),
                'at'           => now()->toIso8601String(),
                'mtcn_before'  => (string) $this->oldMtcn,
                'mtcn_after'   => (string) $this->newMtcn,
            ];
            $sender->forceFill(['payouts' => $payouts])->save();

            // Flip to Executed (does notifications too)
            $this->internalChangeStatus($sender, 'Executed');

            DB::commit();

            // \Log::info('Sender executed successfully', ['sender_id' => $sender->id]);

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Updated & marked as Executed'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            // \Log::error('Sender exec confirm failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Operation failed: ') . $e->getMessage(),
            ]);
        }

        // close FIRST, then refresh parent so modal never sticks
        $this->closeModal();
        $this->emitUp('actions:refresh');
    }

    // ----- Other flips -----

    public function markPending(): void
    {
        if (!$this->isAdmin) abort(403);

        $sender = Sender::query()
            ->where('id', $this->senderId)
            ->whereIn('status', ['Executed','Rejected'])
            ->first();

        if (!$sender) {
            $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
            return;
        }

        $this->internalChangeStatus($sender, 'Pending');
        $this->emitUp('actions:refresh');
    }

    public function markRejected(): void
    {
        if (!$this->isAdmin) abort(403);

        $sender = Sender::query()
            ->where('id', $this->senderId)
            ->whereIn('status', ['Pending','Executed'])
            ->first();

        if (!$sender) {
            $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
            return;
        }

        $this->internalChangeStatus($sender, 'Rejected');
        $this->emitUp('actions:refresh');
    }

    // ----- Core status change for Sender -----

    protected function internalChangeStatus(Sender $sender, string $to): void
    {
        if (!in_array($to, ['Pending','Executed','Rejected'], true)) {
            $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Invalid status')]);
            return;
        }

        $from = (string) $sender->status;
        if ($from === $to) {
            $this->dispatchBrowserEvent('alert', ['type'=>'info','message'=>__('No change')]);
            return;
        }

        $ok = $sender->update(['status' => $to]);

        if (!$ok) {
            $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
            return;
        }

        if ($to === 'Rejected') {
            // only for registers
            $role = (int) optional($sender->user)->role;
            if ($role === 2) {
                DB::transaction(function () use ($sender) {
                    // lock user rows to avoid race conditions with concurrent flips
                    DB::table('sender_balances')
                        ->where('user_id', $sender->user_id)
                        ->lockForUpdate()
                        ->get();

                    // prevent duplicate refund if someone toggles repeatedly
                    $alreadyRefunded = SenderBalance::query()
                        ->where('sender_id', $sender->id)
                        ->where('status', 'Incoming')
                        ->where('note', 'like', 'Sender Rejected%')
                        ->exists();

                    if (!$alreadyRefunded) {
                        SenderBalance::create([
                            'user_id'   => $sender->user_id,
                            'amount'    => (float) $sender->total,
                            'status'    => 'Incoming',
                            'sender_id' => $sender->id,
                            'note'      => 'Sender Rejected (' . trim(($sender->first_name ?? '') . ' ' . ($sender->last_name ?? '')) . ')',
                        ]);
                    }
                });
            }
        }

        $this->dispatchBrowserEvent('alert', [
            'type'=>'success',
            'message'=>__('Marked :status', ['status'=>$to])
        ]);

        // Telegram
        try {
            Notification::route('toTelegram', null)->notify(new TeleNotifySenderAction(
                $sender->id,
                $sender->mtcn,
                trim(($sender->first_name ?? '').' '.($sender->last_name ?? '')),
                $sender->total,
                $from,
                $to,
                auth()->user()->name ?? 'system'
            ));
        } catch (\Throwable $e) {
            // Log::warning('TeleNotifySenderAction failed', ['e'=>$e->getMessage()]);
        }

        // WhatsApp when moving to Executed from either Pending OR Rejected
        if ($to === 'Executed' && in_array($from, ['Pending','Rejected'], true)) {
            $this->sendWhatsAppSenderExecuted($sender);
        }
    }

    protected function sendWhatsAppSenderExecuted(Sender $sender): void
    {
        try {
            $phoneId  = config('services.whatsapp.phone_id');
            $token    = config('services.whatsapp.token');
            $template = config('services.whatsapp.template_sender');
            $lang     = config('services.whatsapp.lang', 'en');

            if (!$phoneId || !$token || !$template) {
                return;
            }

            // Two recipients: agency (owner) + customer (sender phone)
            $toNumberAgency   = optional(optional($sender->user)->profile)->phone;
            $toNumberCustomer = $sender->phone ?? null;

            // Remove leading '+' if present
            $toNumberAgency   = $toNumberAgency   ? ltrim(trim($toNumberAgency), '+')   : null;
            $toNumberCustomer = $toNumberCustomer ? ltrim(trim($toNumberCustomer), '+') : null;

            if (!$toNumberAgency && !$toNumberCustomer) {
                return;
            }

            // Per-recipient URLs
            $urlForAgency   = 'receipts-executed/'.$sender->id.'/agent';
            $urlForCustomer = 'receipts-executed/'.$sender->id.'/customer';

            $customerName = 'Customer';
            $mtcn         = (string) $sender->mtcn;

            $anySuccess = false;
            $lastError  = null;

            $sendOne = function (?string $to, string $url) use (
                $token, $phoneId, $template, $lang, $customerName, $mtcn, $sender, &$anySuccess, &$lastError
            ) {
                if (!$to) return;

                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $template,
                        'language' => ['code' => $lang],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'parameter_name' => 'text', 'text' => $customerName],
                                    ['type' => 'text', 'parameter_name' => 'mtcn', 'text' => 'mtcn-'.$mtcn],
                                ],
                            ],
                            [
                                'type' => 'button',
                                'sub_type' => 'url',
                                'index' => '0',
                                'parameters' => [
                                    ['type' => 'text', 'text' => (string) $url],
                                ],
                            ],
                        ],
                    ],
                ];

                $resp = Http::withToken($token)->acceptJson()->asJson()
                    ->post("https://graph.facebook.com/v22.0/{$phoneId}/messages", $payload);

                if ($resp->successful()) {
                    $anySuccess = true;
                } else {
                    $lastError = ['status' => $resp->status(), 'body' => $resp->body(), 'to' => $to, 'url' => $url];
                }
            };

            // Send to both recipients
            $sendOne($toNumberAgency,   $urlForAgency);
            $sendOne($toNumberCustomer, $urlForCustomer);

            // Single toast after both attempts
            if ($anySuccess) {
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('WhatsApp push sent')]);
            } elseif ($lastError) {
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'warning',
                    'message' => __('WhatsApp push failed (:code)', ['code' => $lastError['status']]),
                ]);
            }
        } catch (\Throwable $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('WhatsApp push failed')]);
        }
    }

    public function render()
    {
        return view('components.reuse.sender-action', [
            'sender' => $this->sender,
            'modalId' => $this->modalId(),
        ]);
    }
}