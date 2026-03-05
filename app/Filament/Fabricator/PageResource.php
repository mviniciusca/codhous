<?php

namespace App\Filament\Fabricator;

use Z3d0X\FilamentFabricator\Resources\PageResource as FabricatorPageResource;

class PageResource extends FabricatorPageResource
{
    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Páginas';
}
