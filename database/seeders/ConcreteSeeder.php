<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use App\Enums\ProductUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConcreteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Product::updateOrCreate(
            ['name' => 'Concreto Bombeado'],
            ['is_active' => true]
        );

        $concreteTypes = [
            ['fck' => 'FCK 20', 'price' => 380.00],
            ['fck' => 'FCK 25', 'price' => 410.00],
            ['fck' => 'FCK 30', 'price' => 440.00],
            ['fck' => 'FCK 35', 'price' => 480.00],
            ['fck' => 'FCK 40', 'price' => 530.00],
        ];

        foreach ($concreteTypes as $type) {
            ProductOption::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $type['fck'],
                ],
                [
                    'slug' => Str::slug($type['fck']),
                    'unit' => ProductUnit::M3,
                    'price' => $type['price'],
                ]
            );
        }
    }
}
