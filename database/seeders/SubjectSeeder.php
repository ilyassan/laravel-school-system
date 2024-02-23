<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert main subjects
        $subjects = [
            'Mathematics',
            'Economics',
            'English',
            'Arabic',
        ];

        foreach ($subjects as $subjectName) {
            Subject::create(['name' => $subjectName]);
        } 
    }
}
