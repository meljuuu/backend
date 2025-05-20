<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('classes')->insert([
            [
                'Class_ID' => 1,
                'Teacher_ID' => 1, // Link to the teacher created in TeacherSeeder
                'ClassName' => 'Grade 10 - A', // Correct column name
                'Section' => 'A', // Add other required fields
                'SY_ID' => 1, // Add other required fields
                'Grade_Level' => '10', // Add other required fields
                'Track' => 'Academic', // Add other required fields
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Class_ID' => 2,
                'Teacher_ID' => 1, // Same teacher for another class
                'ClassName' => 'Grade 10 - B', // Correct column name
                'Section' => 'B', // Add other required fields
                'SY_ID' => 1, // Add other required fields
                'Grade_Level' => '10', // Add other required fields
                'Track' => 'Academic', // Add other required fields
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}