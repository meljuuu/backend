<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonPlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lesson_plans')->insert([
            'Teacher_ID' => 1, // Example Teacher_ID
            'lesson_plan_no' => 'LP-1001',
            'grade_level' => 'Grade 5',
            'section' => 'A',
            'category' => 'Math',
            'link' => 'https://example.com/lesson-plan',
            'status' => 'Approved',
            'comments' => 'This is an example comment for the lesson plan.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}