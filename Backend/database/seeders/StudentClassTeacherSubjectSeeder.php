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
            // Grade 7 - A Students (StudentClass_ID: 1, 2, 5) - Mathematics
            [
                'student_class_id' => 1,
                'teacher_subject_id' => 1, // Mathematics (Teacher 3)
            ],
            [
                'student_class_id' => 2,
                'teacher_subject_id' => 1, // Mathematics (Teacher 3)
            ],
            [
                'student_class_id' => 5,
                'teacher_subject_id' => 1, // Mathematics (Teacher 3)
            ],

            // Grade 8 - A Students (StudentClass_ID: 3, 7, 8) - No subjects assigned yet
            // Add subjects here when needed

            // Grade 9 - A Students (StudentClass_ID: 4, 6, 11) - English
            [
                'student_class_id' => 4,
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
            [
                'student_class_id' => 6,
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
            [
                'student_class_id' => 11,
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],

            // Grade 10 - A Students (StudentClass_ID: 9, 10, 12) - Araling Panlipunan
            [
                'student_class_id' => 9,
                'teacher_subject_id' => 5, // Araling Panlipunan (Teacher 5)
            ],
            [
                'student_class_id' => 10,
                'teacher_subject_id' => 5, // Araling Panlipunan (Teacher 5)
            ],
            [
                'student_class_id' => 12,
                'teacher_subject_id' => 5, // Araling Panlipunan (Teacher 5)
            ],

            // Grade 11 - A Students (StudentClass_ID: 13, 14, 15) - English
            [
                'student_class_id' => 13,
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
            [
                'student_class_id' => 14,
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
            [
                'student_class_id' => 15,
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
        ];

        // Insert data into the pivot table
        DB::table('student_class_teacher_subject')->insert($relationships);
    }
}