<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolYearsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('school_years')->insert([
            [
                'SY_ID' => 1,
                'Start_Date' => '2025-06-01', // Use a valid date format
                'End_Date' => '2026-03-31',  // Use a valid date format
                'SY_Year' => '2025-2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SY_ID' => 2,
                'Start_Date' => '2026-06-01',
                'End_Date' => '2027-03-31',
                'SY_Year' => '2026-2027',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SY_ID' => 3,
                'Start_Date' => '2027-06-01',
                'End_Date' => '2028-03-31',
                'SY_Year' => '2027-2028',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SY_ID' => 4,
                'Start_Date' => '2028-06-01',
                'End_Date' => '2029-03-31',
                'SY_Year' => '2028-2029',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'SY_ID' => 5,
                'Start_Date' => '2029-06-01',
                'End_Date' => '2030-03-31',
                'SY_Year' => '2029-2030',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}