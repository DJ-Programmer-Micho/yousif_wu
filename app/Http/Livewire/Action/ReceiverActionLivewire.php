<?php

namespace App\Http\Livewire\Action;

use Livewire\Component;
use App\Models\Receiver;
use App\Models\ReceiverBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Notifications\Telegram\TeleNotifyReceiverAction;

class ReceiverActionLivewire extends Component
{
    public int $receiverId;
    public bool $isAdmin = false;

    public function mount(int $receiverId): void
    {
        $this->receiverId = $receiverId;
        $this->isAdmin = ((int) auth()->user()->role) === 1;
    }

    public function getReceiverProperty(): ?Receiver
    {
        return Receiver::find($this->receiverId);
    }

    // ----- Buttons -----

    public function markExecuted(): void
    {
        if (!$this->isAdmin) abort(403);

        $receiver = Receiver::query()
            ->where('id', $this->receiverId)
            ->whereIn('status', ['Pending','Rejected'])
            ->first();

        if (!$receiver) {
            $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
            return;
        }

        $this->internalChangeStatusReceiver($receiver, 'Executed');
        $this->emitUp('actions:refresh');
    }

    public function markPending(): void
    {
        if (!$this->isAdmin) abort(403);

        $receiver = Receiver::query()
            ->where('id', $this->receiverId)
            ->whereIn('status', ['Executed','Rejected'])
            ->first();

        if (!$receiver) {
            $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
            return;
        }

        $this->internalChangeStatusReceiver($receiver, 'Pending');
        $this->emitUp('actions:refresh');
    }

    public function markRejected(): void
    {
        if (!$this->isAdmin) abort(403);

        $receiver = Receiver::query()
            ->where('id', $this->receiverId)
            ->whereIn('status', ['Pending','Executed'])
            ->first();

        if (!$receiver) {
            $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
            return;
        }

        $this->internalChangeStatusReceiver($receiver, 'Rejected');
        $this->emitUp('actions:refresh');
    }

    // ----- Core receiver change -----

protected function internalChangeStatusReceiver(Receiver $receiver, string $to): void
{
    if (!in_array($to, ['Pending','Executed','Rejected'], true)) {
        $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Invalid status')]);
        return;
    }

    try {
        DB::transaction(function () use ($receiver, $to) {
            // Lock fresh copy
            $receiver = Receiver::query()->whereKey($receiver->id)->lockForUpdate()->first();
            if (!$receiver) {
                throw new \RuntimeException('Receiver not found / already processed.');
            }

            $from = (string) $receiver->status;
            if ($from === $to) {
                // no-op
                $this->dispatchBrowserEvent('alert', ['type'=>'info','message'=>__('No change')]);
                // early exit out of transaction block
                throw new \LogicException('__NO_CHANGE__');
            }

            // Flip status
            if (!$receiver->update(['status' => $to])) {
                throw new \RuntimeException('Status update failed');
            }

            // ===== Ledger effects =====
            // (A) Pending/Rejected -> Executed: credit receiver balance (Incoming)
            if ($from !== 'Executed' && $to === 'Executed') {

                // prevent duplicates (safety)
                $exists = ReceiverBalance::query()
                    ->where('receiver_id', $receiver->id)
                    ->where('status', 'Incoming')
                    ->where('note', 'Receiver executed')
                    ->exists();

                if (!$exists) {
                    ReceiverBalance::create([
                        'user_id'     => $receiver->user_id,
                        'receiver_id' => $receiver->id,
                        'amount'      => (int) $receiver->amount_iqd,
                        'status'      => 'Incoming',
                        'note'        => 'Receiver executed',
                    ]);
                }
            }

            // (B) Executed -> Pending/Rejected: debit receiver balance (Outgoing)
            if ($from === 'Executed' && $to !== 'Executed') {
                $fullName = trim(($receiver->first_name ?? '') . ' ' . ($receiver->last_name ?? ''));
                $note = ($to === 'Rejected')
                    ? 'Receiver Rejected (' . $fullName . ')'
                    : 'Receiver moved from Executed to ' . $to . ' (revert)';

                // prevent duplicates (safety)
                $exists = ReceiverBalance::query()
                    ->where('receiver_id', $receiver->id)
                    ->where('status', 'Outgoing')
                    ->where('note', $note)
                    ->exists();

                if (!$exists) {
                    ReceiverBalance::create([
                        'user_id'     => $receiver->user_id,
                        'receiver_id' => $receiver->id,
                        'amount'      => (int) $receiver->amount_iqd, // positive
                        'status'      => 'Outgoing',
                        'note'        => $note,
                        'admin_id'    => auth()->id(),
                    ]);
                }
            }

            // ===== UI toast =====
            $this->dispatchBrowserEvent('alert', [
                'type'    => 'success',
                'message' => __('Marked :status', ['status' => $to]),
            ]);

            // ===== Telegram =====
            try {
                Notification::route('toTelegram', null)->notify(new TeleNotifyReceiverAction(
                    $receiver->id,
                    $receiver->mtcn,
                    trim(($receiver->first_name ?? '').' '.($receiver->last_name ?? '')),
                    (float) $receiver->amount_iqd,
                    $from,
                    $to,
                    auth()->user()->name ?? 'system'
                ));
            } catch (\Throwable $e) {
                Log::warning('TeleNotifyReceiverAction failed', ['e'=>$e->getMessage()]);
            }

            // ===== WhatsApp only for Pending/Rejected -> Executed =====
            if (in_array($from, ['Pending','Rejected'], true) && $to === 'Executed') {
                $this->sendWhatsAppReceiverExecuted($receiver);
            }
        });
    } catch (\LogicException $e) {
        if ($e->getMessage() === '__NO_CHANGE__') return; // already handled toast
        Log::warning('Receiver no-change short-circuit', ['receiver_id' => $receiver->id]);
        return;
    } catch (\Throwable $e) {
        Log::error('Receiver status change failed', [
            'error' => $e->getMessage(),
            'receiver_id' => $receiver->id,
            'to' => $to,
        ]);
        $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Something went wrong!')]);
    }
}


