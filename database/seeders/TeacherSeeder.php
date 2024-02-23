<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Enums\UserRole;
use App\Models\Subject;
use Database\Factories\TeacherFactory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::pluck('id'); // Get all subject IDs

        TeacherFactory::new()->count(5)->create()->each(function ($teacher) use ($subjects) {
            $teacher->subject_id = $subjects->random();
            $teacher->save();
        });
    }
}
