<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Budget;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class BudgetWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    public function table(Table $table): Table
    {
        return $table
            ->description(__('Quick view in your pending budgets here.'))
            ->headerActions([
                Action::make('edit')
                    ->label(__('Budgets'))
                    ->icon('heroicon-o-currency-dollar')
                    ->url(route('filament.admin.resources.budgets.index'))
            ])
            ->heading(__(
                'Budget (' .
                Budget::where('status', '=', 'pending')
                    ->count()
            ) . ')')
            ->query(
                Budget::query()
                    ->select()
                    ->where('status', '=', 'pending')
                    ->take(5)
            )
            ->paginated(false)
            ->striped()
            ->columns([
                TextColumn::make('code')
                    ->label(__('Code')),
                TextColumn::make('content.customer_name')
                    ->label(__('Customer Name')),
                TextColumn::make('content.type')
                    ->label(__('Type')),
                TextColumn::make('content.area')
                    ->label(__('Area / Local')),
            ]);
    }
}
