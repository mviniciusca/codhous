<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use App\Enums\ProductUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MachinerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Product::updateOrCreate(
            ['name' => 'Aluguel de Maquinário'],
            ['is_active' => true]
        );

        $machines = [
            [
                'name' => 'Alisadora de Piso (Bambolê) 36"',
                'price' => 150.00,
            ],
            [
                'name' => 'Vibrador de Imersão para Concreto (Mangote 5m)',
                'price' => 85.00,
            ],
            [
                'name' => 'Polidora de Piso Diamantada (Trifásica)',
                'price' => 350.00,
            ],
            [
                'name' => 'Cortadora de Piso (Serra de Juntas)',
                'price' => 180.00,
            ],
            [
                'name' => 'Bomba Portátil para Concreto',
                'price' => 1200.00,
            ],
            [
                'name' => 'Régua Vibratória de Alumínio 2m',
                'price' => 120.00,
            ],
        ];

        foreach ($machines as $machine) {
            ProductOption::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $machine['name'],
                ],
                [
                    'slug' => Str::slug($machine['name']),
                    'unit' => ProductUnit::DAY,
                    'price' => $machine['price'],
                ]
            );
        }
    }
}
