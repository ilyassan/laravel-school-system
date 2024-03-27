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
                    for ($year = 0; $year < 2; $year++) {
                        for ($month = 1; $month <= 12; $month++) {
                            $createdAt = Carbon::now()->subYear()->day(rand(1,28))->addYears($year)->month($month);
                            $grades[] = [
                                'student_id' => $student->id,
                                'teacher_id' => $teacher->id,
                                'subject_id' => $teacher->subject_id,
                                'grade' => number_format(rand(95, 200) / 10, 2), // Grade between 9.5 and 20
                                'created_at' => $createdAt, // Set the creation date
                            ];
                        }
                    }
            }
            Grade::insert($grades);
            $grades = [];
        }
    }
    }
}