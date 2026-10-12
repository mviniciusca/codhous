<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * Prioridade das tarefas do Meu Workspace.
 */
enum WorkspacePriority: string implements HasColor, HasIcon, HasLabel
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function getLabel(): string
    {
        return match ($this) {
            self::High => 'Alta',
            self::Medium => 'Média',
            self::Low => 'Baixa',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::High => 'danger',
            self::Medium => 'warning',
            self::Low => 'primary',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::High => 'heroicon-m-exclamation-circle',
            self::Medium => 'heroicon-m-minus-circle',
            self::Low => 'heroicon-m-arrow-down-circle',
        };
    }

    /** Ordem na lista: alta primeiro */
    public function weight(): int
    {
        return match ($this) {
            self::High => 0,
            self::Medium => 1,
            self::Low => 2,
        };
    }
}
