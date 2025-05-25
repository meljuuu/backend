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
                'student_class_id' => 1, // Replace with actual student_class_id
                'teacher_subject_id' => 1, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 2, // Replace with actual student_class_id
                'teacher_subject_id' => 1, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 3, // Replace with actual student_class_id
                'teacher_subject_id' => 1, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 4, // Replace with actual student_class_id
                'teacher_subject_id' => 3, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 5, // Replace with actual student_class_id
                'teacher_subject_id' => 3, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 6, // Replace with actual student_class_id
                'teacher_subject_id' => 3, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 7, // Replace with actual student_class_id
                'teacher_subject_id' => 2, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 8, // Replace with actual student_class_id
                'teacher_subject_id' => 2, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 9, // Replace with actual student_class_id
                'teacher_subject_id' => 2, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 10, // Replace with actual student_class_id
                'teacher_subject_id' => 5, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 11, // Replace with actual student_class_id
                'teacher_subject_id' => 5, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 12, // Replace with actual student_class_id
                'teacher_subject_id' => 5, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 13, // Replace with actual student_class_id
                'teacher_subject_id' => 6, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 14, // Replace with actual student_class_id
                'teacher_subject_id' => 6, // Replace with actual teacher_subject_id
            ],
            [
                'student_class_id' => 15, // Replace with actual student_class_id
                'teacher_subject_id' => 6, // Replace with actual teacher_subject_id
            ],
        ];

        // Insert data into the pivot table
        DB::table('student_class_teacher_subject')->insert($relationships);
    }
}