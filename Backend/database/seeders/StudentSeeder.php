<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('students')->insert([
            [
                'Student_ID' => 1,
                'LRN' => '123456789012',
                'FirstName' => 'Alice',
                'LastName' => 'Smith',
                'MiddleName' => 'Marie',
                'Suffix' => 'Jr.', // Valid value
                'BirthDate' => '2010-05-10',
                'Sex' => 'F',
                'Age' => 15,
                'Religion' => 'Christian',
                'HouseNo' => '123',
                'Barangay' => 'Barangay 1',
                'Municipality' => 'City A',
                'Province' => 'Province A',
                'MotherName' => 'Jane Smith',
                'FatherName' => 'John Smith',
                'Guardian' => 'Jane Smith',
                'Relationship' => 'Mother',
                'ContactNumber' => '09171234567',
                'Curriculum' => 'JHS', // Valid value
                'Track' => 'Academic',
                'created_at' => now(),
                'updated_at' => now(),
                'Status' => 'Accepted', // Valid value
            ],
            [
                'Student_ID' => 2,
                'LRN' => '987654321098',
                'FirstName' => 'Bob',
                'LastName' => 'Johnson',
                'MiddleName' => 'Edward',
                'Suffix' => 'Sr.', // Valid value
                'BirthDate' => '2010-08-15',
                'Sex' => 'M',
                'Age' => 15,
                'Religion' => 'Christian',
                'HouseNo' => '456',
                'Barangay' => 'Barangay 2',
                'Municipality' => 'City B',
                'Province' => 'Province B',
                'MotherName' => 'Anna Johnson',
                'FatherName' => 'Robert Johnson',
                'Guardian' => 'Anna Johnson',
                'Relationship' => 'Mother',
                'ContactNumber' => '09179876543',
                'Curriculum' => 'SHS', // Valid value
                'Track' => 'Technical-Vocational',
                'created_at' => now(),
                'updated_at' => now(),
                'Status' => 'Pending', // Valid value
            ],
        ]);
    }
}