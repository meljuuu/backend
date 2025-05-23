<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('student_class')->insert([
            [
                'Student_ID' => 1, // Alice
                'Class_ID' => 1,   // Grade 10 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // Changed from Advisory_ID to Adviser_ID
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 2, // Bob
                'Class_ID' => 1,   // Grade 10 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 1, // Changed from Advisory_ID to Adviser_ID
                'isAdvisory' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}