<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Unidades dos campos da calculadora de volume. Toda fórmula trabalha em metros:
 * o valor digitado é convertido com toBase() antes de entrar na expressão, e
 * dimension() é a potência do comprimento usada na verificação de unidades
 * (m = 1, m² = 2, m³ = 3, contagem = 0).
 */
enum MeasureUnit: string implements HasLabel
{
    case M = 'm';
    case CM = 'cm';
    case MM = 'mm';
    case M2 = 'm2';
    case M3 = 'm3';
    case UNIT = 'un';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::M => 'Metros (m)',
            self::CM => 'Centímetros (cm)',
            self::MM => 'Milímetros (mm)',
            self::M2 => 'Metros quadrados (m²)',
            self::M3 => 'Metros cúbicos (m³)',
            self::UNIT => 'Quantidade (un)',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::M2 => 'm²',
            self::M3 => 'm³',
            default => $this->value,
        };
    }

    /** Fator que leva o valor digitado para a unidade base (m, m², m³ ou un) */
    public function factor(): float
    {
        return match ($this) {
            self::CM => 0.01,
            self::MM => 0.001,
            default => 1.0,
        };
    }

    public function toBase(float $value): float
    {
        return $value * $this->factor();
    }

    public function dimension(): int
    {
        return match ($this) {
            self::M, self::CM, self::MM => 1,
            self::M2 => 2,
            self::M3 => 3,
            self::UNIT => 0,
        };
    }

    /** Símbolo da unidade base, mostrado na memória de cálculo após a conversão */
    public function baseSymbol(): string
    {
        return match ($this->dimension()) {
            0 => 'un',
            1 => 'm',
            2 => 'm²',
            default => 'm³',
        };
    }

    public function isInteger(): bool
    {
        return $this === self::UNIT;
    }

    public static function dimensionLabel(float $dimension): string
    {
        return match (true) {
            abs($dimension) < 1e-9 => 'adimensional',
            abs($dimension - 1) < 1e-9 => 'm',
            abs($dimension - 2) < 1e-9 => 'm²',
            abs($dimension - 3) < 1e-9 => 'm³',
            default => 'm^'.rtrim(rtrim(number_format($dimension, 3, ',', ''), '0'), ','),
        };
    }
}
