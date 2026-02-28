<?php

namespace Database\Factories;

use App\Enums\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductOption>
 */
class ProductOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->city();

        return [
            'name'  => $name,
            'slug'  => Str::slug($name),
            'unit'  => fake()->randomElement(ProductUnit::cases()),
            'price' => fake()->randomFloat(2, 300, 1000),
        ];
    }
}
