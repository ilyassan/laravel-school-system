<?php

namespace Database\Seeders;

use App\Models\Classes;
use Database\Factories\StudentFactory;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all class IDs
        $classIds = Classes::pluck('id');

        // Create students for each class
        foreach ($classIds as $key => $classId) {

            $count = 40;

            if ($key === 0) {
                StudentFactory::new()->create([
                    'email' => 'student@gmail.com',
                    'class_id' => $classId,
                ]);
                $count--;
            }
            StudentFactory::new()->count($count)->create([
                'class_id' => $classId,
            ]);
        }
    }
}
