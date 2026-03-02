<?php

namespace Database\Seeders;

use App\Models\OperationArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperationAreaSeeder extends Seeder
{
    public function run(): void
    {
        // Lista de prefixos (5 primeiros dígitos) das cidades do Grande Rio
        // todas estão no estado do Rio (RJ)
        $areas = [
            ['city' => 'Rio de Janeiro (Capital)', 'state' => 'RJ', 'postcode_prefix' => '20000', 'shipping_fee' => 250.00],
            ['city' => 'Duque de Caxias', 'state' => 'RJ', 'postcode_prefix' => '25000', 'shipping_fee' => 100.00, 'is_base' => true],
            ['city' => 'Nova Iguaçu', 'state' => 'RJ', 'postcode_prefix' => '26000', 'shipping_fee' => 180.00],
            ['city' => 'São João de Meriti', 'state' => 'RJ', 'postcode_prefix' => '25500', 'shipping_fee' => 150.00],
            ['city' => 'Nilópolis', 'state' => 'RJ', 'postcode_prefix' => '26500', 'shipping_fee' => 160.00],
            ['city' => 'Belford Roxo', 'state' => 'RJ', 'postcode_prefix' => '26100', 'shipping_fee' => 170.00],
            ['city' => 'Mesquita', 'state' => 'RJ', 'postcode_prefix' => '26550', 'shipping_fee' => 160.00],
            ['city' => 'Niterói', 'state' => 'RJ', 'postcode_prefix' => '24000', 'shipping_fee' => 300.00],
            ['city' => 'São Gonçalo', 'state' => 'RJ', 'postcode_prefix' => '24400', 'shipping_fee' => 320.00],
            ['city' => 'Magé', 'state' => 'RJ', 'postcode_prefix' => '25900', 'shipping_fee' => 200.00],
        ];

        foreach ($areas as $area) {
            OperationArea::updateOrCreate(
                ['postcode_prefix' => $area['postcode_prefix']],
                array_merge($area, ['is_active' => true])
            );
        }
    }
}
