<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Enums\UserRole;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Database\Factories\TeacherFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjectsIds = Subject::pluck('id'); // Get all subject IDs
        $classesIds = Classes::pluck('id'); // Get all class IDs

        $teachers = TeacherFactory::new()->count(6)->create();

        foreach ($teachers as $index => $teacher) {
            // Assign each teacher to a class
            if ($index < $classesIds->count()) {
                $classId = $classesIds[$index];
            }
            $randomClassId = collect($classesIds)->filter(fn($id) => $id !== $classId)->random();
            $teacher->classes()->attach([$classId, $randomClassId]);

            // Assign a random subject to each teacher
            $teacher->subject_id = $subjectsIds->random();
            $teacher->save();
        }
    }
}
