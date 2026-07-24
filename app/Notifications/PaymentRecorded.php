<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentRecorded extends Notification
{
    use Queueable;

    public $amount;
    public $studentName;
    public $method;

    public function __construct($amount, $studentName, $method)
    {
        $this->amount = $amount;
        $this->studentName = $studentName;
        $this->method = $method;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_recorded',
            'title' => 'Payment Recorded',
            'description' => "KES " . number_format($this->amount, 2) . " ({$this->method}) recorded for {$this->studentName}.",
            'icon' => 'bi-receipt',
            'icon_bg' => 'bg-info-subtle text-info'
        ];
    }
}
