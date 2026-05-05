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
            self::M3 => 'M³',
            self::KG => 'Kg',
            self::M2 => 'M²',
            self::UNIT => 'Unidade',
            self::DAY => 'Dia',
            self::HOUR => 'Hora',
            self::WEEK => 'Semana',
            self::MONTH => 'Mês',
        };
    }
}
