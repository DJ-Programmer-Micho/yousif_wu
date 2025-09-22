<?php
// app/Http/Livewire/Receiver/RejectedTransferLivewire.php
namespace App\Http\Livewire\Receiver;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Receiver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Telegram\TeleNotifyReceiverAction;

class RejectedTransferLivewire extends Component
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

    // /** Rejected → Pending / Executed */
    // public function markPending(int $receiverId): void
    // {
    //     $this->changeStatus($receiverId, 'Pending', 'Rejected');
    // }

    // public function markExecuted(int $receiverId): void
    // {
    //     $this->changeStatus($receiverId, 'Executed', 'Rejected');
    // }

    // protected function changeStatus(int $receiverId, string $to, string $onlyIfCurrent): void
    // {
    //     if (!in_array($to, ['Pending','Executed'], true)) {
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
    //         $receiver->update(['status' => $to]); // model hook credits if -> Executed

    //         DB::commit();

    //         $this->dispatchBrowserEvent('alert', ['type'=>'success','message'=>__('Marked :status', ['status'=>$to])]);

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

        return view('components.tables.receiver-rejected-table', [
            'rows'         => $this->rows(),
            'allreceivers' => $totalReceivers,
        ]);
    }
}
