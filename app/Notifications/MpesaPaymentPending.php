<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MpesaPaymentPending extends Notification
{
    use Queueable;

    public $amount;
    public $phone;

    public function __construct($amount, $phone)
    {
        $this->amount = $amount;
        $this->phone = $phone;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'mpesa_pending',
            'title' => 'M-Pesa Payment Pending',
            'description' => "STK Push sent to {$this->phone} for KES " . number_format($this->amount, 2) . ".",
            'icon' => 'bi-hourglass-split',
            'icon_bg' => 'bg-warning-subtle text-warning'
        ];
    }
}
