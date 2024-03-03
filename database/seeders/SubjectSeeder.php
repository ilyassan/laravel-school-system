<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert main subjects
        $subjectsName = [
            'Mathematics',
            'Economics',
            'English',
            'Arabic',
            'History',
            'Information technology',
        ];

        $subjects = [];
        foreach ($subjectsName as $subjectName) {
            $subjects[] = ['name' => $subjectName];
        } 

        Subject::insert($subjects);
    }
}
