<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::query();

        // Search by name, email, or role
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%");
            });
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        $staff = $query->orderBy('name')->paginate(10)->withQueryString();

        // Available courses list
        $courses = Course::pluck('course_name')->unique();
        if ($courses->isEmpty()) {
            $courses = Student::distinct()->pluck('course')->filter();
        }

        // Summary KPI stats
        $totalStaff   = Staff::count();
        $teacherCount = Staff::whereIn('role', ['Teacher', 'Lecturer', 'Senior Lecturer', 'Professor'])->count();
        $assignedCoursesCount = Staff::whereNotNull('course')->distinct('course')->count('course');

        return view('staff.index', compact(
            'staff',
            'courses',
            'totalStaff',
            'teacherCount',
            'assignedCoursesCount'
        ));
    }

    public function create()
    {
        $courses = Course::pluck('course_name')->unique();
        if ($courses->isEmpty()) {
            $courses = Student::distinct()->pluck('course')->filter();
        }
        return view('staff.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:staff,email',
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'course'     => 'nullable|string|max:100',
            'hire_date'  => 'nullable|date',
        ]);

        Staff::create($request->all());

        return redirect()->route('staff.index')
            ->with('success', 'Staff member / Teacher added successfully.');
    }

    public function edit(Staff $staff)
    {
        $courses = Course::pluck('course_name')->unique();
        if ($courses->isEmpty()) {
            $courses = Student::distinct()->pluck('course')->filter();
        }
        return view('staff.edit', compact('staff', 'courses'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:staff,email,' . $staff->id,
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'course'     => 'nullable|string|max:100',
            'hire_date'  => 'nullable|date',
        ]);

        $staff->update($request->all());

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index')
            ->with('success', 'Staff member removed successfully.');
    }
}
