<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Absence;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentsIds = User::where('role_id', UserRole::STUDENT)->limit(10)->pluck('id');
        $teacherId = User::where('role_id', UserRole::TEACHER)->first()->id;
        
        foreach ($studentsIds as $studentId) {
            Absence::create([
                'student_id' => $studentId,
                'teacher_id' => $teacherId,
                'from' => "10AM",
                'to' => "12PM",
            ]);
        }
    }
}
