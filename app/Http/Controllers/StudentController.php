<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Student::query();
        
        // Search functionality (Bonus feature)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%");
        }
        
        // Pagination (Bonus feature)
        $students = $query->latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'phone' => 'nullable|string|max:20',
            'course' => 'required|string|max:100',
            'total_fee' => 'required|numeric|min:0',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('password123'), // Default password for new students
            'role' => 'student',
        ]);

        $studentData = $request->all();
        $studentData['user_id'] = $user->id;

        \App\Models\Student::create($studentData);

        \Illuminate\Support\Facades\Notification::send(
            \App\Models\User::all(),
            new \App\Notifications\NewStudentEnrolled($request->name)
        );

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(\App\Models\Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(\App\Models\Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, \App\Models\Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'course' => 'required|string|max:100',
            'total_fee' => 'required|numeric|min:0',
        ]);

        $student->update($request->all());

        if ($student->user) {
            $student->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(\App\Models\Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
