@component('mail::message')
# Payment Received Successfully

Hello {{ $fee->student->name ?? 'Student' }},

We have successfully received your payment of **Ksh {{ number_format($fee->amount, 2) }}**.

**Payment Details:**
- **Receipt No:** {{ $fee->receipt_no ?? $fee->id }}
- **Method:** {{ $fee->payment_method }}
- **Date:** {{ \Carbon\Carbon::parse($fee->payment_date)->format('F d, Y') }}

A PDF copy of your official receipt is attached to this email.

@component('mail::button', ['url' => route('login')])
View Dashboard
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
