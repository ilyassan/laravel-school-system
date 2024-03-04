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
        foreach ($classIds as $classId) {
            StudentFactory::new()->count(10)->create([
                'class_id' => $classId,
            ]);
        }
    }
}
