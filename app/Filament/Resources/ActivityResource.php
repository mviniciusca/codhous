<?php

namespace App\Filament\Resources;

use Z3d0X\FilamentLogger\Resources\ActivityResource as BaseActivityResource;

class ActivityResource extends BaseActivityResource
{
    public static function getNavigationGroup(): ?string
    {
        return 'Configurações';
    }

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Registro de Atividade';
    }

    public static function getModelLabel(): string
    {
        return 'Atividade';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Registro de Atividades';
    }
}
