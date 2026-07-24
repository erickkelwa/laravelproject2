<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MpesaPaymentReceived extends Notification
{
    use Queueable;

    public $amount;
    public $studentName;
    public $time;

    public function __construct($amount, $studentName, $time = null)
    {
        $this->amount = $amount;
        $this->studentName = $studentName;
        $this->time = $time ?: now()->format('h:i A, M d');
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'mpesa_received',
            'title' => 'M-Pesa Payment Received',
            'description' => "KES " . number_format($this->amount, 2) . " received from {$this->studentName} at {$this->time}.",
            'icon' => 'bi-cash-stack',
            'icon_bg' => 'bg-success-subtle text-success'
        ];
    }
}
