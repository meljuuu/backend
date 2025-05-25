<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassTeacherSubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Sample data for the pivot table
        $relationships = [
            [
                'student_class_id' => 7, // Replace with actual student_class_id
                'teacher_subject_id' => 3, // Replace with actual teacher_subject_id
            ],
        ];

        // Insert data into the pivot table
        DB::table('student_class_teacher_subject')->insert($relationships);
    }
}