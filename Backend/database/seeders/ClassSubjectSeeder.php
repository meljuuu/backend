<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $classes = DB::table('classes')->get();
        $subjects = DB::table('subjects')->get();
        $teachers = DB::table('teachers')->get();
        $schoolYear = DB::table('school_years')->orderByDesc('SY_ID')->first();

        if ($classes->isEmpty() || $subjects->isEmpty() || $teachers->isEmpty() || !$schoolYear) {
            return;
        }

        foreach ($classes as $class) {
            foreach ($subjects as $subject) {
                $randomTeacher = $teachers->random();
                DB::table('class_subject')->insert([
                    'Class_ID' => $class->Class_ID,
                    'Subject_ID' => $subject->Subject_ID,
                    'Teacher_ID' => $randomTeacher->Teacher_ID,
                    'SY_ID' => $schoolYear->SY_ID,
                    // Add Student_ID if required, or leave null if not needed
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
} 