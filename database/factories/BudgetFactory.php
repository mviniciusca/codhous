<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product_id = $this->faker->randomElement([1, 2, 3, 4, 5]);
        $product_option_id = $this->faker->numberBetween(1, 15);
        $quantity = $this->faker->randomElement([5, 10, 15, 20]);
        $price = $this->faker->randomFloat(2, 100, 1000);
        $subtotal = $quantity * $price;

        return [
            'created_at' => $this->faker->dateTimeBetween(date('2024-01-01')),
            'code'       => $this->faker->numerify('####'),
            'is_active'  => $this->faker->boolean(),
            'status'     => $this->faker->randomElement([
                'on going',
                'pending',
                'ignored',
                'done',
            ]),
            'content' => [
                'quantity'       => (string) $quantity,
                'product'        => (string) $product_id,
                'product_option' => (string) $product_option_id,
                'location'       => (string) $this->faker->randomElement([1, 2, 3]),
                'postcode'       => $this->faker->numerify('22###-###'),
                'customer_name'  => $this->faker->name(),
                'customer_email' => $this->faker->email(),
                'customer_phone' => $this->faker->phoneNumber(),
                'street'         => $this->faker->streetAddress(),
                'number'         => (string) $this->faker->randomNumber(),
                'city'           => $this->faker->city(),
                'neighborhood'   => $this->faker->city(),
                'state'          => $this->faker->countryCode(),
                'tax'            => (string) $this->faker->randomElement([10, 15, 20]),
                'price'          => (string) $price,
                'total'          => (string) $subtotal,
                'discount'       => (string) $this->faker->randomElement([5, 10, 15]),
                'products'       => [
                    [
                        'product'        => $product_id,
                        'product_option' => $product_option_id,
                        'location'       => $this->faker->randomElement([1, 2, 3]),
                        'quantity'       => $quantity,
                        'price'          => $price,
                        'subtotal'       => $subtotal,
                    ],
                ],
            ],
        ];
    }
}
