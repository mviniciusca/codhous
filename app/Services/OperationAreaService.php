<?php

namespace App\Services;

use App\Models\OperationArea;

class OperationAreaService
{
    /**
     * Verifica se o CEP está na área de operação (faixa por cidade).
     */
    public static function isCepInOperationArea(string $cep): bool
    {
        return OperationArea::isCepInOperationArea($cep);
    }

    /**
     * Retorna a área de operação para o CEP, ou null se fora da área.
     */
    public static function findAreaByCep(string $cep): ?OperationArea
    {
        return OperationArea::findOperationAreaByCep($cep);
    }

    /**
     * Retorna o valor do frete para o CEP com base na área de operação.
     * Retorna null se o CEP estiver fora da área.
     */
    public static function getShippingFeeForCep(string $cep): ?float
    {
        $area = OperationArea::findOperationAreaByCep($cep);

        return $area !== null ? (float) $area->shipping_fee : null;
    }

    /**
     * Resultado da consulta: dentro da área (com frete) ou fora.
     */
    public static function resultForCep(string $cep): array
    {
        $area = OperationArea::findOperationAreaByCep($cep);

        if ($area === null) {
            return [
                'in_area' => false,
                'shipping_fee' => null,
                'city' => null,
                'message' => __('Este CEP está fora da nossa área de atendimento. No momento atendemos apenas a região do Rio de Janeiro e Grande Rio.'),
            ];
        }

        return [
            'in_area' => true,
            'shipping_fee' => (float) $area->shipping_fee,
            'city' => $area->city,
            'message' => $area->shipping_fee > 0
                ? __('Frete para :city: :value', ['city' => $area->city, 'value' => env('CURRENCY_SUFFIX', 'R$') . ' ' . number_format($area->shipping_fee, 2, ',', '.')])
                : __('Entrega em :city sem custo adicional.', ['city' => $area->city]),
        ];
    }
}
