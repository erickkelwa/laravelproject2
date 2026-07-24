<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewStudentEnrolled extends Notification
{
    use Queueable;

    public $studentName;

    public function __construct($studentName)
    {
        $this->studentName = $studentName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_student',
            'title' => 'New Student Enrolled',
            'description' => "{$this->studentName} has been enrolled.",
            'icon' => 'bi-person-plus',
            'icon_bg' => 'bg-primary-subtle text-primary'
        ];
    }
}
