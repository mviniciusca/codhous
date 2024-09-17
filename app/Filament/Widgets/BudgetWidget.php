<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Budget;
use App\Models\Setting;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\SelectColumn;
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
            ->description(__('Quick view in your budgets here.'))
            ->headerActions([
                CreateAction::make('new')
                    ->label(__('New'))
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar'),
                Action::make('edit')
                    ->label(__('Budgets'))
                    ->label(__('View All'))
                    ->icon('heroicon-o-eye')
                    ->url(route('filament.admin.resources.budgets.index'))
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
                TextColumn::make('content.area')
                    ->label(__('Area')),
            ]);
    }
}
