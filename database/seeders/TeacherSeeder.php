<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Database\Factories\TeacherFactory;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjectsIds = Subject::pluck('id'); // Get all subject IDs
        $classesIds = Classes::pluck('id'); // Get all class IDs

        $teachers = TeacherFactory::new()->count($classesIds->count() * 5)->create();

        foreach ($teachers as $index => $teacher) {
            // Assign each teacher to a class
            if ($index < $classesIds->count()) {
                $classId = $classesIds[$index];
            }
            $randomClassesId = $classesIds->filter(fn($id) => $id !== $classId)->shuffle()->unique()->take(3);
            $teacher->classes()->attach([$classId, ...$randomClassesId]);

            // give teacher a subject
            $subjectId = $subjectsIds->random();
            if ($index < $subjectsIds->count()) {
                $subjectId = $subjectsIds[$index];
            }
            $teacher->subject_id = $subjectId;
            $teacher->save();
        }
    }
}
