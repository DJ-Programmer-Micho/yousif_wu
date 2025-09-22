<?php

namespace App\Notifications\Telegram;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TeleNotifyReceiverAction extends Notification
{
    protected $o_id;
    protected $mtcn;
    protected $mtcnFormatted;
    protected $s_full_name;
    protected $s_phone;
    protected $from;
    protected $to;
    protected $total;
    protected $by;

    public function __construct($id, $mtcn, $s_full_name, $total, $from, $to, $by)
    {
        $this->o_id = $id;
        $this->mtcn = $mtcn;
        $this->s_full_name = $s_full_name;
        $this->from = $from;
        $this->to = $to;
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
        // $customer_url = env('APP_URL') . '/receipts/'.$this->o_id.'/customer';
        // $agent_url = env('APP_URL') . '/receipts/'.$this->o_id.'/agent';

        $content = "*" . 'RECEIVER ACTION' . "*\n"
        . "*" . '----SENDER----' . "*\n" 
        . "*" . 'MTCN: ' . $this->mtcnFormatted . "*\n"
        . "*" . 'Full Name: ' . $this->s_full_name . "*\n"
        . "*" . 'Phone: ' . $this->s_phone . "*\n"
        . "*" . '---PAYMENT----' . "*\n" 
        . "*" . 'Total: ' . number_format($this->total, 0) . " IQD *\n"
        . "*" . '----Action----' . "*\n" 
        . "*" .  $this->from . ' -> ' . $this->to . "*\n"
        . "*" . 'By: ' . $this->by . "*\n";


       return TelegramMessage::create()
        ->to(env('TELEGRAM_BOT_GROUP_PROCESS_ID') ?? '-4882881229')
        ->content($content);
    }

    public function toArray($notifiable)
    {

    }
}
