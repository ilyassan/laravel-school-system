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
                $createdAt = Carbon::createFromTimestamp(mt_rand(strtotime('2023-09-01'), time()));
                foreach ($class->students as $student) {
                    $grades[] = [
                        'student_id' => $student->id,
                        'teacher_id' => $teacher->id,
                        'subject_id' => $teacher->subject_id,
                        'grade' => number_format(mt_rand(0, 200) / 10, 2),
                        'created_at' => $createdAt,
                    ];
                }
            }
        }
        Grade::insert($grades);
    }
}
