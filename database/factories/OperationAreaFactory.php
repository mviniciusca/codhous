<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OperationArea>
 */
class OperationAreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city' => $this->faker->city(),
            'state' => 'RJ',
            'postcode_prefix' => $this->faker->numerify('####'),
            'is_active' => $this->faker->boolean(90),
            'shipping_fee' => $this->faker->randomFloat(2, 0, 50),
            'is_base' => false,
        ];
    }
}
