<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudget extends EditRecord
{
    protected static string $resource = BudgetResource::class;

    public function getTitle(): string 
    {
        return 'Detalhes do Orçamento';
    }

    public function getSubheading(): ?string
    {
        return 'Revise as informações do cliente, defina os preços e gere o PDF para envio.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir'),
            Actions\ForceDeleteAction::make()
                ->label('Excluir Permanente'),
            Actions\RestoreAction::make()
                ->label('Restaurar'),
        ];
    }
}
