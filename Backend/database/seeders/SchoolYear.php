<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchoolYear extends Seeder
{
    public function run()
    {
        // Temporarily disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Use delete() instead of truncate() to avoid foreign key issues
        DB::table('school_years')->delete();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // Insert essential school years with explicit IDs
        DB::table('school_years')->insert([
            [
                'SY_ID' => 1,
                'Start_Date' => '2023-06-01',
                'End_Date' => '2024-03-31',
                'SY_Year' => '2023-2024',
            ],
            [
                'SY_ID' => 2,
                'Start_Date' => '2024-06-01',
                'End_Date' => '2025-03-31',
                'SY_Year' => '2024-2025',
            ],
        ]);
    }
}