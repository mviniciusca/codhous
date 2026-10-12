<?php

namespace App\Services\VolumeCalculator;

use App\Services\VolumeCalculator\Expression\Expression;

/**
 * Resultado de um cálculo: volume base, ajustes, volume final e a memória de
 * cálculo. toArray() é o que fica salvo no histórico.
 */
final class CalculationResult
{
    /**
     * @param  array<int, array{key: string, label: string, value: float, unit: string, base_value: float, base_unit: string}>  $inputs
     * @param  array<int, array{label: string, detail: string, value: float}>  $steps
     */
    public function __construct(
        public readonly float $baseVolume,
        public readonly float $marginPercent,
        public readonly float $marginVolume,
        public readonly float $volumeWithMargin,
        public readonly float $roundingAdjustment,
        public readonly float $finalVolume,
        public readonly array $inputs,
        public readonly array $steps,
        public readonly string $formula,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            baseVolume: (float) $data['base_volume'],
            marginPercent: (float) $data['margin_percent'],
            marginVolume: (float) $data['margin_volume'],
            volumeWithMargin: (float) $data['volume_with_margin'],
            roundingAdjustment: (float) $data['rounding_adjustment'],
            finalVolume: (float) $data['final_volume'],
            inputs: $data['inputs'] ?? [],
            steps: $data['steps'] ?? [],
            formula: (string) ($data['formula'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'base_volume' => $this->baseVolume,
            'margin_percent' => $this->marginPercent,
            'margin_volume' => $this->marginVolume,
            'volume_with_margin' => $this->volumeWithMargin,
            'rounding_adjustment' => $this->roundingAdjustment,
            'final_volume' => $this->finalVolume,
            'inputs' => $this->inputs,
            'steps' => $this->steps,
            'formula' => $this->formula,
        ];
    }

    /** “4,40 m³” */
    public static function formatVolume(float $volume, int $decimals = 2): string
    {
        return number_format($volume, $decimals, ',', '.').' m³';
    }

    public static function formatNumber(float $value): string
    {
        return Expression::formatNumber($value);
    }
}
