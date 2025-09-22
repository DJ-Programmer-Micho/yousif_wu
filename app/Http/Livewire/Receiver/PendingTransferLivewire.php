<?php
// app/Http/Livewire/Receiver/PendingTransferLivewire.php
namespace App\Http\Livewire\Receiver;

use Livewire\Component;
use App\Models\Receiver;
use Livewire\WithPagination;
use App\Models\ReceiverBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Telegram\TeleNotifyReceiverAction;

class PendingTransferLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $q = '';
    public int $perPage = 10;
    public bool $isAdmin = false;
    protected $listeners = ['actions:refresh' => '$refresh'];
    protected $queryString = [
        'q'       => ['except' => ''],
        'page'    => ['except' => 1],
        'perPage' => ['except' => 10],
    ];

    public function mount(): void
    {
        $this->isAdmin = ((int) auth()->user()->role) === 1;
    }

    public function updatingQ()       { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function formatMtcn(?string $v): string
    {
        $v = (string) $v;
        return preg_match('/^\d{10}$/', $v)
            ? substr($v,0,3).'-'.substr($v,3,3).'-'.substr($v,6,4)
            : $v;
    }

    protected function baseQuery()
    {
        $q = Receiver::query()
            ->with('user:id,name')
            ->where('status', 'Pending');

        if (!$this->isAdmin) {
            $q->where('user_id', auth()->id());
        }
        return $q;
    }

    protected function rows()
    {
        $escapeLike = fn($t) => '%'.str_replace(['%','_'], ['\%','\_'], trim($t)).'%';
        $q = $this->baseQuery();

        if ($this->q !== '') {
            $term   = $escapeLike($this->q);
            $digits = preg_replace('/\D+/', '', $this->q);

            $q->where(function ($w) use ($term, $digits) {
                $w->where('mtcn', 'like', $term);
                if ($digits !== '') {
                    $w->orWhere('mtcn', 'like', '%'.$digits.'%');
                }
                $w->orWhere('first_name', 'like', $term)
                  ->orWhere('last_name',  'like', $term)
                  ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", [$term])
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('address', 'like', $term);
            });
        }

        return $q->orderByDesc('created_at')->paginate($this->perPage);
    }

    /** Pending → Executed / Rejected */
    // public function markExecuted(int $receiverId): void
    // {
    //     $this->changeStatus($receiverId, 'Executed', 'Pending');
    // }

    // public function markRejected(int $receiverId): void
    // {
    //     $this->changeStatus($receiverId, 'Rejected', 'Pending');
    // }
    //     // 2) Credit receiver balance (Incoming IQD)

    // protected function changeStatus(int $receiverId, string $to, string $onlyIfCurrent): void
    // {
    //     if (!in_array($to, ['Executed','Rejected'], true)) {
    //         $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Invalid status')]);
    //         return;
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $q = Receiver::query()->where('id', $receiverId)->where('status', $onlyIfCurrent);
    //         if (!$this->isAdmin) $q->where('user_id', auth()->id());
    //         /** @var Receiver|null $receiver */
    //         $receiver = $q->lockForUpdate()->first();

    //         if (!$receiver) {
    //             DB::rollBack();
    //             $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
    //             return;
    //         }

    //         $from = $receiver->status;
    //         $receiver->update(['status' => $to]); // model hook credits when -> Executed

    //         if($to == "Executed") {
    //             ReceiverBalance::create([
    //                 'user_id'     => $receiver->user_id,
    //                 'receiver_id' => $receiver->id,           // link to receiver row (nullable FK)
    //                 'amount'      => (int) $receiver->amount_iqd, // IQD
    //                 'status'      => 'Incoming',
    //                 'note'        => 'Receiver created',
    //                 // 'admin_id'  => null,                    // left null intentionally
    //             ]);
    //         }

                    

    //         DB::commit();

    //         $this->dispatchBrowserEvent('alert', ['type'=>'success','message'=>__('Marked :status', ['status'=>$to])]);

    //         if ($to === 'Executed') {
    //         try {
    //             $phoneId  = config('services.whatsapp.phone_id');
    //             $token    = config('services.whatsapp.token');
    //             $toNumber = config('services.whatsapp.test_to');  // in E.164 without '+'
    //             $template = config('services.whatsapp.template_receiver');
    //             $lang     = config('services.whatsapp.lang', 'en'); // <-- use en, not en_US

    //             $customerName = trim(($receiver->first_name ?? '') . ' ' . ($receiver->last_name ?? '')) ?: 'Customer';
    //             $mtcn         = (string) $receiver->mtcn;

    //             $payload = [
    //                 'messaging_product' => 'whatsapp',
    //                 'to' => $toNumber,
    //                 'type' => 'template',
    //                 'template' => [
    //                     'name' => $template,
    //                     'language' => ['code' => $lang],   // <-- match the template locale
    //                     'components' => [[
    //                         'type' => 'body',
    //                         'parameters' => [
    //                             ['type' => 'text', 'parameter_name' => 'text', 'text' => $customerName],  // {{1}}
    //                             ['type' => 'text', 'parameter_name' => 'mtcn', 'text' => 'mtcn-' . $mtcn] // {{2}}
    //                         ],
    //                     ]],
    //                 ],
    //             ];

    //             $resp = Http::withToken($token)
    //                 ->acceptJson()
    //                 ->asJson()
    //                 ->post("https://graph.facebook.com/v22.0/{$phoneId}/messages", $payload);

    //             if (!$resp->successful()) {
    //                 Log::error('WhatsApp API error', ['status' => $resp->status(), 'body' => $resp->body()]);
    //                 $this->dispatchBrowserEvent('alert', [
    //                     'type' => 'warning',
    //                     'message' => __('WhatsApp push failed (:code)', ['code' => $resp->status()]),
    //                 ]);
    //             } else {
    //                 $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('WhatsApp push sent')]);
    //             }
    //         } catch (\Throwable $e) {
    //             Log::error('WhatsApp push exception', ['error' => $e->getMessage()]);
    //             $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('WhatsApp push failed')]);
    //         }
    //     }
        
    //         // Telegram notify
    //         try {
    //             Notification::route('toTelegram', null)->notify(new TeleNotifyReceiverAction(
    //                 $receiver->id,
    //                 $receiver->mtcn,
    //                 trim(($receiver->first_name ?? '').' '.($receiver->last_name ?? '')),
    //                 (float)$receiver->amount_iqd,
    //                 $from,
    //                 $to,
    //                 auth()->user()->name ?? 'system'
    //             ));
    //         } catch (\Throwable $e) {}

    //         if ($this->rows()->isEmpty() && $this->page > 1) {
    //             $this->previousPage();
    //         }
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Something went wrong!')]);
    //     }
    // }

    public function clearFilters()
    {
        $this->q       = '';
        $this->perPage = 10;

        $this->resetPage();
        $this->dispatchBrowserEvent('filter-cleared');
        session()->flash('message', __('Filters cleared successfully'));
    }

    public function render()
    {
        $totalReceivers = $this->baseQuery()->count();

        return view('components.tables.receiver-pending-table', [
            'rows'         => $this->rows(),
            'allreceivers' => $totalReceivers,
        ]);
    }
}
