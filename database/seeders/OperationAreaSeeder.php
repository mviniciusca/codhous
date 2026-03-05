<?php

namespace Database\Seeders;

use App\Models\OperationArea;
use Illuminate\Database\Seeder;

class OperationAreaSeeder extends Seeder
{
    /**
     * Uma entrada por cidade. Quando postcode_start e postcode_end estão preenchidos,
     * o CEP é considerado na área se estiver na faixa (ex: Rio 20000-23999 cobre Barra da Tijuca 22xxx).
     */
    public function run(): void
    {
        $areas = [
            ['city' => 'Rio de Janeiro', 'state' => 'RJ', 'postcode_prefix' => '20000', 'postcode_start' => '20000', 'postcode_end' => '23999', 'shipping_fee' => 0, 'is_base' => false],
            ['city' => 'Niterói', 'state' => 'RJ', 'postcode_prefix' => '24000', 'postcode_start' => '24000', 'postcode_end' => '24399', 'shipping_fee' => 80.00, 'is_base' => false],
            ['city' => 'São Gonçalo', 'state' => 'RJ', 'postcode_prefix' => '24400', 'postcode_start' => '24400', 'postcode_end' => '24799', 'shipping_fee' => 120.00, 'is_base' => false],
            ['city' => 'Duque de Caxias', 'state' => 'RJ', 'postcode_prefix' => '25000', 'postcode_start' => '25000', 'postcode_end' => '25299', 'shipping_fee' => 0, 'is_base' => true],
            ['city' => 'São João de Meriti', 'state' => 'RJ', 'postcode_prefix' => '25500', 'postcode_start' => '25500', 'postcode_end' => '25699', 'shipping_fee' => 50.00, 'is_base' => false],
            ['city' => 'Magé', 'state' => 'RJ', 'postcode_prefix' => '25900', 'postcode_start' => '25900', 'postcode_end' => '25999', 'shipping_fee' => 90.00, 'is_base' => false],
            ['city' => 'Nova Iguaçu', 'state' => 'RJ', 'postcode_prefix' => '26000', 'postcode_start' => '26000', 'postcode_end' => '26399', 'shipping_fee' => 70.00, 'is_base' => false],
            ['city' => 'Belford Roxo', 'state' => 'RJ', 'postcode_prefix' => '26100', 'postcode_start' => '26100', 'postcode_end' => '26299', 'shipping_fee' => 60.00, 'is_base' => false],
            ['city' => 'Nilópolis', 'state' => 'RJ', 'postcode_prefix' => '26500', 'postcode_start' => '26500', 'postcode_end' => '26599', 'shipping_fee' => 65.00, 'is_base' => false],
            ['city' => 'Mesquita', 'state' => 'RJ', 'postcode_prefix' => '26550', 'postcode_start' => '26550', 'postcode_end' => '26599', 'shipping_fee' => 60.00, 'is_base' => false],
            ['city' => 'Queimados', 'state' => 'RJ', 'postcode_prefix' => '26320', 'postcode_start' => '26320', 'postcode_end' => '26399', 'shipping_fee' => 85.00, 'is_base' => false],
            ['city' => 'Itaguaí', 'state' => 'RJ', 'postcode_prefix' => '23800', 'postcode_start' => '23800', 'postcode_end' => '23899', 'shipping_fee' => 150.00, 'is_base' => false],
            ['city' => 'Maricá', 'state' => 'RJ', 'postcode_prefix' => '24900', 'postcode_start' => '24900', 'postcode_end' => '24999', 'shipping_fee' => 130.00, 'is_base' => false],
            ['city' => 'Guapimirim', 'state' => 'RJ', 'postcode_prefix' => '25940', 'postcode_start' => '25940', 'postcode_end' => '25949', 'shipping_fee' => 100.00, 'is_base' => false],
        ];

        foreach ($areas as $area) {
            OperationArea::updateOrCreate(
                ['postcode_prefix' => $area['postcode_prefix']],
                array_merge($area, ['is_active' => true])
            );
        }
    }
}
