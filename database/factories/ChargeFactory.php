<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Charge>
 */
class ChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2, true),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(1, 200) * 10,
            'quantity' => fake()->numberBetween(1, 10),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
