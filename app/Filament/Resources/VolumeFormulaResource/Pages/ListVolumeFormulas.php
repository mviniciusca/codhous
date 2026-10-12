<?php

namespace App\Filament\Resources\VolumeFormulaResource\Pages;

use App\Filament\Resources\VolumeFormulaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVolumeFormulas extends ListRecords
{
    protected static string $resource = VolumeFormulaResource::class;

    public function getSubheading(): ?string
    {
        return 'Regras de cálculo usadas pela calculadora de volume. Cada alteração vira uma nova versão, e os cálculos salvos guardam a versão usada.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nova fórmula')->icon('heroicon-o-plus'),
        ];
    }
}
