<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectsTableSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Subjects
            ['SubjectName' => 'Mathematics', 'SubjectCode' => 101],
            ['SubjectName' => 'Science', 'SubjectCode' => 102],
            ['SubjectName' => 'English', 'SubjectCode' => 103],
            ['SubjectName' => 'Filipino', 'SubjectCode' => 104],
            ['SubjectName' => 'Araling Panlipunan', 'SubjectCode' => 105],
            
            // JHS Additional Subjects
            ['SubjectName' => 'MAPEH', 'SubjectCode' => 106],
            ['SubjectName' => 'TLE', 'SubjectCode' => 107],
            ['SubjectName' => 'Values Education', 'SubjectCode' => 108],
            ['SubjectName' => 'Computer Science', 'SubjectCode' => 109],
            
            // SHS Core Subjects
            ['SubjectName' => 'General Mathematics', 'SubjectCode' => 201],
            ['SubjectName' => 'Statistics and Probability', 'SubjectCode' => 202],
            ['SubjectName' => 'Earth and Life Science', 'SubjectCode' => 203],
            ['SubjectName' => 'Physical Science', 'SubjectCode' => 204],
            ['SubjectName' => 'Introduction to Philosophy', 'SubjectCode' => 205],
            ['SubjectName' => 'Personal Development', 'SubjectCode' => 206],
            
            // SHS Applied Subjects
            ['SubjectName' => 'Practical Research 1', 'SubjectCode' => 301],
            ['SubjectName' => 'Practical Research 2', 'SubjectCode' => 302],
            ['SubjectName' => 'Inquiries, Investigations and Immersion', 'SubjectCode' => 303],
            ['SubjectName' => 'English for Academic and Professional Purposes', 'SubjectCode' => 304],
            
            // SHS Specialized Subjects (STEM)
            ['SubjectName' => 'Pre-Calculus', 'SubjectCode' => 401],
            ['SubjectName' => 'Basic Calculus', 'SubjectCode' => 402],
            ['SubjectName' => 'General Biology 1', 'SubjectCode' => 403],
            ['SubjectName' => 'General Biology 2', 'SubjectCode' => 404],
            ['SubjectName' => 'General Physics 1', 'SubjectCode' => 405],
            ['SubjectName' => 'General Physics 2', 'SubjectCode' => 406],
            ['SubjectName' => 'General Chemistry 1', 'SubjectCode' => 407],
            ['SubjectName' => 'General Chemistry 2', 'SubjectCode' => 408],
            
            // SHS Specialized Subjects (HUMSS)
            ['SubjectName' => 'Creative Writing', 'SubjectCode' => 501],
            ['SubjectName' => 'Creative Nonfiction', 'SubjectCode' => 502],
            ['SubjectName' => 'World Religions and Belief Systems', 'SubjectCode' => 503],
            ['SubjectName' => 'Disciplines and Ideas in Social Sciences', 'SubjectCode' => 504],
            ['SubjectName' => 'Disciplines and Ideas in Applied Social Sciences', 'SubjectCode' => 505],
            ['SubjectName' => 'Philippine Politics and Governance', 'SubjectCode' => 506],
            ['SubjectName' => 'Community Engagement, Solidarity, and Citizenship', 'SubjectCode' => 507],
            
            // SHS Specialized Subjects (ABM)
            ['SubjectName' => 'Fundamentals of Accountancy, Business and Management 1', 'SubjectCode' => 601],
            ['SubjectName' => 'Fundamentals of Accountancy, Business and Management 2', 'SubjectCode' => 602],
            ['SubjectName' => 'Business Math', 'SubjectCode' => 603],
            ['SubjectName' => 'Business Finance', 'SubjectCode' => 604],
            ['SubjectName' => 'Organization and Management', 'SubjectCode' => 605],
            ['SubjectName' => 'Principles of Marketing', 'SubjectCode' => 606],
            
            // SHS Specialized Subjects (GAS)
            ['SubjectName' => 'Humanities 1', 'SubjectCode' => 701],
            ['SubjectName' => 'Humanities 2', 'SubjectCode' => 702],
            ['SubjectName' => 'Social Science 1', 'SubjectCode' => 703],
            ['SubjectName' => 'Applied Economics', 'SubjectCode' => 704],
            ['SubjectName' => 'Organization and Management', 'SubjectCode' => 705],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert([
                'SubjectName' => $subject['SubjectName'],
                'SubjectCode' => $subject['SubjectCode'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}