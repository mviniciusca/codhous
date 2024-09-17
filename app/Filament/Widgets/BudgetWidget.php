<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use App\Models\Budget;
use App\Models\Setting;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
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
                Action::make('new')
                    ->label(__('New'))
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->modalHeading(__('Quick Simulation'))
                    ->form([
                        Section::make(__('Quantity & Pricing'))
                            ->description(__('Quantity & Pricing'))
                            ->label(__('Quantity & Pricing'))
                            ->icon('heroicon-o-currency-dollar')
                            ->columns(4)
                            ->schema([
                                Select::make('content.fck')
                                    ->label(__('FCK'))
                                    ->required()
                                    ->options(
                                        Setting::select(['budget'])
                                            ->get()
                                            ->pluck('budget.fck', 'id')
                                    ),
                                Select::make('content.area')
                                    ->label(__('Area'))
                                    ->required()
                                    ->options(
                                        Setting::select(['budget'])
                                            ->get()
                                            ->pluck('budget.area', 'id')
                                    ),
                                Select::make('content.type')
                                    ->required()
                                    ->label(__('Product'))
                                    ->options(
                                        Setting::select(['budget'])
                                            ->get()
                                            ->pluck('budget.type', 'id')
                                    ),
                                TextInput::make('content.quantity')
                                    ->required()
                                    ->label(__('Quantity'))
                                    ->suffix('m³')
                                    ->placeholder(__('quantity (m³)')),
                            ]),
                        Section::make(__('Locale & Shipment'))
                            ->description(__('Quantity & Pricing'))
                            ->label(__('Quantity & Pricing'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([]),
                        Section::make(__('Customer Information'))
                            ->description(__('Quantity & Pricing'))
                            ->label(__('Quantity & Pricing'))
                            ->icon('heroicon-o-currency-dollar')
                            ->columns(3)
                            ->schema([
                                TextInput::make('content.customer_name')
                                    ->label(__('Name'))
                                    ->placeholder(__('Admin Name'))
                                    ->helperText(__('Admin Name'))
                                    ->default(__(User::first()->name)),
                            ]),
                    ]),
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
