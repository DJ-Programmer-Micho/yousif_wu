<?php

namespace App\Http\Livewire\Action;

use Livewire\Component;
use App\Models\Sender;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Telegram\TeleNotifySenderAction;

class SenderActionLivewire extends Component
{
    public int $senderId;

    // modal fields (unique per-row component instance)
    public ?int $execId = null;

    public string $oldMtcn = '';
    public string $newMtcn = '';

    public string $oldFirstName = '';
    public string $newFirstName = '';

    public string $oldLastName = '';
    public string $newLastName = '';

    public ?float $execTotal = null;

    public bool $isAdmin = false;

    protected function rules(): array
    {
        return [
            'newMtcn'       => ['required','digits:10'],
        // tighten names if you want caps only: 'regex:/^[A-Z\s\-\'\.]+$/u'
            'newFirstName'  => ['required','string','max:100'],
            'newLastName'   => ['required','string','max:100'],
        ];
    }

    public function mount(int $senderId): void
    {
        $this->senderId = $senderId;
        $this->isAdmin = ((int) auth()->user()->role) === 1;
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
        ->whereIn('status', ['Pending','Rejected']) // ⬅ allow Rejected too
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

    $this->execTotal = (float) $s->total;

    $this->dispatchBrowserEvent('modal:open', ['id' => $this->modalId()]);
}

public function markExecutedConfirmed(): void
{
    if (!$this->isAdmin) abort(403);
    $this->validate();

    DB::beginTransaction();
    try {
        $sender = Sender::query()
            ->where('id', $this->execId)
            ->whereIn('status', ['Pending','Rejected']) // ⬅ allow either
            ->lockForUpdate()
            ->first();

        if (!$sender) {
            DB::rollBack();
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => __('Not allowed or already processed'),
            ]);
            $this->closeModal();
            return;
        }

        $sender->update([
            'mtcn'       => $this->newMtcn,
            'first_name' => $this->newFirstName,
            'last_name'  => $this->newLastName,
        ]);

        // Flip to Executed (does notifications too)
        $this->internalChangeStatus($sender, 'Executed');

        DB::commit();

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Updated & marked as Executed'),
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Sender exec confirm failed', ['error' => $e->getMessage()]);
        $this->dispatchBrowserEvent('alert', [
            'type' => 'warning',
            'message' => __('Operation failed'),
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
        Log::warning('TeleNotifySenderAction failed', ['e'=>$e->getMessage()]);
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
            $toNumber = config('services.whatsapp.test_to');
            $template = config('services.whatsapp.template_sender');
            $lang     = config('services.whatsapp.lang', 'en');

            if (!$phoneId || !$token || !$toNumber || !$template) {
                Log::warning('WA Sender: missing config', compact('phoneId','toNumber','template','lang'));
                return;
            }

            $customerName = trim(($sender->first_name ?? '').' '.($sender->last_name ?? '')) ?: 'Customer';
            $mtcn         = (string) $sender->mtcn;

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $toNumber,
                'type' => 'template',
                'template' => [
                    'name' => $template,
                    'language' => ['code' => $lang],
                    'components' => [[
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'parameter_name' => 'text', 'text' => $customerName],
                            ['type' => 'text', 'parameter_name' => 'mtcn', 'text' => 'mtcn-'.$mtcn],
                        ],
                    ]],
                ],
            ];

            $resp = Http::withToken($token)->acceptJson()->asJson()
                ->post("https://graph.facebook.com/v22.0/{$phoneId}/messages", $payload);

            if (!$resp->successful()) {
                Log::error('WhatsApp API error (sender)', ['status'=>$resp->status(), 'body'=>$resp->body()]);
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'warning',
                    'message' => __('WhatsApp push failed (:code)', ['code' => $resp->status()]),
                ]);
            } else {
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('WhatsApp push sent')]);
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp push exception (sender)', ['error' => $e->getMessage()]);
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
