<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductUnit: string implements HasLabel
{
    case M3 = 'm³';
    case KG = 'kg';
    case M2 = 'm²';
    case UNIT = 'unidade';
    case DAY = 'dia';
    case HOUR = 'hora';
    case WEEK = 'semana';
    case MONTH = 'mes';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::M3 => 'Metro cúbico (m³)',
            self::KG => 'Quilo (kg)',
            self::M2 => 'Metro quadrado (m²)',
            self::UNIT => 'Unidade',
            self::DAY => 'Diária',
            self::HOUR => 'Hora',
            self::WEEK => 'Semana',
            self::MONTH => 'Mês',
        };
    }

    /**
     * Abreviação usada ao lado de quantidades e preços (ex: "R$ 415,00/m³").
     */
    public function short(): string
    {
        return match ($this) {
            self::MONTH => 'mês',
            default => $this->value,
        };
    }

    public function isDecimal(): bool
    {
        return match ($this) {
            self::M3, self::KG, self::M2 => true,
            default => false,
        };
    }
}
