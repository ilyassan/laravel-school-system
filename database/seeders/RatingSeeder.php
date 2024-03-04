<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rating;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersIds = User::where('role_id', '!=', UserRole::ADMIN)->pluck('id');

        $ratings = [];
        foreach ($usersIds as $userId) {
            $ratings[] = [
                'user_id' => $userId,
                'rating' => rand(1, 5),
                'comment' => fake()->realText(),
            ];
        }

        Rating::insert($ratings);
    }
}
