<?php

namespace App\Filament\Widgets;


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
            ->defaultSort('created_at', 'desc')
            ->recordUrl(
                fn(Budget $record): string => route('filament.admin.resources.budgets.edit', ['record' => $record]),
            )
            ->description(__('New and pending budgets'))
            ->headerActions([
                Action::make('new_budget')
                    ->label(__('Budgets'))
                    ->label(__('New'))
                    ->color('primary')
                    ->icon('heroicon-o-currency-dollar')
                    ->url(route('filament.admin.resources.budgets.create')),
                Action::make('view_all')
                    ->label(__('Budgets'))
                    ->label(__('View All'))
                    ->icon('heroicon-o-arrow-up-right')
                    ->url(route('filament.admin.resources.budgets.index')),
            ])
            ->heading(__(
                'Budget (' .
                Budget::where('status', '=', 'pending')
                    ->where('is_active', '=', true)
                    ->count()
            ) . ')')
            ->query(
                Budget::query()
                    ->select()
                    ->where('status', '=', 'pending')
                    ->where('is_active', '=', true)
                    ->take(5)
            )
            ->paginated(false)
            ->striped()
            ->columns([
                TextColumn::make('code')
                    ->label(__('Code')),
                TextColumn::make('content.customer_name')
                    ->label(__('Customer Name')),
                TextColumn::make('created_at')
                    ->sortable()
                    ->datetime('d/m/Y H:i')
                    ->label(__('Received At')),
            ]);
    }
}
