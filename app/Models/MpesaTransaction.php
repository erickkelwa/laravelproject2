<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'checkout_request_id',
        'merchant_request_id',
        'amount',
        'phone_number',
        'status',
        'term',
        'receipt_number',
        'transaction_date',
        'result_desc',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
