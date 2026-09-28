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
        // Buscar um produto aleatório do banco de dados
        $product = \App\Models\Product::inRandomOrder()->first();

        // Se não houver produtos, usar IDs padrão
        if (! $product) {
            $product_id = $this->faker->randomElement([1, 2, 3, 4, 5]);
            $product_option_id = $this->faker->numberBetween(1, 15);
            $price = $this->faker->randomFloat(2, 100, 1000);
        } else {
            $product_id = $product->id;

            // Buscar uma opção aleatória que pertence a este produto
            $productOption = $product->productOption()->inRandomOrder()->first();

            // Se não houver opção, criar valores padrão
            if (! $productOption) {
                $product_option_id = $this->faker->numberBetween(1, 15);
                $price = $this->faker->randomFloat(2, 100, 1000);
            } else {
                $product_option_id = $productOption->id;
                $price = $productOption->price;
            }
        }

        $quantity = $this->faker->randomElement([3, 4, 5]); // Quantidade aleatória: 3, 4 ou 5
        $subtotal = $quantity * $price;

        // Valores adicionais
        $shipping = $this->faker->randomFloat(2, 10, 50);
        $tax = $this->faker->randomElement([10, 15, 20]);
        $discount = $this->faker->randomElement([5, 10, 15]);

        // Cálculo do total: subtotal + shipping + tax - discount
        $total = $subtotal + $shipping + $tax - $discount;

        // Gera o código usando o mesmo padrão do modelo
        $code = 'BD'.date('Ym').str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);

        $address = $this->faker->randomElement([
            ['postcode' => '20040-002', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'neighborhood' => 'Centro', 'street' => 'Avenida Rio Branco'],
            ['postcode' => '22041-001', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'neighborhood' => 'Copacabana', 'street' => 'Rua Barata Ribeiro'],
            ['postcode' => '22793-081', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'neighborhood' => 'Barra da Tijuca', 'street' => 'Avenida das Américas'],
            ['postcode' => '24220-008', 'city' => 'Niterói', 'state' => 'RJ', 'neighborhood' => 'Icaraí', 'street' => 'Praia de Icaraí'],
            ['postcode' => '25010-007', 'city' => 'Duque de Caxias', 'state' => 'RJ', 'neighborhood' => 'Centro', 'street' => 'Avenida Presidente Kennedy'],
            ['postcode' => '26210-210', 'city' => 'Nova Iguaçu', 'state' => 'RJ', 'neighborhood' => 'Centro', 'street' => 'Via Light'],
            ['postcode' => '24440-420', 'city' => 'São Gonçalo', 'state' => 'RJ', 'neighborhood' => 'Centro', 'street' => 'Rua Doutor Nilo Peçanha'],
            ['postcode' => '21040-361', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'neighborhood' => 'Bonsucesso', 'street' => 'Avenida Brasil'],
        ]);

        return [
            'created_at' => $this->faker->dateTimeBetween(date('2024-01-01')),
            'code'       => $code,
            'is_active'  => $this->faker->boolean(),
            'status'     => $this->faker->randomElement([
                'on going',
                'pending',
                'ignored',
                'done',
            ]),
            'content' => [
                'quantity'       => (int) $quantity,
                'product'        => (string) $product_id,
                'product_option' => (string) $product_option_id,
                'location'       => (string) $this->faker->randomElement([1, 2, 3]),
                'postcode'       => $address['postcode'],
                'customer_name'  => $this->faker->name(),
                'customer_email' => $this->faker->email(),
                'customer_phone' => $this->faker->phoneNumber(),
                'street'         => $address['street'],
                'number'         => (string) $this->faker->randomNumber(3),
                'city'           => $address['city'],
                'neighborhood'   => $address['neighborhood'],
                'state'          => $address['state'],
                'shipping'       => (string) $shipping,
                'tax'            => (string) $tax,
                'price'          => (string) $price,
                'total'          => (string) number_format($total, 2, '.', ''),
                'discount'       => (string) $discount,
                'products'       => [
                    [
                        'product'        => $product_id,
                        'product_option' => $product_option_id,
                        'location'       => $this->faker->randomElement([1, 2, 3]),
                        'quantity'       => (int) $quantity,
                        'price'          => $price,
                        'subtotal'       => $subtotal,
                    ],
                ],
            ],
        ];
    }
}
