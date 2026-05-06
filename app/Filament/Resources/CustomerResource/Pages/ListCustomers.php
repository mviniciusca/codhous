<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    public function getTitle(): string 
    {
        return 'Lista de Clientes';
    }

    public function getSubheading(): ?string
    {
        return 'Visualize e gerencie todos os clientes cadastrados que solicitaram orçamentos ou serviços.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Cliente')
                ->icon('heroicon-o-plus'),
        ];
    }
}
