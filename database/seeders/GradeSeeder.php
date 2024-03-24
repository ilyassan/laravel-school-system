<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Grade;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = Classes::with('students', 'teachers')->get();

        $grades = [];

        foreach ($classes as $class) {
            foreach ($class->teachers as $teacher) {
                foreach ($class->students as $student) {
                    $grades[] = [
                        'student_id' => $student->id,
                        'teacher_id' => $teacher->id,
                        'subject_id' => $teacher->subject_id,
                        'grade' => number_format(rand(80, 200) / 10, 2), // Grade between 8 and 20
                        'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
                    ];
                }
            }
        }
        Grade::insert($grades);
    }
}
