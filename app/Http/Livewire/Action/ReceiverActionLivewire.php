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

        DB::beginTransaction();
        try {
            $receiver = Receiver::query()->whereKey($receiver->id)->lockForUpdate()->first();
            if (!$receiver) {
                DB::rollBack();
                $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
                return;
            }

            $from = (string) $receiver->status;

            $receiver->update(['status' => $to]);

            // Ledger effects
            if ($from !== 'Executed' && $to === 'Executed') {
                ReceiverBalance::create([
                    'user_id'     => $receiver->user_id,
                    'receiver_id' => $receiver->id,
                    'amount'      => (int) $receiver->amount_iqd,
                    'status'      => 'Incoming',
                    'note'        => 'Receiver executed',
                ]);
                } elseif ($from === 'Executed' && $to !== 'Executed') {
                    ReceiverBalance::create([
                        'user_id'     => $receiver->user_id,
                        'receiver_id' => $receiver->id,
                        'amount'      => (int) $receiver->amount_iqd, // positive!
                        'status'      => 'Outgoing',                  // only Incoming/Outgoing
                        'note'        => "Receiver moved from Executed to {$to} (revert)",
                        'admin_id'    => auth()->id(),
                    ]);
                }
            DB::commit();

            $this->dispatchBrowserEvent('alert', [
                'type'=>'success',
                'message'=>__('Marked :status', ['status'=>$to]),
            ]);

            // Telegram for all
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

            // WhatsApp only for Pending → Executed
            if (in_array($from, ['Pending','Rejected'], true) && $to === 'Executed') {
                $this->sendWhatsAppReceiverExecuted($receiver);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Receiver status change failed', ['error'=>$e->getMessage()]);
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
                            ['type' => 'text', 'text' => (string) $url], // e.g. "27"
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
