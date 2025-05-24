<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subjects')->insert([
            ['SubjectName' => 'Mathematics', 'SubjectCode' => 101],
            ['SubjectName' => 'English', 'SubjectCode' => 102],
            ['SubjectName' => 'Science', 'SubjectCode' => 103],
            ['SubjectName' => 'Filipino', 'SubjectCode' => 104],
            ['SubjectName' => 'Araling Panlipunan', 'SubjectCode' => 105],
            ['SubjectName' => 'MAPEH', 'SubjectCode' => 106],
            ['SubjectName' => 'EPP/TLE', 'SubjectCode' => 107],
            ['SubjectName' => 'Edukasyon sa Pagpapakatao', 'SubjectCode' => 108],
        ]);
    }
} 