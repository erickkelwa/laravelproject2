<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamResultController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->input('term', 'Term 1 - 2026');
        
        $examResults = ExamResult::with('student')
            ->where('term_or_semester', $term)
            ->get();
            
        $students = Student::orderBy('name')->get();

        return view('exams.index', compact('examResults', 'students', 'term'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'term_or_semester' => 'required|string',
            'subject' => 'required|string',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $score = $request->score;
        $grade = $this->calculateGrade($score);

        ExamResult::create([
            'student_id' => $request->student_id,
            'term_or_semester' => $request->term_or_semester,
            'subject' => $request->subject,
            'score' => $score,
            'grade' => $grade,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('exams.index', ['term' => $request->term_or_semester])
            ->with('success', 'Exam result recorded successfully.');
    }

    public function destroy(ExamResult $exam)
    {
        $term = $exam->term_or_semester;
        $exam->delete();
        
        return redirect()->route('exams.index', ['term' => $term])
            ->with('success', 'Exam result deleted successfully.');
    }

    private function calculateGrade($score)
    {
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'E';
    }
}
