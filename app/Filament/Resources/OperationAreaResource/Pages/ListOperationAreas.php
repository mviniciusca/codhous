<?php

namespace App\Filament\Resources\OperationAreaResource\Pages;

use App\Filament\Resources\OperationAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperationAreas extends ListRecords
{
    protected static string $resource = OperationAreaResource::class;
    
    public function getTitle(): string
    {
        return 'Áreas de Operação';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie as regiões de atendimento e as faixas de CEP cobertas por sua operação.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                // use a supported icon to avoid SvgNotFound exception
                ->icon('heroicon-o-map')
                ->label('Nova Área de Operação'),
        ];
    }
}
