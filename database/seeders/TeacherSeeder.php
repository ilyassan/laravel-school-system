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
        $subjects = Subject::all(); // Get all subjects
        $classes = Classes::all(); // Get all classes

        $teachers = TeacherFactory::new()->count($subjects->count() * 4 - 1)->create();
        $teachers[] = TeacherFactory::new()->create([
            'email' => 'teacher@gmail.com'
        ]);

        $subjectTeacherMap = [];

        foreach ($subjects as $subject) {
            $subjectTeacherMap[$subject->id] = $teachers->where('subject_id', null)->take(4)->each(function ($teacher) use ($subject) {
                $teacher->subject_id = $subject->id;
                $teacher->save();
            });
        }

        foreach ($classes as $class) {
            foreach ($subjects as $subject) {
                $availableTeachers = $subjectTeacherMap[$subject->id]->filter(function ($teacher) {
                    return $teacher->classes->count() < 4;
                });

                $teacher = $availableTeachers->random();
                $teacher->classes()->attach($class->id);
            }
        }
    }
}
