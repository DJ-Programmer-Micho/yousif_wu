<?php

namespace App\Notifications\Mail;

use App\Models\Receiver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminReceiverCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public Receiver $receiver;
    public string $agentName;

    public function __construct(Receiver $receiver, string $agentName)
    {
        // keep payload tiny if queued
        $this->receiver  = $receiver->withoutRelations();
        $this->agentName = $agentName;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Receiver Submitted — MTCN '.$this->receiver->mtcn)
            ->view('components.mail.receiver', [
                'receiver'    => $this->receiver,
                'agentName'   => $this->agentName,
                'submittedAt' => now()->timezone(config('app.timezone')),
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'receiver_id' => $this->receiver->id,
            'mtcn'        => $this->receiver->mtcn,
        ];
    }
}
