<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('student_class')->insert([
            //-----------------------------------Grade 7 - A----------------------------------- TEACHER 3
            [
                'Student_ID' => 1, // Alice Smith
                'Class_ID' => 1,   // Grade 7 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 2, // Bob Johnson
                'Class_ID' => 1,   // Grade 7 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 5, // Eve Lopez
                'Class_ID' => 1,   // Grade 7 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            //-----------------------------------Grade 8 - A----------------------------------- TEACHER 4
            [
                'Student_ID' => 3, // Carol Baker
                'Class_ID' => 2,   // Grade 8 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 4, // TEACHER002
                'isAdvisory' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 7, // Grace Nguyen
                'Class_ID' => 2,   // Grade 8 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 4, // TEACHER002
                'isAdvisory' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 8, // Henry Reyes
                'Class_ID' => 2,   // Grade 8 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 4, // TEACHER002
                'isAdvisory' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            //-----------------------------------Grade 9 - A----------------------------------- TEACHER 5
            [
                'Student_ID' => 4, // David Cruz
                'Class_ID' => 3,   // Grade 9 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 5, // TEACHER003
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 6, // Frank Garcia
                'Class_ID' => 3,   // Grade 9 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 5, // TEACHER003
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 11, // Karen Villanueva
                'Class_ID' => 3,   // Grade 9 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 5, // TEACHER003
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            //-----------------------------------Grade 10 - A----------------------------------- TEACHER 3
            [
                'Student_ID' => 9, // Isla Torres
                'Class_ID' => 4,   // Grade 10 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 10, // Jake Santos
                'Class_ID' => 4,   // Grade 10 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 12, // Liam Del Rosario
                'Class_ID' => 4,   // Grade 10 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            //-----------------------------------Grade 11 - A----------------------------------- TEACHER 4
            [
                'Student_ID' => 13, // Mia Fernandez
                'Class_ID' => 5,   // Grade 11 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 4, // TEACHER002
                'isAdvisory' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 14, // Noah Lim
                'Class_ID' => 5,   // Grade 11 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 4, // TEACHER002
                'isAdvisory' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 15, // Olivia Ramos
                'Class_ID' => 5,   // Grade 11 - A
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 4, // TEACHER002
                'isAdvisory' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}