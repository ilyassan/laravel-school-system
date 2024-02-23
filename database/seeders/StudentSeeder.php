<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Classes;
use Database\Factories\StudentFactory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = Classes::pluck('id');

        // Create 10 users for each class
        foreach ($classes as $classId) {
            StudentFactory::new()->count(10)->create()->each(function ($user) use ($classId){
                $user->class_id = $classId;
                $user->save();
             });
        };
    }
}
