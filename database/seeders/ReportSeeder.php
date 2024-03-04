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
        $usersIds = User::where('role_id', '!=', UserRole::ADMIN)->limit(20)->pluck('id');

        $reports = [];
        foreach ($usersIds as $userId) {
            $reports[] = [
                'user_id' => $userId,
                'title' => fake()->title(),
                'description' => fake()->realText(),
            ];
        }

        Report::insert($reports);
    }
}
