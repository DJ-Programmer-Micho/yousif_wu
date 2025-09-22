<?php

namespace App\Http\Livewire\Sender;

use App\Models\Sender;
use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Telegram\TeleNotifySenderAction;

class PendingTransferLivewire extends Component
{
    use WithPagination;

    public string $q = '';
    public string $country = '';
    public int    $perPage = 10;

    public array $availableCountries = [];
    public array $countryMap = [];
    public bool  $isAdmin = false;

    public ?int $execId = null;

    public string $oldMtcn = '';
    public string $newMtcn = '';

    public string $oldFirstName = '';
    public string $newFirstName = '';

    public string $oldLastName = '';
    public string $newLastName = '';
    protected $listeners = ['actions:refresh' => '$refresh'];
    protected $queryString = [
        'q'       => ['except' => ''],
        'country' => ['except' => ''],
        'page'    => ['except' => 1],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'newMtcn' => [
                'required',
                'regex:/^\d{10}$/',
                Rule::unique('senders', 'mtcn')->ignore($this->execId)
            ],
            'newFirstName' => ['required','string','min:2','max:60'],
            'newLastName'  => ['required','string','min:2','max:60'],
        ];
    }

    public function updated($prop): void
    {
        if ($prop === 'newFirstName' && $this->newFirstName !== '') {
            $this->newFirstName = mb_strtoupper($this->newFirstName, 'UTF-8');
        }
        if ($prop === 'newLastName' && $this->newLastName !== '') {
            $this->newLastName = mb_strtoupper($this->newLastName, 'UTF-8');
        }
        if ($prop === 'newMtcn' && $this->newMtcn !== '') {
            // keep only digits, limit to 10 (preserves leading zeros if user typed them)
            $this->newMtcn = substr(preg_replace('/\D+/', '', $this->newMtcn), 0, 10);
        }
    }

    public function mount(): void
    {
        $this->isAdmin = ((int) auth()->user()->role) === 1;

        $displayCol = match (app()->getLocale()) {
            'ar' => 'ar_name',
            'ku' => 'ku_name',
            default => 'en_name',
        };

        $countries = Country::orderBy('en_name')
            ->get(['id','en_name','ar_name','ku_name','iso_code','flag_path']);

        $this->availableCountries = $countries->map(function ($c) use ($displayCol) {
            return [
                'id'        => $c->id,
                'en_name'   => $c->en_name,                 // value stored in senders.country
                'label'     => $c->$displayCol,             // translated label
                'iso_code'  => strtoupper($c->iso_code),
                'ar_name'   => $c->ar_name,
                'ku_name'   => $c->ku_name,
                'flag_path' => $c->flag_path,
            ];
        })->toArray();

        $this->countryMap = $countries
            ->mapWithKeys(fn($c) => [$c->en_name => $c->$displayCol])
            ->toArray();
    }

    public function updatingQ()       { $this->resetPage(); }
    public function updatingCountry() { $this->resetPage(); }
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
        $q = Sender::query()
            ->with('user')
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

        if ($this->country !== '') {
            $q->where('country', $this->country); // en_name
        }

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
                  ->orWhere('r_first_name', 'like', $term)
                  ->orWhere('r_last_name',  'like', $term)
                  ->orWhereRaw("CONCAT(COALESCE(r_first_name,''),' ',COALESCE(r_last_name,'')) LIKE ?", [$term])
                  ->orWhere('r_phone', 'like', $term);
            });
        }

        return $q->orderByDesc('created_at')->paginate($this->perPage);
    }

    // /** Public actions */
    // public function markExecuted(int $senderId): void
    // {
    //     $this->changeStatus($senderId, 'Executed');
    // }

    // public function markRejected(int $senderId): void
    // {
    //     $this->changeStatus($senderId, 'Rejected');
    // }

    // /** Core status change logic with authorization + race-safety */
    // protected function changeStatus(int $senderId, string $to): void
    // {
    //     if (!in_array($to, ['Executed','Rejected'], true)) {
    //         $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Invalid status')]);
    //         return;
    //     }
    //     if($to == "Executed") {

    //     }

    //     $q = Sender::query()
    //         ->where('id', $senderId)
    //         ->where('status', 'Pending'); // only flip Pending rows
    //     $sender = $q->lockForUpdate()->first();

    //     if (!$this->isAdmin) {
    //         $q->where('user_id', auth()->id());
    //     }

    //     $affected = $q->update(['status' => $to]);

    //     if ($affected === 1) {
    //         $this->dispatchBrowserEvent('alert', ['type'=>'success','message'=>__('Marked :status', ['status'=>$to])]);

    //         // If current page becomes empty after removal, move back a page
    //         if ($this->rows()->isEmpty() && $this->page > 1) {
    //             $this->previousPage();
    //         }

            
                
    //     try {
    //         Notification::route('toTelegram', null)->notify(new TeleNotifySenderAction(
    //             $sender->id,
    //             $sender->mtcn,
    //             trim(($sender->first_name ?? '').' '.($sender->last_name ?? '')),
    //             $sender->total,
    //             "Pending",
    //             $to,
    //             auth()->user()->name ?? 'system'
    //         ));

    //             $this->dispatchBrowserEvent('alert', [
    //                 'type' => 'success',
    //                 'message' => __('Push Activated'),
    //             ]);
    //             } catch (\Exception $e) {
    //             dd($e); // log instead of dd()
    //             $this->dispatchBrowserEvent('alert', [
    //                 'type' => 'warning',
    //                 'message' => __('Did not saved in cloud!'),
    //             ]);
    //             return;
    //         }
            
    //     if ($to === 'Executed') {
    //         try {
    //             $phoneId  = config('services.whatsapp.phone_id');
    //             $token    = config('services.whatsapp.token');
    //             $toNumber = config('services.whatsapp.test_to');  // in E.164 without '+'
    //             $template = config('services.whatsapp.template_sender');
    //             $lang     = config('services.whatsapp.lang', 'en'); // <-- use en, not en_US

    //             $customerName = trim(($sender->first_name ?? '') . ' ' . ($sender->last_name ?? '')) ?: 'Customer';
    //             $mtcn         = (string) $sender->mtcn;

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


    //     } else {
    //         $this->dispatchBrowserEvent('alert', ['type'=>'warning','message'=>__('Not allowed or already processed')]);
    //     }
    // }

    public function clearFilters()
    {
        $this->q       = '';
        $this->country = '';
        $this->perPage = 10;

        $this->resetPage();
        $this->dispatchBrowserEvent('filter-cleared');
        session()->flash('message', __('Filters cleared successfully'));
    }

    public function render()
    {
        $totalSenders = $this->baseQuery()->count();

        return view('components.tables.sender-pending-table', [
            'rows'       => $this->rows(),
            'allsenders' => $totalSenders,
        ]);
    }
    public ?float $execTotal = null;
    public ?string $execSenderName = null;
    public ?string $execReceiverName = null;
    public function askExecute(int $id): void
    {
        // only admins can approve (keep your existing role guard if needed)
        if ((int)auth()->user()->role !== 1) abort(403);

        $s = Sender::query()
            ->where('id', $id)
            ->where('status', 'Pending')
            ->when(((int)auth()->user()->role !== 1), fn($q) => $q->where('user_id', auth()->id()))
            ->firstOrFail();

        $this->execId = $s->id;

        // OLDs
        $this->oldMtcn      = (string) $s->mtcn;
        $this->oldFirstName = (string) $s->first_name;
        $this->oldLastName  = (string) $s->last_name;

        // NEW defaults = old
        $this->newMtcn      = (string) $s->mtcn;
        $this->newFirstName = mb_strtoupper((string)$s->first_name, 'UTF-8');
        $this->newLastName  = mb_strtoupper((string)$s->last_name, 'UTF-8');

        // (Optional) summary cards in the modal
        $this->execTotal       = (float) $s->total;
        $this->execSenderName  = trim(($s->first_name ?? '').' '.($s->last_name ?? '')) ?: null;
        $this->execReceiverName= trim(($s->r_first_name ?? '').' '.($s->r_last_name ?? '')) ?: null;

        // Open modal (BS4 helper)
        $this->dispatchBrowserEvent('modal:open', ['id' => 'executionProcess']);
    }

    /** Close modal */
    public function closeModal(): void
    {
        $this->execId = null;
        $this->resetValidation();
        $this->dispatchBrowserEvent('modal:close', ['id' => 'executionProcess']);
    }

    /** Confirm: update fields + mark Executed (atomic) */
   public function markExecutedConfirmed(): void
    {
        // only admins
        if ((int)auth()->user()->role !== 1) abort(403);

        $this->validate();

        // 1) Update ONLY the editable fields (keep status = Pending so changeStatus can do its job)
        $sender = Sender::query()
            ->where('id', $this->execId)
            ->where('status', 'Pending')
            ->when(!$this->isAdmin, fn($q) => $q->where('user_id', auth()->id()))
            ->lockForUpdate()
            ->first();

        if (!$sender) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => __('Not allowed or already processed'),
            ]);
            return;
        }

        $sender->update([
            'mtcn'       => $this->newMtcn,
            'first_name' => $this->newFirstName,
            'last_name'  => $this->newLastName,
        ]);

        // (optional tiny toast so user sees we saved edits)
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Updated MTCN and name. Executing…'),
        ]);

        // 2) Now trigger the canonical status change (does notifications, WhatsApp, pagination fix, etc.)
        $this->changeStatus($sender->id, 'Executed');

        // 3) Close the modal either way
        $this->closeModal();
    }


}
