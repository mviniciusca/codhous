<?php

namespace App\Filament\Clusters\ToolsCluster\Pages;

use App\Enums\AccessArea;
use App\Filament\Clusters\ToolsCluster;
use App\Filament\Concerns\RequiresArea;
use App\Filament\Resources\VolumeCalculationResource;
use App\Filament\Resources\VolumeFormulaResource;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;

/** Página da calculadora de volume; o mesmo componente abre em modal pela topbar */
class VolumeCalculatorPage extends Page
{
    use RequiresArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Comercial;
    }

    protected static ?string $cluster = ToolsCluster::class;

    /** Submenu do cluster em abas no topo: a calculadora precisa da largura toda */
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $slug = 'calculadora-de-volume';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Calculadora de volume';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Calculadora de volume';

    protected static string $view = 'filament.clusters.tools-cluster.pages.volume-calculator';

    public function getSubheading(): ?string
    {
        return 'Volume de concreto de lajes, pisos, pilares e estacas, com margem de segurança e memória de cálculo. As fórmulas são as configuradas pela empresa.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('formulas')
                ->label('Fórmulas')
                ->icon('heroicon-o-variable')
                ->color('gray')
                ->visible(fn () => VolumeFormulaResource::canViewAny())
                ->url(fn () => VolumeFormulaResource::getUrl()),
            Action::make('history')
                ->label('Histórico de cálculos')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->visible(fn () => VolumeCalculationResource::canViewAny())
                ->url(fn () => VolumeCalculationResource::getUrl()),
        ];
    }
}
