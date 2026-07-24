<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'term',
        'term_fee',
        'amount',
        'payment_method',
        'receipt_no',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    /**
     * Get the student that owns the fee payment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
