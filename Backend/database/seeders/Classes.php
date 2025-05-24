<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Classes extends Seeder
{
    public function run()
    {
        DB::table('classes')->insert([
            [
                'ClassName' => 'Grade 7 - Section A',
                'Section' => 'A',
                'SY_ID' => 1, // Ensure this matches an existing school year ID
                'Grade_Level' => '7',
                'Status' => 'Accepted',
                'Track' => null,
                'Adviser_ID' => 1, // Ensure this matches an existing teacher ID
                'Curriculum' => 'JHS',
                'comments' => 'Sample class for Grade 7',
            ],
            [
                'ClassName' => 'Grade 11 - STEM',
                'Section' => 'STEM',
                'SY_ID' => 1,
                'Grade_Level' => '11',
                'Status' => 'Accepted',
                'Track' => 'STEM',
                'Adviser_ID' => 2,
                'Curriculum' => 'SHS',
                'comments' => 'Sample class for Grade 11 STEM',
            ],
        ]);
    }
}