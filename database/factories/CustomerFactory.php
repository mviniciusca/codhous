<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'postcode' => $this->faker->postcode(),
            'address' => [
                'street' => $this->faker->streetAddress(),
                'number' => $this->faker->numerify('##'),
                'neighborhood' => $this->faker->city(),
                'city' => $this->faker->city(),
                'state' => $this->faker->countryCode(),
            ],
        ];
    }
}
