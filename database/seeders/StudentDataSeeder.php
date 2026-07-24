<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentProfile;

class StudentDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Courses ─────────────────────────────────────────────────────────
        $courses = [
            ['course_name' => 'Computer Science'],
            ['course_name' => 'Business Administration'],
            ['course_name' => 'Electrical Engineering'],
            ['course_name' => 'Mathematics'],
            ['course_name' => 'Nursing'],
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(['course_name' => $course['course_name']]);
        }

        $cs  = Course::where('course_name', 'Computer Science')->first();
        $ba  = Course::where('course_name', 'Business Administration')->first();
        $ee  = Course::where('course_name', 'Electrical Engineering')->first();
        $math = Course::where('course_name', 'Mathematics')->first();
        $nur = Course::where('course_name', 'Nursing')->first();

        // ── 2. Students ────────────────────────────────────────────────────────
        $students = [
            [
                'name'      => 'Alice Johnson',
                'email'     => 'alice@example.com',
                'phone'     => '0712345678',
                'course'    => 'Computer Science',
                'course_id' => $cs->id,
                'profile'   => [
                    'gender'        => 'Female',
                    'date_of_birth' => '2002-03-15',
                    'address'       => '12 Maple Street, Nairobi',
                    'guardian_name' => 'Robert Johnson',
                ],
            ],
            [
                'name'      => 'Brian Odhiambo',
                'email'     => 'brian@example.com',
                'phone'     => '0723456789',
                'course'    => 'Business Administration',
                'course_id' => $ba->id,
                'profile'   => [
                    'gender'        => 'Male',
                    'date_of_birth' => '2001-07-22',
                    'address'       => '45 Oak Avenue, Mombasa',
                    'guardian_name' => 'Grace Odhiambo',
                ],
            ],
            [
                'name'      => 'Carol Wanjiku',
                'email'     => 'carol@example.com',
                'phone'     => '0734567890',
                'course'    => 'Electrical Engineering',
                'course_id' => $ee->id,
                'profile'   => [
                    'gender'        => 'Female',
                    'date_of_birth' => '2003-11-05',
                    'address'       => '78 Cedar Road, Kisumu',
                    'guardian_name' => 'Peter Wanjiku',
                ],
            ],
            [
                'name'      => 'David Mwangi',
                'email'     => 'david@example.com',
                'phone'     => '0745678901',
                'course'    => 'Mathematics',
                'course_id' => $math->id,
                'profile'   => [
                    'gender'        => 'Male',
                    'date_of_birth' => '2000-05-30',
                    'address'       => '23 Pine Lane, Eldoret',
                    'guardian_name' => 'Sarah Mwangi',
                ],
            ],
            [
                'name'      => 'Eva Chebet',
                'email'     => 'eva@example.com',
                'phone'     => '0756789012',
                'course'    => 'Nursing',
                'course_id' => $nur->id,
                'profile'   => [
                    'gender'        => 'Female',
                    'date_of_birth' => '2002-09-18',
                    'address'       => '56 Birch Close, Nakuru',
                    'guardian_name' => 'James Chebet',
                ],
            ],
            [
                'name'      => 'Frank Otieno',
                'email'     => 'frank@example.com',
                'phone'     => '0767890123',
                'course'    => 'Computer Science',
                'course_id' => $cs->id,
                'profile'   => [
                    'gender'        => 'Male',
                    'date_of_birth' => '2001-12-01',
                    'address'       => '90 Willow Way, Nairobi',
                    'guardian_name' => 'Mary Otieno',
                ],
            ],
        ];

        foreach ($students as $data) {
            $profileData = $data['profile'];
            unset($data['profile']);

            $student = Student::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            StudentProfile::firstOrCreate(
                ['student_id' => $student->id],
                array_merge(['student_id' => $student->id], $profileData)
            );
        }

        $this->command->info('✅  Courses, Students & StudentProfiles seeded successfully!');
    }
}
