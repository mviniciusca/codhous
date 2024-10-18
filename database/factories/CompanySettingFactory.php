<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanySetting>
 */
class CompanySettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trade_name' => $this->faker->company(),
            'legal_name' => $this->faker->company(),
            'address' => [
                'postcode' => $this->faker->numerify('2####-###'),
                'street' => $this->faker->streetName(),
                'number' => $this->faker->randomNumber(),
                'neighborhood' => $this->faker->city(),
                'city' => $this->faker->city(),
                'state' => $this->faker->countryCode(),
            ],
            'cnpj' => $this->faker->numerify('##.###.###/####-##'),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'budget_information' => $this->faker->sentence(),

        ];
    }
}
