<?php

namespace App\Http\Livewire\Receiver;

use Livewire\Component;
use App\Models\Receiver;
use App\Models\ReceiverBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Mail\AdminReceiverCreated;
use App\Notifications\Telegram\TeleNotifyReceiverNew;

class ReceiverCreateLivewire extends Component
{
    // Receiver fields
    public string  $first_name = '';
    public string  $last_name  = '';
    public string  $phone      = '';
    public ?string $address    = null;
    public $amount_iqd         = null;  // integer-like
    public $identification     = null;  // keep null for now

    // MTCN segmented inputs for UI: xxx-xxx-xxxx
    public string $mtcn1 = '';
    public string $mtcn2 = '';
    public string $mtcn3 = '';

    public array $touched = [];

    protected function rules(): array
    {
        return [
            'first_name' => ['required','string','min:2','max:60'],
            'last_name'  => ['required','string','min:2','max:60'],
            'phone'      => ['required','string','max:32','regex:/^\+?[0-9]{8,32}$/'],
            'address'    => ['nullable','string','max:255'],
            'amount_iqd' => ['required','integer','min:1','max:999999999999'], // DECIMAL(12,0)

            // exact MTCN shape: 3-3-4 digits
            'mtcn1'      => ['required','digits:3'],
            'mtcn2'      => ['required','digits:3'],
            'mtcn3'      => ['required','digits:4'],
        ];
    }

    public function updated($property): void
    {
        $this->touched[$property] = true;

        if (in_array($property, ['first_name','last_name'], true)) {
            $this->$property = mb_strtoupper((string)$this->$property, 'UTF-8');
        }

        // Keep MTCN fields numeric-only
        if (in_array($property, ['mtcn1','mtcn2','mtcn3'], true)) {
            $this->$property = preg_replace('/\D+/', '', (string)$this->$property ?? '');
        }

        $this->validateOnly($property);
    }

    protected function mtcnCombined(): string
    {
        return $this->mtcn1.$this->mtcn2.$this->mtcn3; // 10 digits
    }

    public function submit()
{
    $this->validate();
    $mtcn = $this->mtcnCombined();

    try {
        DB::beginTransaction();

        // 1) Create receiver row
        $receiver = Receiver::create([
            'user_id'        => auth()->id(),
            'mtcn'           => $mtcn,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'phone'          => $this->phone,
            'address'        => $this->address ?: null,
            'amount_iqd'     => (int) $this->amount_iqd,
            'identification' => null,
            'status'         => 'Pending',
        ]);

        DB::commit();

    } catch (\Throwable $e) {
        DB::rollBack();
        $this->dispatchBrowserEvent('alert', [
            'type' => 'error',
            'message' => __('Something went wrong!'),
        ]);
        return;
    }

    // Local success
    $this->dispatchBrowserEvent('alert', [
        'type' => 'success',
        'message' => __('Receiver has been added successfully'),
    ]);

    // 3) Notifications (non-transactional; best-effort)
    try {
        Notification::route('toTelegram', null)
            ->notify(new TeleNotifyReceiverNew(
                $receiver->id,
                $mtcn,
                $this->first_name .' '.$this->last_name,
                $this->phone ?: null,
                $this->address,
                $this->amount_iqd,
                auth()->user()->name
            ));

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Submitted in System'),
        ]);
    } catch (\Throwable $e) {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'warning',
            'message' => __('Did not saved in cloud!'),
        ]);
    }

    try {
        $adminEmail = config('mail.admin_address') ?? env('ADMIN_EMAIL');
        if ($adminEmail) {
            Notification::route('mail', $adminEmail)
                ->notify(new AdminReceiverCreated($receiver, auth()->user()->name));
        }

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Pushed in System'),
        ]);
    } catch (\Throwable $e) {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'warning',
            'message' => __('Did not pushed in system'),
        ]);
    }

    // 4) Open receipts
    $this->dispatchBrowserEvent('open-receiver-receipts', [
        'urls' => [
            route('receipts.receiver.dompdf.show', ['receiver' => $receiver->id, 'type' => 'both'])
        ],
    ]);

    // 5) Reset form
    $this->reset([
        'first_name','last_name','phone','address','amount_iqd','identification',
        'mtcn1','mtcn2','mtcn3',
    ]);
    $this->touched = [];
}

    // public function submit()
    // {
    //     $this->validate();
    //     $mtcn = $this->mtcnCombined();
    //     try {
    //         $receiver = Receiver::create([
    //             'user_id'        => auth()->id(),
    //             'mtcn'           => $mtcn,
    //             'first_name'     => $this->first_name,
    //             'last_name'      => $this->last_name,
    //             'phone'          => $this->phone,
    //             'address'        => $this->address ?: null,
    //             'amount_iqd'     => (int) $this->amount_iqd,
    //             'identification' => null,
    //         ]);

    //         $this->dispatchBrowserEvent('alert', [
    //             'type' => 'success',
    //             'message' => __('Receiver has been added successfully'),
    //         ]);

    //         try {
    //             Notification::route('toTelegram', null)
    //             ->notify(new TeleNotifyReceiverNew(
    //                 $receiver->id,
    //                 $mtcn, 
    //                 $this->first_name .' '.$this->last_name, 
    //                 $this->phone ?: null,
    //                 $this->address, 
    //                 $this->amount_iqd,
    //                 auth()->user()->name
    //             ));

    //             $this->dispatchBrowserEvent('alert', [
    //                 'type' => 'success',
    //                 'message' => __('Submitted in System'),
    //             ]);
    //             } catch (\Exception $e) {
    //             dd($e); // log instead of dd()
    //             $this->dispatchBrowserEvent('alert', [
    //                 'type' => 'warning',
    //                 'message' => __('Did not saved in cloud!'),
    //             ]);
    //             return;
    //         }

    //         try {
    //             $adminEmail = config('mail.admin_address') ?? env('ADMIN_EMAIL');
    //             if ($adminEmail) {
    //                 Notification::route('mail', $adminEmail)
    //                     ->notify(new AdminReceiverCreated($receiver, auth()->user()->name));
    //             }
                
    //             $this->dispatchBrowserEvent('alert', [
    //                 'type' => 'success',
    //                 'message' => __('Pushed in System'),
    //             ]);
    //             } catch (\Exception $e) {
    //             dd($e); // log instead of dd()
    //             $this->dispatchBrowserEvent('alert', [
    //                 'type' => 'warning',
    //                 'message' => __('Did not pushed in system'),
    //             ]);
    //             return;
    //         }
    //         // Open customer + agent PDFs (dompdf) in new tabs
    //         $this->dispatchBrowserEvent('open-receiver-receipts', [
    //             'urls' => [
    //                 route('receipts.receiver.dompdf.show', ['receiver' => $receiver->id, 'type' => 'both'])
    //             ],
    //         ]);

    //         $this->reset([
    //             'first_name','last_name','phone','address','amount_iqd','identification',
    //             'mtcn1','mtcn2','mtcn3',
    //         ]);
    //         $this->touched = [];

    //     } catch (\Throwable $e) {
    //         dd('asd', $e);
    //         $this->dispatchBrowserEvent('alert', [
    //             'type' => 'error',
    //             'message' => __('Something went wrong!'),
    //         ]);
    //     }
    // }

    public function render()
    {
        return view('components.forms.receiver-create');
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
