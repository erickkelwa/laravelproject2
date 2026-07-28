<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Fee;
use Illuminate\Support\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have at least a few students
        if (Student::count() < 3) {
            Student::create(['name' => 'John Doe', 'email' => 'john' . rand(1,999) . '@example.com', 'phone' => '123456789', 'course' => 'Computer Science']);
            Student::create(['name' => 'Jane Smith', 'email' => 'jane' . rand(1,999) . '@example.com', 'phone' => '987654321', 'course' => 'Information Technology']);
            Student::create(['name' => 'Alice Johnson', 'email' => 'alice' . rand(1,999) . '@example.com', 'phone' => '555123456', 'course' => 'Software Engineering']);
        }

        // Ensure students have total_fee set
        Student::all()->each(function ($student) {
            if (!$student->total_fee || $student->total_fee == 0) {
                $student->update(['total_fee' => 45000.00]);
            }
        });

        $studentIds = Student::pluck('id')->toArray();
        if (empty($studentIds)) return;

        // Seed sample term-wise fee records if none exist
        if (Fee::count() == 0) {
            $terms = ['Term 1', 'Term 2', 'Term 3'];
            $methods = ['Mpesa', 'Bank Transfer', 'Cash', 'Cheque'];

            foreach ($studentIds as $sId) {
                // Term 1
                Fee::create([
                    'student_id'     => $sId,
                    'term'           => 'Term 1',
                    'term_fee'       => 15000.00,
                    'amount'         => 15000.00,
                    'payment_method' => 'Mpesa',
                    'receipt_no'     => 'REC-T1-' . rand(1000, 9999),
                    'payment_date'   => Carbon::now()->subMonths(4),
                ]);

                // Term 2
                Fee::create([
                    'student_id'     => $sId,
                    'term'           => 'Term 2',
                    'term_fee'       => 15000.00,
                    'amount'         => rand(0, 1) ? 15000.00 : 8000.00,
                    'payment_method' => 'Bank Transfer',
                    'receipt_no'     => 'REC-T2-' . rand(1000, 9999),
                    'payment_date'   => Carbon::now()->subMonths(1),
                ]);
            }
        }
    }
}
