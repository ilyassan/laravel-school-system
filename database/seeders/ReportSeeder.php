<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentIds = User::students()->limit(10)->pluck('id');
        $teacherIds = User::teachers()->limit(10)->pluck('id');

        $userIds = $studentIds->merge($teacherIds);

        $reports = [];
        foreach ($userIds as $userId) {
            $reports[] = [
                'user_id' => $userId,
                'title' => fake()->title(),
                'description' => fake()->realText(),
                'created_at' => fake()->dateTimeBetween('-2 months', 'now'),
            ];
        }

        Report::insert($reports);
    }
}
