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
        return 'Catálogo e tabela de preços usados no formulário do site e nos orçamentos.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo produto')
                ->icon('heroicon-o-plus'),
        ];
    }
}
