<?php

namespace App\Notifications\Telegram;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TeleNotifySenderNew extends Notification
{
    protected $o_id;
    protected $mtcn;
    protected $mtcnFormatted;
    protected $s_full_name;
    protected $s_phone;
    protected $country;
    protected $amount;
    protected $total;
    protected $fee;
    protected $r_full_name;
    protected $r_phone;
    protected $by;

    public function __construct($id, $mtcn, $s_full_name, $s_phone, $country, $amount, $fee, $total, $r_full_name, $r_phone, $by)
    {
        $this->o_id = $id;
        $this->mtcn = $mtcn;
        $this->s_full_name = $s_full_name;
        $this->s_phone = $s_phone;
        $this->country = $country;
        $this->amount = $amount;
        $this->total = $total;
        $this->fee = $fee;
        $this->r_full_name = $r_full_name;
        $this->r_phone = $r_phone ?? 'N/A';
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
        $customer_url = env('APP_URL') . '/receipts/'.$this->o_id.'/customer';
        $agent_url = env('APP_URL') . '/receipts/'.$this->o_id.'/agent';

        $content = "*" . 'NEW SENDER' . "*\n"
        . "*" . '----SENDER----' . "*\n" 
        . "*" . 'MTCN: ' . $this->mtcnFormatted . "*\n"
        . "*" . 'Full Name: ' . $this->s_full_name . "*\n"
        . "*" . 'Phone: ' . $this->s_phone . "*\n"
        . "*" . 'Country: ' . $this->country . "*\n"
        . "*" . '---RECEIVER---' . "*\n" 
        . "*" . 'Full Name: ' . $this->r_full_name . "*\n"
        . "*" . 'Phone: ' . $this->r_phone . "*\n"
        . "*" . '---PAYMENT----' . "*\n" 
        . "*" . 'Amout: $' . number_format($this->amount, 2) . "*\n"
        . "*" . 'Fee: $' . number_format($this->fee, 2) . "*\n"
        . "*" . 'Total: $' . number_format($this->total, 2) . "*\n"
        . "*" . '----Action----' . "*\n" 
        . "*" . 'By: ' . $this->by . "*\n";


       return TelegramMessage::create()
        ->to(env('TELEGRAM_BOT_GROUP_SERNDER_ID') ?? '-4933137583')
        ->content($content)
        ->button('Customer Receipt', $customer_url)
        ->button('Agent Receipt', $agent_url);
    }

    public function toArray($notifiable)
    {

    }
}
