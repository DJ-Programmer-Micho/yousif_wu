<?php

namespace App\Notifications\Telegram;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TeleNotifyReceiverNew extends Notification
{
    protected $o_id;
    protected $r_full_name;
    protected $r_phone;
    protected $mtcn;
    protected $mtcnFormatted;
    protected $address;
    protected $total;
    protected $by;

    public function __construct($id, $mtcn, $r_full_name, $r_phone, $address, $total, $by)
    {
        $this->o_id = $id;
        $this->mtcn = $mtcn;
        $this->r_full_name = $r_full_name;
        $this->r_phone = $r_phone ?? 'N/A';
        $this->address = $address;
        $this->total = $total;
        $this->by = $by;

        $this->mtcnFormatted = (preg_match('/^\d{10}$/', (string)$this->mtcn))
        ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', (string)$this->mtcn)
        : (string)$this->mtcn;
    }

    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        $customer_url = env('APP_URL') . '/receipts-receiver/'.$this->o_id.'/customer';
        $agent_url = env('APP_URL') . '/receipts-receiver/'.$this->o_id.'/agent';

        $content = "*" . 'NEW RECEIVER' . "*\n"
        . "*" . '---RECEIVER---' . "*\n" 
        . "*" . 'MTCN: ' . $this->mtcnFormatted . "*\n"
        . "*" . 'Full Name: ' . $this->r_full_name . "*\n"
        . "*" . 'Phone: ' . $this->r_phone . "*\n"
        . "*" . 'Address: ' . $this->address . "*\n"
        . "*" . '---PAYMENT----' . "*\n" 
        . "*" . 'Total: ' . number_format($this->total, 0) . " IQD *\n"
        . "*" . '----Action----' . "*\n" 
        . "*" . 'By: ' . $this->by . "*\n";


       return TelegramMessage::create()
        ->to(env('TELEGRAM_BOT_GROUP_RECEIVER_ID') ?? '-4892945051')
        ->content($content)
        ->button('Customer Receipt', $customer_url)
        ->button('Agent Receipt', $agent_url);
    }

    public function toArray($notifiable)
    {

    }
}
