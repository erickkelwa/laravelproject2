<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Fee;
use App\Models\MpesaTransaction;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            // Student record not found for this user
            return view('student.error', ['message' => 'Your student profile has not been linked yet.']);
        }

        // Fetch recent payments for this student
        $payments = Fee::where('student_id', $student->id)->latest()->take(5)->get();
        $transactions = MpesaTransaction::where('student_id', $student->id)->latest()->take(5)->get();

        return view('student.dashboard', compact('student', 'payments', 'transactions'));
    }
}
