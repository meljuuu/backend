<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_class')->insert([
            [
                'Student_ID' => 1,
                'Class_ID' => 1,
                'ClassName' => 'Grade 10 - A',
                'SY_ID' => 1,
                'Adviser_ID' => 1,
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 2,
                'Class_ID' => 1,
                'ClassName' => 'Grade 10 - A',
                'SY_ID' => 1,
                'Adviser_ID' => 1,
                'isAdvisory' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 3,
                'Class_ID' => 2,
                'ClassName' => 'Grade 11 - STEM',
                'SY_ID' => 2,
                'Adviser_ID' => 2,
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
