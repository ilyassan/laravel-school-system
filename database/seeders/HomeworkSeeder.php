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
            for($i = 0; $i < 4; $i++) {
                $createdAt = now()->instance(fake()->dateTimeBetween('-5 months', 'now'));
                $title = fake()->sentence(3);
                $classes = $teacher->classes;
    
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
        }

        Homework::insert($homeworks);
    }
}
