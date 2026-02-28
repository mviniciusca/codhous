<?php

namespace Database\Factories;

use App\Models\ProductOption;
use App\Enums\ProductUnit;
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
            'name'      => $this->faker->randomElement(['Concreto Bombeado', 'Polimento de Piso', 'Aluguel de Maquinário']),
            'is_active' => true,
        ];
    }

    /**
     * State for Concreto Bombeado
     */
    public function concreto(): self
    {
        return $this->state(['name' => 'Concreto Bombeado'])
            ->has(ProductOption::factory()->count(4)->sequence(
                ['name' => 'FCK 20', 'slug' => 'fck-20', 'unit' => ProductUnit::M3, 'price' => 380.00],
                ['name' => 'FCK 30', 'slug' => 'fck-30', 'unit' => ProductUnit::M3, 'price' => 410.00],
                ['name' => 'FCK 40', 'slug' => 'fck-40', 'unit' => ProductUnit::M3, 'price' => 440.00],
                ['name' => 'FCK 50', 'slug' => 'fck-50', 'unit' => ProductUnit::M3, 'price' => 480.00],
            ));
    }

    /**
     * State for Polimento de Piso
     */
    public function polimento(): self
    {
        return $this->state(['name' => 'Polimento de Piso'])
            ->has(ProductOption::factory()->count(1)->state([
                'name'  => 'Polimento',
                'slug'  => 'polimento',
                'unit'  => ProductUnit::M2,
                'price' => 35.00,
            ]));
    }

    /**
     * State for Aluguel de Maquinário
     */
    public function maquinario(): self
    {
        return $this->state(['name' => 'Aluguel de Maquinário'])
            ->has(ProductOption::factory()->count(1)->state([
                'name'  => 'Diária',
                'slug'  => 'diaria',
                'unit'  => ProductUnit::DAY,
                'price' => 250.00,
            ]));
    }
}
