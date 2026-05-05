<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string 
    {
        return 'Editar Produto ou Serviço';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir'),
            Actions\RestoreAction::make()
                ->label('Restaurar'),
            Actions\ForceDeleteAction::make()
                ->label('Excluir Permanentemente'),
        ];
    }
}
