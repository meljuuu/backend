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
        $teacher = DB::table('teachers')->first();
        $subjects = DB::table('subjects')->get();
        if ($teacher) {
            foreach ($subjects as $subject) {
                DB::table('teachers_subject')->insert([
                    'teacher_id' => $teacher->Teacher_ID,
                    'subject_id' => $subject->Subject_ID,
                    'subject_code' => $subject->SubjectCode,
                ]);
            }
        }
    }
} 