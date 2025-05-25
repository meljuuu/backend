<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('student_class')->insert([
            //-----------------------------------Grade 7 A----------------------------------- TEACHER 3
            [
                'Student_ID' => 4, // Alice Smith
                'Class_ID' => 1,   // Grade 7 A
                'ClassName' => 'Grade 7 A',
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}