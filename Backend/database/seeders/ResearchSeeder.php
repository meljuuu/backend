<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResearchSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('research')->insert([
            'Teacher_ID' => 1, // Example Teacher_ID
            'Title' => 'Example Research Title',
            'Abstract' => 'This is an example abstract for the research.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}