<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        return 'Dados do produto, variações e preços.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\DeleteAction::make()
                    ->label('Mover para a lixeira')
                    ->modalDescription('Some do site e de novos orçamentos. Orçamentos antigos não são afetados.'),
                Actions\RestoreAction::make()
                    ->label('Restaurar'),
                Actions\ForceDeleteAction::make()
                    ->label('Excluir definitivamente')
                    ->visible(fn (Product $record) => $record->trashed() && ! $record->isUsedInBudgets()),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->tooltip('Mais ações'),
        ];
    }
}
