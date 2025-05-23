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
        ]);
    }
}