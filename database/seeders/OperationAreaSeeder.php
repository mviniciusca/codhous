<?php

namespace Database\Seeders;

use App\Models\OperationArea;
use Illuminate\Database\Seeder;

class OperationAreaSeeder extends Seeder
{
    /**
     * Uma entrada por cidade: um único postcode_prefix (5 dígitos) que representa a cidade.
     * A verificação no site usa os 2 primeiros dígitos do CEP: ex. 25000 = opera em Duque de Caxias (todo 25xxx).
     */
    public function run(): void
    {
        $areas = [
            ['city' => 'Rio de Janeiro', 'state' => 'RJ', 'postcode_prefix' => '20000', 'shipping_fee' => 0, 'is_base' => false],
            ['city' => 'Niterói', 'state' => 'RJ', 'postcode_prefix' => '24000', 'shipping_fee' => 80.00, 'is_base' => false],
            ['city' => 'São Gonçalo', 'state' => 'RJ', 'postcode_prefix' => '24400', 'shipping_fee' => 120.00, 'is_base' => false],
            ['city' => 'Duque de Caxias', 'state' => 'RJ', 'postcode_prefix' => '25000', 'shipping_fee' => 0, 'is_base' => true],
            ['city' => 'São João de Meriti', 'state' => 'RJ', 'postcode_prefix' => '25500', 'shipping_fee' => 50.00, 'is_base' => false],
            ['city' => 'Magé', 'state' => 'RJ', 'postcode_prefix' => '25900', 'shipping_fee' => 90.00, 'is_base' => false],
            ['city' => 'Nova Iguaçu', 'state' => 'RJ', 'postcode_prefix' => '26000', 'shipping_fee' => 70.00, 'is_base' => false],
            ['city' => 'Belford Roxo', 'state' => 'RJ', 'postcode_prefix' => '26100', 'shipping_fee' => 60.00, 'is_base' => false],
            ['city' => 'Nilópolis', 'state' => 'RJ', 'postcode_prefix' => '26500', 'shipping_fee' => 65.00, 'is_base' => false],
            ['city' => 'Mesquita', 'state' => 'RJ', 'postcode_prefix' => '26550', 'shipping_fee' => 60.00, 'is_base' => false],
            ['city' => 'Queimados', 'state' => 'RJ', 'postcode_prefix' => '26320', 'shipping_fee' => 85.00, 'is_base' => false],
            ['city' => 'Itaguaí', 'state' => 'RJ', 'postcode_prefix' => '23800', 'shipping_fee' => 150.00, 'is_base' => false],
            ['city' => 'Maricá', 'state' => 'RJ', 'postcode_prefix' => '24900', 'shipping_fee' => 130.00, 'is_base' => false],
            ['city' => 'Guapimirim', 'state' => 'RJ', 'postcode_prefix' => '25940', 'shipping_fee' => 100.00, 'is_base' => false],
        ];

        foreach ($areas as $area) {
            OperationArea::updateOrCreate(
                ['postcode_prefix' => $area['postcode_prefix']],
                array_merge($area, ['is_active' => true])
            );
        }
    }
}
