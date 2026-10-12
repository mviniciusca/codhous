<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Cores de destaque de compromissos e notas. O valor é o tom do painel (cx-tone-*).
 */
enum WorkspaceColor: string implements HasColor, HasLabel
{
    case Gray = 'gray';
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
    case Purple = 'purple';

    public function getLabel(): string
    {
        return match ($this) {
            self::Gray => 'Cinza',
            self::Primary => 'Azul',
            self::Success => 'Verde',
            self::Warning => 'Amarelo',
            self::Danger => 'Vermelho',
            self::Purple => 'Roxo',
        };
    }

    public function getColor(): string
    {
        return $this->value;
    }
}
