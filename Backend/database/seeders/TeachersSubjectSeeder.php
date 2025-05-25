<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeachersSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data for the teachers_subject table
        $teachersSubjects = [
            // Teacher 1 (TEACHER001) - Mathematics and Science
            [
                'teacher_id' => 3, // TEACHER001
                'subject_id' => 1, // Mathematics
                'subject_code' => 'MATH101',
            ],
            [
                'teacher_id' => 3,
                'subject_id' => 3, // Science
                'subject_code' => 'SCI101',
            ],

            // Teacher 2 (TEACHER002) - English and Filipino
            [
                'teacher_id' => 4, // TEACHER002
                'subject_id' => 2, // English
                'subject_code' => 'ENG101',
            ],
            [
                'teacher_id' => 4,
                'subject_id' => 4, // Filipino
                'subject_code' => 'FIL101',
            ],

            // Teacher 3 (TEACHER003) - Araling Panlipunan and MAPEH
            [
                'teacher_id' => 5, // TEACHER003
                'subject_id' => 5, // Araling Panlipunan
                'subject_code' => 'AP101',
            ],
            [
                'teacher_id' => 5,
                'subject_id' => 6, // MAPEH
                'subject_code' => 'MAPEH101',
            ],
        ];

        // Insert the data into the teachers_subject table
        DB::table('teachers_subject')->insert($teachersSubjects);
    }
}