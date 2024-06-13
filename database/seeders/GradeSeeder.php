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

        $batchSize = 100; // Set the batch size to control memory usage
        $years = 2;

        foreach ($classes as $class) {
            foreach ($class->teachers as $teacher) {
                foreach ($class->students as $student) {
                    $grades = [];

                    for ($year = 0; $year < $years; $year++) {
                        $maxMonth = $year < $years - 1 ? 12 : Carbon::now()->month;

                        for ($month = 1; $month <= $maxMonth ; $month++) {

                            $day = ($month == Carbon::now()->month && $year == $years - 1) ? rand(1, Carbon::now()->day) : rand(1, 28);
                            $createdAt = Carbon::now()->subYear()->day($day)->addYears($year)->month($month);
                            $grades[] = [
                                'student_id' => $student->id,
                                'teacher_id' => $teacher->id,
                                'subject_id' => $teacher->subject_id,
                                'grade' => number_format(rand(95, 200) / 10, 2), // Grade between 9.5 and 20
                                'created_at' => $createdAt, // Set the creation date
                            ];

                            // Insert grades in batches
                            if (count($grades) >= $batchSize) {
                                Grade::insert($grades);
                                $grades = []; // Reset the grades array for the next batch
                            }
                        }
                    }

                    // Insert the remaining grades (if any)
                    if (!empty($grades)) {
                        Grade::insert($grades);
                    }
                }
            }
        }
    }
}