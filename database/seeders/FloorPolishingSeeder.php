<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use App\Enums\ProductUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FloorPolishingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Product::updateOrCreate(
            ['name' => 'Polimento de Piso'],
            ['is_active' => true]
        );

        $options = [
            [
                'name' => 'Polimento Industrial Espelhado',
                'price' => 45.00,
                'unit' => ProductUnit::M2,
            ],
            [
                'name' => 'Polimento com Lapidação',
                'price' => 65.00,
                'unit' => ProductUnit::M2,
            ],
        ];

        foreach ($options as $option) {
            ProductOption::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $option['name'],
                ],
                [
                    'slug' => Str::slug($option['name']),
                    'unit' => $option['unit'],
                    'price' => $option['price'],
                ]
            );
        }
    }
}
