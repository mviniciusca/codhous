<?php

namespace App\Filament\Resources\ShowcaseResource\Pages;

use App\Filament\Resources\ShowcaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShowcases extends ListRecords
{
    protected static string $resource = ShowcaseResource::class;

    public function getTitle(): string 
    {
        return 'Nossas Obras';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie o portfólio de obras concluídas para exibir a qualidade e agilidade dos seus serviços aos clientes.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nova Obra')
                ->icon('heroicon-o-plus'),
        ];
    }
}
