<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    public function getTitle(): string 
    {
        return 'Marcas e Parceiros';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie as logomarcas que aparecem no carrossel da página inicial do seu site.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nova Marca')
                ->icon('heroicon-o-plus'),
        ];
    }
}
