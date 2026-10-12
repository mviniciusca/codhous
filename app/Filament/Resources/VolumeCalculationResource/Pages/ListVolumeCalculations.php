<?php

namespace App\Filament\Resources\VolumeCalculationResource\Pages;

use App\Filament\Clusters\ToolsCluster\Pages\VolumeCalculatorPage;
use App\Filament\Resources\VolumeCalculationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListVolumeCalculations extends ListRecords
{
    protected static string $resource = VolumeCalculationResource::class;

    public function getSubheading(): ?string
    {
        return 'Cálculos salvos pela equipe. Cada um guarda as medidas, a memória de cálculo e a versão da fórmula usada.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calculator')
                ->label('Abrir calculadora')
                ->icon('heroicon-o-cube')
                ->url(VolumeCalculatorPage::getUrl()),
        ];
    }
}
