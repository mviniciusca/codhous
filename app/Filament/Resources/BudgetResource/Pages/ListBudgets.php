<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    public function getTitle(): string 
    {
        return 'Orçamentos Recebidos';
    }

    public function getSubheading(): ?string
    {
        return 'Acompanhe e gerencie todos os pedidos de orçamento enviados pelos clientes através do site.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Orçamento')
                ->icon('heroicon-o-plus'),
        ];
    }
}
