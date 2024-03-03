<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
        $studentsIds = User::students()->limit(10)->pluck('id');
        $teacherId = User::teachers()->first()->id;
        
        $absences = [];
        foreach ($studentsIds as $studentId) {
            $absences[] = [
                'student_id' => $studentId,
                'teacher_id' => $teacherId,
                'from' => "10AM",
                'to' => "12PM",
            ];
        }
        Absence::insert($absences);
    }
}
