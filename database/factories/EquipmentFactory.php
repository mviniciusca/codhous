<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'category' => fake()->randomElement(['Preparação de Piso', 'Acabamento de Piso', 'Limpeza Industrial', 'Transporte de Água']),
            'description' => fake()->paragraph(),
            'is_available' => fake()->boolean(80),
        ];
    }
}
