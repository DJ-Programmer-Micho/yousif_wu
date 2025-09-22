<?php

namespace App\Http\Livewire\Sender;

use App\Models\Sender;
use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Telegram\TeleNotifySenderAction;

class RejectedTransferLivewire extends Component
{
    use WithPagination;

    public string $q = '';
    public string $country = '';
    public int    $perPage = 10;

    public array $availableCountries = [];
    public array $countryMap = [];
    public bool  $isAdmin = false;
    protected $listeners = ['actions:refresh' => '$refresh'];
    protected $queryString = [
        'q'       => ['except' => ''],
        'country' => ['except' => ''],
        'page'    => ['except' => 1],
        'perPage' => ['except' => 10],
    ];

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
            ->where('status', 'Rejected');

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
    // public function markPending(int $senderId): void
    // {
    //     $this->changeStatus($senderId, 'Pending');
    // }

    // public function markExecuted(int $senderId): void
    // {
    //     $this->changeStatus($senderId, 'Executed');
    // }

    // /** Core status change logic with authorization + race-safety */
    // protected function changeStatus(int $senderId, string $to): void
    // {
    //     if (!in_array($to, ['Pending','Executed'], true)) {
    //         $this->dispatchBrowserEvent('alert', ['type'=>'error','message'=>__('Invalid status')]);
    //         return;
    //     }

    //     $q = Sender::query()
    //         ->where('id', $senderId)
    //         ->where('status', 'Rejected'); // only flip Pending rows
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
    //         try {
    //             Notification::route('toTelegram', null)->notify(new TeleNotifySenderAction(
    //             $sender->id,
    //             $sender->mtcn,
    //             trim(($sender->first_name ?? '').' '.($sender->last_name ?? '')),
    //             $sender->total,
    //             "Rejected",
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

        return view('components.tables.sender-rejected-table', [
            'rows'       => $this->rows(),
            'allsenders' => $totalSenders,
        ]);
    }
}