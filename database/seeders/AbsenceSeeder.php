<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Database\Seeder;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::students()->with('class.teachers')->get();

        $absences = [];
        foreach ($students as $key => $student) {
            $teachers = $student->class->teachers;

            if ($key % 3 == 0) {
                for ($i = 0; $i < 2; $i++) {
                    // Generate random date and time within a reasonable range
                    $from = now()->subDays(rand(1, 30))->setTime(fake()->randomElement([rand(8, 10), rand(2, 4)]), 0);
                    if ($from->isSunday())
                        $from->addDay();
                    $to = $from->copy()->addHours(rand(1, 2));

                    $absences[] = [
                        'student_id' => $student->getKey(),
                        'teacher_id' => fake()->randomElement($teachers->pluck('id')),
                        'from' => $from,
                        'to' => $to,
                        'created_at' => $to->copy()->addDays(rand(1, 2)),
                    ];
                }
            }
        }

        Absence::insert($absences);
    }
}
