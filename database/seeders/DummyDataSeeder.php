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

        $studentIds = Student::pluck('id')->toArray();

        // Create 5 fee records
        Fee::create([
            'student_id' => $studentIds[array_rand($studentIds)],
            'amount' => 15000.00,
            'payment_method' => 'Mpesa',
            'receipt_no' => 'MPESA12345',
            'payment_date' => Carbon::now()->subDays(2)
        ]);

        Fee::create([
            'student_id' => $studentIds[array_rand($studentIds)],
            'amount' => 25000.50,
            'payment_method' => 'Bank Transfer',
            'receipt_no' => 'BNK98765',
            'payment_date' => Carbon::today()
        ]);

        Fee::create([
            'student_id' => $studentIds[array_rand($studentIds)],
            'amount' => 10000.00,
            'payment_method' => 'Cash',
            'receipt_no' => 'CSH001',
            'payment_date' => Carbon::now()->subDays(5)
        ]);

        Fee::create([
            'student_id' => $studentIds[array_rand($studentIds)],
            'amount' => 30000.00,
            'payment_method' => 'Mpesa',
            'receipt_no' => 'MPESA99887',
            'payment_date' => Carbon::today()
        ]);

        Fee::create([
            'student_id' => $studentIds[array_rand($studentIds)],
            'amount' => 12500.00,
            'payment_method' => 'Cheque',
            'receipt_no' => 'CHQ44556',
            'payment_date' => Carbon::now()->subDays(1)
        ]);
    }
}
