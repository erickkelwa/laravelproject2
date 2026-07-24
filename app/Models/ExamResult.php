<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'term_or_semester',
        'subject',
        'score',
        'grade',
        'remarks'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
