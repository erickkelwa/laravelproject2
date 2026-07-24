<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        if ($students->isEmpty()) {
            return;
        }

        // Current week Monday - Friday
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY);

        $days = [
            0 => ['status' => 'Present', 'remark' => null],
            1 => ['status' => 'Present', 'remark' => null],
            2 => ['status' => 'Present', 'remark' => null],
            3 => ['status' => 'Present', 'remark' => null],
            4 => ['status' => 'Present', 'remark' => null],
        ];

        foreach ($students as $sIndex => $student) {
            foreach ([0, 1, 2, 3, 4] as $dayOffset) {
                $dateStr = $startOfWeek->copy()->addDays($dayOffset)->format('Y-m-d');
                
                // Mix statuses realistically for demonstration
                $status = 'Present';
                $remark = null;

                if ($sIndex === 1 && $dayOffset === 1) { // 2nd student absent on Tuesday
                    $status = 'Absent';
                    $remark = 'Sick with flu';
                } elseif ($sIndex === 2 && $dayOffset === 2) { // 3rd student late on Wednesday
                    $status = 'Late';
                    $remark = 'Traffic delay';
                } elseif ($sIndex === 3 && $dayOffset === 1) { // 4th student excused on Tuesday
                    $status = 'Excused';
                    $remark = 'Doctor appointment';
                }

                Attendance::updateOrCreate(
                    ['student_id' => $student->id, 'date' => $dateStr],
                    ['status' => $status, 'remarks' => $remark]
                );
            }
        }
    }
}
