<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Selected date (defaults to today)
        $date = $request->input('date', date('Y-m-d'));
        $currentCarbon = Carbon::parse($date);

        // Compute Monday to Friday of the selected date's week
        $startOfWeek = $currentCarbon->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = $startOfWeek->copy()->addDays(4); // Friday

        $weekDates = [
            'Monday'    => $startOfWeek->copy()->format('Y-m-d'),
            'Tuesday'   => $startOfWeek->copy()->addDays(1)->format('Y-m-d'),
            'Wednesday' => $startOfWeek->copy()->addDays(2)->format('Y-m-d'),
            'Thursday'  => $startOfWeek->copy()->addDays(3)->format('Y-m-d'),
            'Friday'    => $startOfWeek->copy()->addDays(4)->format('Y-m-d'),
        ];

        // Search & course filters
        $search = $request->input('search');
        $courseFilter = $request->input('course');

        $studentsQuery = Student::orderBy('name');
        if ($search) {
            $studentsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($courseFilter) {
            $studentsQuery->where('course', $courseFilter);
        }
        $students = $studentsQuery->get();

        // Fetch all attendance records for this week (Mon-Fri)
        $weeklyAttendanceRecords = Attendance::whereBetween('date', [
            $startOfWeek->format('Y-m-d'),
            $endOfWeek->format('Y-m-d')
        ])->get();

        // Key by student_id then date for fast lookup in grid: $weeklyMatrix[$studentId][$dateString]
        $weeklyMatrix = [];
        foreach ($weeklyAttendanceRecords as $record) {
            $dateStr = Carbon::parse($record->date)->format('Y-m-d');
            $weeklyMatrix[$record->student_id][$dateStr] = $record;
        }

        // Daily attendances for current selected date
        $dailyAttendances = Attendance::with('student')
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        // Distinct courses for filter dropdown
        $courses = Student::distinct()->pluck('course')->filter();

        // Statistics for the selected week
        $totalWeekRecords = $weeklyAttendanceRecords->count();
        $totalPresentCount = $weeklyAttendanceRecords->whereIn('status', ['Present', 'Late'])->count();
        $totalAbsentCount  = $weeklyAttendanceRecords->where('status', 'Absent')->count();
        $totalLateCount    = $weeklyAttendanceRecords->where('status', 'Late')->count();

        $weeklyAttendanceRate = $totalWeekRecords > 0
            ? round(($totalPresentCount / $totalWeekRecords) * 100, 1)
            : 0;

        // Active tab determination (defaults to daily if tab=daily or date requested, else weekly)
        $activeTab = $request->input('tab', $request->has('tab') ? $request->input('tab') : ($request->has('date') ? 'daily' : 'weekly'));

        return view('attendance.index', compact(
            'students',
            'dailyAttendances',
            'weeklyMatrix',
            'date',
            'startOfWeek',
            'endOfWeek',
            'weekDates',
            'courses',
            'search',
            'courseFilter',
            'weeklyAttendanceRate',
            'totalPresentCount',
            'totalAbsentCount',
            'totalLateCount',
            'activeTab'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Present,Absent,Late,Excused',
        ]);

        $date = $request->date;

        foreach ($request->attendance as $student_id => $data) {
            Attendance::updateOrCreate(
                ['student_id' => $student_id, 'date' => $date],
                ['status' => $data['status'], 'remarks' => $data['remarks'] ?? null]
            );
        }

        $formattedDate = Carbon::parse($date)->format('l, M d, Y');

        return redirect()->route('attendance.index', ['date' => $date, 'tab' => 'daily'])
            ->with('success', "Attendance recorded successfully for {$formattedDate}.");
    }
}
