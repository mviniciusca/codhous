<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** Regra de arredondamento do volume final da calculadora (aplicada depois da margem) */
enum VolumeRounding: string implements HasLabel
{
    case None = 'none';
    case Up = 'up';
    case Nearest = 'nearest';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::None => 'Sem arredondamento (2 casas)',
            self::Up => 'Para cima, em múltiplos do passo',
            self::Nearest => 'Ao múltiplo mais próximo do passo',
        };
    }

    public function apply(float $volume, float $step): float
    {
        // Corta o ruído de ponto flutuante antes do ceil (4,4000000001 não vira 4,5)
        $volume = round($volume, 9);

        if ($this === self::None || $step <= 0) {
            return round($volume, 2);
        }

        $multiples = round($volume / $step, 9);

        return round(($this === self::Up ? ceil($multiples) : round($multiples)) * $step, 6);
    }
}
