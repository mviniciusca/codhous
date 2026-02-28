<?php

namespace App\Services;

class BudgetCalculatorService
{
    /**
     * Calcula o subtotal de um item (quantidade x preço)
     *
     * @param int|float $quantity
     * @param float $price
     * @return string
     */
    public static function calculateItemSubtotal($quantity, float $price): string
    {
        $quantity = intval($quantity);
        $subtotal = $quantity * $price;

        return number_format($subtotal, 2, '.', '');
    }

    /**
     * Calcula o total geral do orçamento
     *
     * @param array $products Array de produtos com quantity e price
     * @param float $shipping Custo de frete
     * @param float $tax Taxa ou impostos
     * @param float $discount Desconto
     * @return array ['subtotal' => float, 'quantity' => int, 'total' => string]
     */
    public static function calculateTotal(array $products, float $shipping = 0, float $tax = 0, float $discount = 0): array
    {
        $subtotal = 0;
        $totalQuantity = 0;

        // Calcular subtotais de cada produto
        foreach ($products as $product) {
            $productQuantity = intval($product['quantity'] ?? 0);
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
            'subtotal'      => number_format($subtotal, 2, '.', ''),
            'quantity'      => $totalQuantity,
            'price'         => number_format($averagePrice, 2, '.', ''),
            'total'         => number_format($total, 2, '.', ''),
        ];
    }

    /**
     * Calcula os subtotais de todos os produtos e retorna o array atualizado
     *
     * @param array $products
     * @return array
     */
    public static function calculateProductSubtotals(array $products): array
    {
        foreach ($products as $index => $product) {
            $productQuantity = intval($product['quantity'] ?? 0);
            $productPrice = floatval($product['price'] ?? 0);
            $itemSubtotal = $productQuantity * $productPrice;

            $products[$index]['subtotal'] = number_format($itemSubtotal, 2, '.', '');
        }

        return $products;
    }

    /**
     * Formata um valor numérico para moeda (2 casas decimais)
     *
     * @param float $value
     * @return string
     */
    public static function formatCurrency(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    /**
     * Calcula o total a partir de valores individuais
     *
     * @param float $subtotal
     * @param float $shipping
     * @param float $tax
     * @param float $discount
     * @return string
     */
    public static function calculateTotalFromValues(float $subtotal, float $shipping = 0, float $tax = 0, float $discount = 0): string
    {
        $total = $subtotal + $shipping + $tax - $discount;

        return self::formatCurrency($total);
    }
}
