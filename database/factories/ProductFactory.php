<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Usinado', 'Bombeado', 'Polimento de Piso', 'Concreto Hidráulico']),
            'price' => number_format($this->faker->randomFloat(2, 1, 1000), 2),
            'is_active' => true,
        ];
    }
}
