<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('teachers')->insert([
            'Email' => 'admin@example.com',
            'Password' => Hash::make('admin123'),
            'FirstName' => 'System',
            'LastName' => 'Administrator',
            'MiddleName' => null,
            'BirthDate' => '1980-01-01',
            'Sex' => 'M',
            'Position' => 'Admin',
            'ContactNumber' => '09171234567',
            'Address' => 'Admin Office, Main Campus',
        ]);
    }
}
