<?php

namespace App\Notifications\Mail;

use App\Models\Sender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminSenderCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public Sender $sender;
    public string $agentName;

    public function __construct(Sender $sender, string $agentName)
    {
        $this->sender = $sender->withoutRelations();
        $this->agentName = $agentName;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Transfer Submitted — MTCN '.$this->sender->mtcn)
            // Render your Blade email view and pass the data it needs:
            ->view('components.mail.sender', [
                'sender'     => $this->sender,
                'agentName'  => $this->agentName,
                'submittedAt'=> now()->timezone(config('app.timezone')),
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'sender_id' => $this->sender->id,
            'mtcn'      => $this->sender->mtcn,
        ];
    }
}
