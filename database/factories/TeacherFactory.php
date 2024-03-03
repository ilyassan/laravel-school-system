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
class TeacherFactory extends Factory
{
    protected $model = User::class;
    
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('0#########'),
            'password' => Hash::make('password'),
            'gender' => fake()->randomElement(['M','F']),
            'role_id' => UserRole::TEACHER,
            'salary' => round(fake()->numberBetween(3500, 15000), -2),
            'remember_token' => Str::random(10),
        ];
    }
}
