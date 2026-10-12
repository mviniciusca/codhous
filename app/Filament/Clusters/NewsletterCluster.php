<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class NewsletterCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Website';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Newsletter';
    }
}
