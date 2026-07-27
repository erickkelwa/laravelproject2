<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{

    protected $fillable = [
        'student_id',
        'gender',
        'date_of_birth',
        'address',
        'guardian_name',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
