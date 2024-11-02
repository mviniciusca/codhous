<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('new_budget')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar')
                ->label(__('New Budget')),
            Action::make('view_trash')
                ->color('gray')
                ->icon('heroicon-o-trash')
                ->url(route('filament.admin.resources.budgets.bin'))
                ->label(__(false)),
        ];
    }
}
