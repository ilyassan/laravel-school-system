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
        $users = User::where('role_id', '!=', UserRole::ADMIN)->get();

        $ratings = [];
        foreach ($users as $user) {
            $ratings[] = [
                'user_id' => $user->id,
                'rating' => rand(1, 5),
                'comment' => fake()->realText(),
            ];
        }

        Rating::insert($ratings);
    }
}
