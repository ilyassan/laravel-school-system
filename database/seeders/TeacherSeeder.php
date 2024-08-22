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
        $subjects = Subject::pluck('id'); // Get subjects Id's
        $classes = Classes::pluck('id'); // Get classes Id's

        $maxClassesForTeacher = 4;

        $teachersCount = floor($classes->count() / $maxClassesForTeacher) * $subjects->count() +
            ($classes->count() % $maxClassesForTeacher) * $subjects->count();

        $teachers = TeacherFactory::new()->count($teachersCount - 1)->create();
        $teachers[] = TeacherFactory::new()->create([
            'email' => 'teacher@gmail.com'
        ]);

        // Give each teacher a subject
        for ($i = 0; $i < $teachers->count(); $i++) {
            $teachers[$i]->subject_id = $subjects[$i % $subjects->count()];
            $teachers[$i]->save();
        }

        // Ensure that every teacher attach to a class
        // Ensure that every teacher have less or equale than 4 classes
        $h = 0;
        foreach ($teachers as $i => $teacher) {
            if ($i != 0 && $i % $subjects->count() == 0) {
                $h += $maxClassesForTeacher;
            }
            for ($j = 0 + $h; ($j < $maxClassesForTeacher + $h && $j < $classes->count()); $j++) {
                $teacher->classes()->attach($classes[$j]);
            }
        }
    }
}
