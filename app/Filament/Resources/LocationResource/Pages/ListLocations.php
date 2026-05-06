<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    public function getTitle(): string 
    {
        return 'Locais da Obra';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie os diferentes locais e áreas operacionais onde o material será aplicado ou o serviço realizado.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Local')
                ->icon('heroicon-o-plus'),
        ];
    }
}
