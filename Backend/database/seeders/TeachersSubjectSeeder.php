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
            [
                'teacher_id' => 1, // Ensure this teacher_id exists in the teachers table
                'subject_id' => 1, // Ensure this subject_id exists in the subjects table
                'subject_code' => 'MATH101',
            ],
        ];

        // Insert the data into the teachers_subject table
        DB::table('teachers_subject')->insert($teachersSubjects);
    }
}