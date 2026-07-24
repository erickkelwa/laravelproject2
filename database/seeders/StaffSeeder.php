<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Course;
use App\Models\Student;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // Gather available courses
        $courses = Course::pluck('course_name')->unique()->toArray();
        if (empty($courses)) {
            $courses = Student::distinct()->pluck('course')->filter()->toArray();
        }

        if (empty($courses)) {
            $courses = ['Computer Science', 'Mathematics', 'Electrical Engineering', 'Nursing'];
        }

        $teachers = [
            ['name' => 'Prof. Alan Turing', 'email' => 'turing@school.com', 'role' => 'Professor', 'department' => 'Computer Science Dept.'],
            ['name' => 'Dr. Marie Curie', 'email' => 'marie@school.com', 'role' => 'Senior Lecturer', 'department' => 'Science & Physics'],
            ['name' => 'Mr. Isaac Newton', 'email' => 'isaac@school.com', 'role' => 'Teacher', 'department' => 'Mathematics'],
            ['name' => 'Mrs. Ada Lovelace', 'email' => 'ada@school.com', 'role' => 'Lecturer', 'department' => 'IT & Software'],
            ['name' => 'Dr. John Snow', 'email' => 'snow@school.com', 'role' => 'Teacher', 'department' => 'Medical & Health'],
        ];

        foreach ($teachers as $index => $teacherData) {
            $courseTaught = $courses[$index % count($courses)] ?? null;

            Staff::firstOrCreate(
                ['email' => $teacherData['email']],
                [
                    'name' => $teacherData['name'],
                    'phone' => '07' . rand(10000000, 99999999),
                    'role' => $teacherData['role'],
                    'department' => $teacherData['department'],
                    'course' => $courseTaught,
                    'hire_date' => now()->subYears(rand(1, 10))->subDays(rand(1, 300))->format('Y-m-d')
                ]
            );
        }
    }
}
