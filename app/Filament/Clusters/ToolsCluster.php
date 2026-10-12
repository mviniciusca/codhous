<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

/** Ferramentas do dia a dia: calculadora de volume, fórmulas e histórico de cálculos */
class ToolsCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $activeNavigationIcon = 'heroicon-s-squares-plus';

    protected static ?string $navigationGroup = 'Empresa';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'ferramentas';

    public static function getNavigationLabel(): string
    {
        return 'Ferramentas';
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return 'Ferramentas';
    }
}
