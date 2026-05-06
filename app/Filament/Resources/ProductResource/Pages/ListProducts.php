<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string 
    {
        return 'Produtos e Serviços';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie seu catálogo completo de materiais, locação de equipamentos e serviços especializados.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Produto ou Serviço')
                ->icon('heroicon-o-shopping-bag'),
        ];
    }
}
