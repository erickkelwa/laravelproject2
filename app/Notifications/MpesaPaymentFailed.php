<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class MpesaPaymentFailed extends Notification
{
    use Queueable;

    public $amount;
    public $studentName;
    public $reason;
    public $time;

    public function __construct($amount, $studentName, $reason, $time = null)
    {
        $this->amount = $amount;
        $this->studentName = $studentName;
        $this->reason = $reason;
        $this->time = $time ?: now()->format('h:i A, M d');
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'mpesa_failed',
            'title' => 'M-Pesa Payment Failed',
            'description' => "Failed KES " . number_format($this->amount, 2) . " for {$this->studentName} at {$this->time}. Reason: {$this->reason}",
            'icon' => 'bi-x-circle',
            'icon_bg' => 'bg-danger-subtle text-danger'
        ];
    }
}
