<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcadbaseSeeder extends Seeder
{
    public function run()
    {
        $students = [
            [
                'lrn' => '202110048',
                'name' => 'Bueno, Ryan Joshua E.',
                'track' => 'TVL - IEM',
                'batch' => 'S.Y 2020 - 2021',
                'curriculum' => 'Senior High School',
                'status' => 'Released',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110049',
                'name' => 'Dela Cruz, Juan',
                'track' => 'HUMSS',
                'batch' => 'S.Y 2021 - 2022',
                'curriculum' => 'Senior High School',
                'status' => 'Unreleased',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110050',
                'name' => 'Reyes, Maria Clara',
                'track' => 'BEC',
                'batch' => 'S.Y 2022 - 2023',
                'curriculum' => 'JHS Grade 10',
                'status' => 'Not-Applicable',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110051',
                'name' => 'Santos, Pedro P.',
                'track' => 'SPA',
                'batch' => 'S.Y 2023 - 2024',
                'curriculum' => 'SHS Grade 12',
                'status' => 'Dropped-Out',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110052',
                'name' => 'Gonzales, Angela R.',
                'track' => 'SPJ',
                'batch' => 'S.Y 2024 - 2025',
                'curriculum' => 'SHS Grade 11',
                'status' => 'Released',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110053',
                'name' => 'Mendoza, Paul J.',
                'track' => 'TVL',
                'batch' => 'S.Y 2020 - 2021',
                'curriculum' => 'Senior High School',
                'status' => 'Unreleased',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110054',
                'name' => 'Torres, Miguel A.',
                'track' => 'HUMSS',
                'batch' => 'S.Y 2021 - 2022',
                'curriculum' => 'SHS Grade 12',
                'status' => 'Not-Applicable',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110055',
                'name' => 'Fernandez, Lucia M.',
                'track' => 'BEC',
                'batch' => 'S.Y 2022 - 2023',
                'curriculum' => 'JHS Grade 10',
                'status' => 'Dropped-Out',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110056',
                'name' => 'Navarro, Crisostomo I.',
                'track' => 'SPA',
                'batch' => 'S.Y 2023 - 2024',
                'curriculum' => 'Senior High School',
                'status' => 'Released',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110057',
                'name' => 'Luna, Maria Sofia',
                'track' => 'TVL - IEM',
                'batch' => 'S.Y 2024 - 2025',
                'curriculum' => 'SHS Grade 11',
                'status' => 'Unreleased',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110058',
                'name' => 'Cruz, Antonio R.',
                'track' => 'HUMSS',
                'batch' => 'S.Y 2020 - 2021',
                'curriculum' => 'Senior High School',
                'status' => 'Released',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110059',
                'name' => 'Mercado, Isabella T.',
                'track' => 'BEC',
                'batch' => 'S.Y 2021 - 2022',
                'curriculum' => 'JHS Grade 10',
                'status' => 'Not-Applicable',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110060',
                'name' => 'Ramos, Carlos M.',
                'track' => 'SPA',
                'batch' => 'S.Y 2022 - 2023',
                'curriculum' => 'SHS Grade 12',
                'status' => 'Dropped-Out',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110061',
                'name' => 'Santos, Gabriela P.',
                'track' => 'SPJ',
                'batch' => 'S.Y 2023 - 2024',
                'curriculum' => 'SHS Grade 11',
                'status' => 'Released',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'lrn' => '202110062',
                'name' => 'Garcia, Miguel A.',
                'track' => 'TVL',
                'batch' => 'S.Y 2024 - 2025',
                'curriculum' => 'Senior High School',
                'status' => 'Unreleased',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert the data
        foreach ($students as $student) {
            DB::table('acadbase')->insert($student);
        }
    }
}