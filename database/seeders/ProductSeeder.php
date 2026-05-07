<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use App\Enums\ProductUnit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa dados existentes
        Product::query()->delete();
        ProductOption::query()->delete();

        // 1. Concreto Bombeado
        $concreto = Product::create([
            'name' => 'Concreto Bombeado',
            'description' => 'Concreto usinado com serviço de bombeamento incluso para lajes, vigas e colunas.',
            'is_active' => true,
        ]);

        $this->seedOptions($concreto, [
            ['name' => 'FCK 20 (Brita 0 e 1)', 'price' => 385.00, 'unit' => ProductUnit::M3],
            ['name' => 'FCK 25 (Brita 0 e 1)', 'price' => 415.00, 'unit' => ProductUnit::M3],
            ['name' => 'FCK 30 (Brita 0 e 1)', 'price' => 445.00, 'unit' => ProductUnit::M3],
            ['name' => 'FCK 35 (Alta Resistência)', 'price' => 490.00, 'unit' => ProductUnit::M3],
        ]);

        // 2. Polimento de Piso
        $polimento = Product::create([
            'name' => 'Polimento de Piso',
            'description' => 'Serviço profissional de acabamento e lapidação de pisos industriais e comerciais.',
            'is_active' => true,
        ]);

        $this->seedOptions($polimento, [
            ['name' => 'Polimento Básico (Nivelamento)', 'price' => 28.00, 'unit' => ProductUnit::M2],
            ['name' => 'Polimento Espelhado (Alto Brilho)', 'price' => 48.00, 'unit' => ProductUnit::M2],
            ['name' => 'Lapidação com Endurecedor Químico', 'price' => 65.00, 'unit' => ProductUnit::M2],
        ]);

        // 3. Aluguel de Maquinário
        $maquinario = Product::create([
            'name' => 'Aluguel de Maquinário',
            'description' => 'Locação de equipamentos para construção civil com manutenção inclusa.',
            'is_active' => true,
        ]);

        $this->seedOptions($maquinario, [
            ['name' => 'Placa Vibratória (Gasolina)', 'price' => 120.00, 'unit' => ProductUnit::DAY],
            ['name' => 'Cortadora de Piso (Climber)', 'price' => 180.00, 'unit' => ProductUnit::DAY],
            ['name' => 'Betoneira 400L (Elétrica)', 'price' => 85.00, 'unit' => ProductUnit::DAY],
            ['name' => 'Compactador de Solo (Sapo)', 'price' => 150.00, 'unit' => ProductUnit::DAY],
        ]);
    }

    private function seedOptions(Product $product, array $options): void
    {
        foreach ($options as $option) {
            ProductOption::create([
                'product_id' => $product->id,
                'name' => $option['name'],
                'price' => $option['price'],
                'unit' => $option['unit'],
                'is_active' => true,
            ]);
        }
    }
}
