<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'email', 'phone', 'course', 'course_id', 'total_fee'];

    public function getBalanceAttribute()
    {
        $expected = $this->total_fee > 0 ? $this->total_fee : 45000;
        return $expected - $this->fees()->sum('amount');
    }

    // A student belongs to a course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // A student has many fees
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    // A student has many attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // A student has many exam results
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    // A student belongs to a user (login account)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
