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
                'student_class_id' => 1, // Alice Smith - Grade 7 - A
                'teacher_subject_id' => 1, // Mathematics (Teacher 3)
            ],
            [
                'student_class_id' => 2, // Bob Johnson - Grade 7 - A
                'teacher_subject_id' => 1, // Mathematics (Teacher 3)
            ],
            [
                'student_class_id' => 5, // Eve Lopez - Grade 7 - A
                'teacher_subject_id' => 1, // Mathematics (Teacher 3)
            ],

            // Grade 8 - A Students (StudentClass_ID: 3, 7, 8) - No subjects assigned yet
            // Carol Baker, Grace Nguyen, Henry Reyes - Grade 8 - A

            // Grade 9 - A Students (StudentClass_ID: 4, 6, 11) - English
            [
                'student_class_id' => 4, // David Cruz - Grade 9 - A
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
            [
                'student_class_id' => 6, // Frank Garcia - Grade 9 - A
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
            [
                'student_class_id' => 11, // Karen Villanueva - Grade 9 - A
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],

            // Grade 10 - A Students (StudentClass_ID: 9, 10, 12) - Araling Panlipunan
            [
                'student_class_id' => 9, // Isla Torres - Grade 10 - A
                'teacher_subject_id' => 5, // Araling Panlipunan (Teacher 5)
            ],
            [
                'student_class_id' => 10, // Jake Santos - Grade 10 - A
                'teacher_subject_id' => 5, // Araling Panlipunan (Teacher 5)
            ],
            [
                'student_class_id' => 12, // Liam Del Rosario - Grade 10 - A
                'teacher_subject_id' => 5, // Araling Panlipunan (Teacher 5)
            ],

            // Grade 11 - A Students (StudentClass_ID: 13, 14, 15) - English
            [
                'student_class_id' => 13, // Mia Fernandez - Grade 11 - A
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
            [
                'student_class_id' => 14, // Noah Lim - Grade 11 - A
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
            [
                'student_class_id' => 15, // Olivia Ramos - Grade 11 - A
                'teacher_subject_id' => 3, // English (Teacher 4)
            ],
        ];

        // Insert data into the pivot table
        DB::table('student_class_teacher_subject')->insert($relationships);
    }
}