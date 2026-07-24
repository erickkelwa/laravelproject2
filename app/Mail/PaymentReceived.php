<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $fee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Models\Fee $fee)
    {
        $this->fee = $fee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('fees.receipt', ['fee' => $this->fee]);
        
        return $this->subject('Payment Receipt - ' . config('app.name'))
                    ->markdown('emails.payments.received')
                    ->attachData($pdf->output(), 'Receipt_' . ($this->fee->receipt_no ?? $this->fee->id) . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
