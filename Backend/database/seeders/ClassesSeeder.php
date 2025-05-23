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
                'ClassName' => 'Grade 10 - A',
                'Section' => 'A',
                'SY_ID' => 1,
                'Grade_Level' => '10',
                'Status' => 'Accepted',
                'Track' => 'Academic',
                'Adviser_ID' => 1,
                'Curriculum' => 'JHS',
                'comments' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ClassName' => 'Grade 10 - B',
                'Section' => 'B',
                'SY_ID' => 1,
                'Grade_Level' => '10',
                'Status' => 'Accepted',
                'Track' => 'Academic',
                'Adviser_ID' => 1,
                'Curriculum' => 'JHS',
                'comments' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}