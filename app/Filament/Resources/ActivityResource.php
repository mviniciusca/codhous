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
}
