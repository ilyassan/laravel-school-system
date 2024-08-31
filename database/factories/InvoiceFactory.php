<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
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
            'price_excl_tax' => fake()->numberBetween(1, 150) * 10,
            'quantity' => fake()->numberBetween(1, 10),
            'tax_ratio' => fake()->randomElement([0.07, 0.10, 0.14, 0.20]),
            'payed' => fake()->boolean(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
