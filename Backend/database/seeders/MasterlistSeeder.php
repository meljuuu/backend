<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\acadbase\MasterlistModel;

class MasterlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'lrn' => '202110048',
                'name' => 'Bueno, Ryan Joshua E.',
                'track' => 'TVL - IEM',
                'curriculum' => 'Senior High School',
                'batch' => 'S.Y 2020 - 2021',
                'status' => 'Approved'
            ],
            [
                'lrn' => '202110049',
                'name' => 'Dela Cruz, Juan',
                'track' => 'HUMSS',
                'curriculum' => 'Senior High School',
                'batch' => 'S.Y 2021 - 2022',
                'status' => 'Review'
            ],
            [
                'lrn' => '202110050',
                'name' => 'Reyes, Maria Clara',
                'track' => 'BEC',
                'curriculum' => 'JHS Grade 10',
                'batch' => 'S.Y 2022 - 2023',
                'status' => 'Not-Applicable'
            ],
            [
                'lrn' => '202110051',
                'name' => 'Santos, Pedro P.',
                'track' => 'SPA',
                'curriculum' => 'SHS Grade 12',
                'batch' => 'S.Y 2023 - 2024',
                'status' => 'Revised'
            ],
            [
                'lrn' => '202110052',
                'name' => 'Gonzales, Angela R.',
                'track' => 'SPJ',
                'batch' => 'S.Y 2024 - 2025',
                'curriculum' => 'SHS Grade 11',
                'status' => 'Approved'
            ],
            [
                'lrn' => '202110053',
                'name' => 'Mendoza, Paul J.',
                'track' => 'TVL',
                'curriculum' => 'Senior High School',
                'batch' => 'S.Y 2020 - 2021',
                'status' => 'Review'
            ],
            [
                'lrn' => '202110054',
                'name' => 'Torres, Miguel A.',
                'track' => 'HUMSS',
                'curriculum' => 'SHS Grade 12',
                'batch' => 'S.Y 2021 - 2022',
                'status' => 'Approved'
            ],
            [
                'lrn' => '202110055',
                'name' => 'Fernandez, Lucia M.',
                'track' => 'BEC',
                'curriculum' => 'JHS Grade 10',
                'batch' => 'S.Y 2022 - 2023',
                'status' => 'Revised'
            ],
            [
                'lrn' => '202110056',
                'name' => 'Navarro, Crisostomo I.',
                'track' => 'SPA',
                'curriculum' => 'Senior High School',
                'batch' => 'S.Y 2023 - 2024',
                'status' => 'Approved'
            ],
            [
                'lrn' => '202110057',
                'name' => 'Lopez, Maria Sofia',
                'track' => 'TVL - IEM',
                'curriculum' => 'SHS Grade 11',
                'batch' => 'S.Y 2024 - 2025',
                'status' => 'Review'
            ]
        ];

        foreach ($students as $student) {
            MasterlistModel::create($student);
        }
    }
}
