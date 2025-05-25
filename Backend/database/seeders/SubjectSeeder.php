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
            [
                'SubjectName' => 'Mathematics',
                'SubjectCode' => 101,
                'GradeLevel' => 7
            ],
            [
                'SubjectName' => 'English',
                'SubjectCode' => 102,
                'GradeLevel' => 7
            ],
            [
                'SubjectName' => 'Science',
                'SubjectCode' => 103,
                'GradeLevel' => 7
            ],
            [
                'SubjectName' => 'Filipino',
                'SubjectCode' => 104,
                'GradeLevel' => 7
            ],
            [
                'SubjectName' => 'Araling Panlipunan',
                'SubjectCode' => 105,
                'GradeLevel' => 7
            ],
            [
                'SubjectName' => 'MAPEH',
                'SubjectCode' => 106,
                'GradeLevel' => 7
            ],
            [
                'SubjectName' => 'EPP/TLE',
                'SubjectCode' => 107,
                'GradeLevel' => 7
            ],
            [
                'SubjectName' => 'Edukasyon sa Pagpapakatao',
                'SubjectCode' => 108,
                'GradeLevel' => 7
            ],
        ]);
    }
} 