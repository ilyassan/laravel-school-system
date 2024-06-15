<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class StudentFactory extends Factory
{
    protected $model = User::class;
    
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('06########'),
            'password' => Hash::make('password'),
            'gender' => fake()->randomElement(['M','F']),
            'role_id' => UserRole::STUDENT,
            'remember_token' => Str::random(10),
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
