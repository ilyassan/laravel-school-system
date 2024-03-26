<?php

namespace Database\Seeders;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Database\Seeder;

class HomeworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = User::teachers()->with('classes:id')->get('id');
        
        $homeworks = [];
        foreach ($teachers as $teacher) {
            $createdAt = now()->instance(fake()->dateTimeBetween('-3 months', 'now'));
            $title = fake()->title();
            
            // Random 2 classes for each teacher
            $classes = fake()->randomElements($teacher->classes, 2, false);

            foreach ($classes as $class) {
                $homeworks[] = [
                    'title' => $title,
                    'teacher_id' => $teacher->id,
                    'class_id' => $class->id,
                    'end_date' => $createdAt->clone()->addWeeks(rand(1, 2)),
                    'created_at' => $createdAt,
                ];
            }
        }

        Homework::insert($homeworks);
    }
}
