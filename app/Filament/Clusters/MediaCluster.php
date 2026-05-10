<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\SubNavigationPosition;

class MediaCluster extends Cluster
{
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Mídias';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'midias';

    public static function getNavigationLabel(): string
    {
        return 'Mídias';
    }
}
