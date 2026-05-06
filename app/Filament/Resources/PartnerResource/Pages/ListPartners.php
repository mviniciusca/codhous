<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Resources\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;

    public function getTitle(): string 
    {
        return 'Parceiros e Empresas';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie as empresas parceiras e prestadores de serviço cadastrados no sistema.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Parceiro')
                ->icon('heroicon-o-plus'),
        ];
    }
}
