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
                'ClassName' => 'Grade 7 - A',
                'Section' => 'A',
                'SY_ID' => 1,
                'Grade_Level' => '7',
                'Status' => 'incomplete',
                'Track' => 'Academic',
                'Adviser_ID' => 3,
                'Curriculum' => 'JHS',
                'comments' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ClassName' => 'Grade 7 - B',
                'Section' => 'B',
                'SY_ID' => 1,
                'Grade_Level' => '7',
                'Status' => 'incomplete',
                'Track' => 'Academic',
                'Adviser_ID' => 4,
                'Curriculum' => 'JHS',
                'comments' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'ClassName' => 'Grade 8 - A',
            //     'Section' => 'A',
            //     'SY_ID' => 1,
            //     'Grade_Level' => '8',
            //     'Status' => 'Accepted',
            //     'Track' => 'Academic',
            //     'Adviser_ID' => 3,
            //     'Curriculum' => 'JHS',
            //     'comments' => null,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'ClassName' => 'Grade 11 - A',
            //     'Section' => 'A',
            //     'SY_ID' => 1,
            //     'Grade_Level' => '11',
            //     'Status' => 'Accepted',
            //     'Track' => 'STEM', // You can change to HUMSS, ABM, etc.
            //     'Adviser_ID' => 5,
            //     'Curriculum' => 'SHS',
            //     'comments' => null,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'ClassName' => 'Grade 12 - A',
            //     'Section' => 'A',
            //     'SY_ID' => 1,
            //     'Grade_Level' => '12',
            //     'Status' => 'Accepted',
            //     'Track' => 'STEM',
            //     'Adviser_ID' => 5,
            //     'Curriculum' => 'SHS',
            //     'comments' => null,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ]);
    }
}
