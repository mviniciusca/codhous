<?php

namespace App\Services;

class BudgetCalculatorService
{
    /**
     * Calcula o subtotal de um item (quantidade x preço)
     *
     * @param  int|float  $quantity
     */
    public static function calculateItemSubtotal($quantity, float $price): string
    {
        // Concreto é vendido em m³ fracionado (ex: 2,5 m³), então não arredondamos
        $quantity = floatval($quantity);
        $subtotal = $quantity * $price;

        return number_format($subtotal, 2, '.', '');
    }

    /**
     * Calcula o total geral do orçamento
     *
     * @param  array  $products  Array de produtos com quantity e price
     * @param  float  $shipping  Custo de frete
     * @param  float  $tax  Taxa ou impostos
     * @param  float  $discount  Desconto
     * @return array ['subtotal' => string, 'quantity' => float, 'price' => string, 'total' => string]
     */
    public static function calculateTotal(array $products, float $shipping = 0, float $tax = 0, float $discount = 0): array
    {
        $subtotal = 0;
        $totalQuantity = 0;

        // Calcular subtotais de cada produto
        foreach ($products as $product) {
            $productQuantity = floatval($product['quantity'] ?? 0);
            $productPrice = floatval($product['price'] ?? 0);
            $itemSubtotal = $productQuantity * $productPrice;

            $subtotal += $itemSubtotal;
            $totalQuantity += $productQuantity;
        }

        // Aplicar shipping, taxas e descontos
        $total = $subtotal + $shipping + $tax - $discount;

        // Calcular preço médio por unidade
        $averagePrice = ($totalQuantity > 0) ? ($subtotal / $totalQuantity) : 0;

        return [
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'quantity' => $totalQuantity,
            'price' => number_format($averagePrice, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
        ];
    }

    /**
     * Calcula os subtotais de todos os produtos e retorna o array atualizado
     */
    public static function calculateProductSubtotals(array $products): array
    {
        foreach ($products as $index => $product) {
            $productQuantity = floatval($product['quantity'] ?? 0);
            $productPrice = floatval($product['price'] ?? 0);
            $itemSubtotal = $productQuantity * $productPrice;

            $products[$index]['subtotal'] = number_format($itemSubtotal, 2, '.', '');
        }

        return $products;
    }

    /**
     * Formata um valor numérico para moeda (2 casas decimais)
     */
    public static function formatCurrency(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    /**
     * Formata uma quantidade no padrão brasileiro, sem zeros desnecessários (ex: 2,5 / 3 / 1.250)
     */
    public static function formatQuantity(float $quantity): string
    {
        $formatted = number_format($quantity, 2, ',', '.');

        return str_contains($formatted, ',') ? rtrim(rtrim($formatted, '0'), ',') : $formatted;
    }

    /**
     * Quantidade com unidade, no plural quando for o caso (ex: "2,5 m³", "1 dia", "3 dias", "2 meses")
     */
    public static function formatQuantityWithUnit(float $quantity, ?string $unit): string
    {
        $formatted = self::formatQuantity($quantity);

        if (blank($unit)) {
            return $formatted;
        }

        $plural = [
            'dia' => 'dias',
            'hora' => 'horas',
            'semana' => 'semanas',
            'mes' => 'meses',
            'unidade' => 'unidades',
        ];

        $label = $unit === 'mes' ? 'mês' : $unit;
        if ($quantity > 1 && isset($plural[$unit])) {
            $label = $plural[$unit];
        }

        return $formatted.' '.$label;
    }

    /**
     * Calcula o total a partir de valores individuais
     */
    public static function calculateTotalFromValues(float $subtotal, float $shipping = 0, float $tax = 0, float $discount = 0): string
    {
        $total = $subtotal + $shipping + $tax - $discount;

        return self::formatCurrency($total);
    }
}