    protected function sendWhatsAppReceiverExecuted(Receiver $receiver): void
    {
        try {
            $phoneId  = config('services.whatsapp.phone_id');
            $token    = config('services.whatsapp.token');
            // $toNumber = config('services.whatsapp.test_to');
            $template = config('services.whatsapp.template_receiver');
            $lang     = config('services.whatsapp.lang', 'en');
            
            if (!$phoneId || !$token || !$template) {
                Log::warning('WA Receiver: missing config', compact('phoneId','template','lang'));
                return;
            }
            $toNumberAgency   = optional(optional($receiver->user)->profile)->phone;
            $toNumberCustomer = $receiver->phone;

            $toNumberAgency   = $toNumberAgency   ? ltrim(trim($toNumberAgency), '+')   : null;
            $toNumberCustomer = $toNumberCustomer ? ltrim(trim($toNumberCustomer), '+') : null;

            $urlForAgency   = 'receipts-receiver-executed/'.$receiver->id.'/agent';
            $urlForCustomer = 'receipts-receiver-executed/'.$receiver->id.'/customer';

            $customerName = trim(($receiver->first_name ?? '').' '.($receiver->last_name ?? '')) ?: 'Customer';
            $mtcn         = (string) $receiver->mtcn;

            $anySuccess = false;
            $lastError  = null;
            $sendOne = function (?string $to, string $url) use (
                $token, $phoneId, $template, $lang, $customerName, $mtcn, $receiver, &$anySuccess, &$lastError
                ) {
                $link = $url;
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
                        'index' => '0', // 0 if it’s the first button in the template
                        'parameters' => [
                            ['type' => 'text', 'text' => $link], // e.g. "27"
                        ],
                        ],
                    ],
                ],
            ];

            $resp = Http::withToken($token)->acceptJson()->asJson()
                ->post("https://graph.facebook.com/v22.0/{$phoneId}/messages", $payload);

            if (!$resp->successful()) {
                Log::error('WhatsApp API error (receiver)', ['status'=>$resp->status(), 'body'=>$resp->body()]);
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'warning',
                    'message' => __('WhatsApp push failed (:code)', ['code' => $resp->status()]),
                ]);
            } else {
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('WhatsApp push sent')]);
            }
        };

        $sendOne($toNumberAgency,   $urlForAgency);
        $sendOne($toNumberCustomer, $urlForCustomer);

        if ($anySuccess) {
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('WhatsApp push sent')]);
        } elseif ($lastError) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => __('WhatsApp push failed (:code)', ['code' => $lastError['status']]),
            ]);
        }

        } catch (\Throwable $e) {
            Log::error('WhatsApp push exception (receiver)', ['error' => $e->getMessage()]);
            $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('WhatsApp push failed')]);
        }
    }

    public function render()
    {
        return view('components.reuse.receiver-action', [
            'receiver' => $this->receiver,
        ]);
    }
}
