<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Budget;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\BudgetResource\Pages;

class BudgetResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'Budget';
    protected static ?string $model = Budget::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return __('Budget');
    }
    protected static ?string $navigationGroup = 'Budget';
    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'content'];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('Customer') => $record->content['customer_name'],
            __('Code') => $record->code,
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count != 0 ? $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('Budget Overview'))
                            ->columns(4)
                            ->description(__('Organize your budget report'))
                            ->icon('heroicon-o-document')
                            ->schema([
                                Toggle::make('is_active')
                                    ->helperText(__('Enable or disable this budget from the dashboard view'))
                                    ->label(__('Active'))
                                    ->inline(),
                                Select::make('status')
                                    ->helperText(__('Set the budget status'))
                                    ->options([
                                        'pending' => __('Pending'),
                                        'on going' => __('On Going'),
                                        'done' => __('Done'),
                                        'ignored' => __('Ignored'),
                                    ]),
                                TextInput::make('code')
                                    ->label(__('Budget Code'))
                                    ->helperText(__('Use this code to search'))
                                    ->disabled(),
                                DateTimePicker::make('created_at')
                                    ->format('d/m/Y H:i')
                                    ->label(__('Date'))
                                    ->disabled()
                                    ->helperText(__('When this budget was created'))
                            ]),
                    ]),
                Section::make('Budget Content')
                    ->description(__('Here is the content from your budget'))
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Fieldset::make(__('Customer Information'))
                            ->columns(3)
                            ->schema([
                                Group::make()
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('content.customer_name')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label(__('Customer Name')),
                                        TextInput::make('content.customer_email')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label(__('Email')),
                                        TextInput::make('content.customer_phone')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label(__('Phone')),
                                    ]),
                                TextInput::make('content.postcode')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated()
                                    ->label(__('CEP')),
                                TextInput::make('content.street')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->label(__('Street')),
                                TextInput::make('content.number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Number')),
                                TextInput::make('content.city')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated()
                                    ->label(__('City')),
                                TextInput::make('content.neighborhood')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->label(__('Neighborhood')),
                                TextInput::make('content.state')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated()
                                    ->label(__('State')),
                            ]),
                        Fieldset::make('Construction Components')
                            ->columns(4)
                            ->schema([
                                TextInput::make('content.quantity')
                                    ->required()
                                    ->label(__('Quantity m³'))
                                    ->suffix(__('m³'))
                                    ->helperText(__('Min value is 3 (ABNT NBR 7212)'))
                                    ->afterStateHydrated(fn(Set $set, string $state) => $set('quantity', $state))
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('content.area')
                                    ->required()
                                    ->label(__('Local / Area'))
                                    ->helperText(__('Local or area to be concreted'))
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('content.fck')
                                    ->required()
                                    ->label(__('FCK'))
                                    ->helperText(__('Feature Compression Know'))
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('content.product')
                                    ->required()
                                    ->label(__('Product'))
                                    ->helperText(__('Type of Concrete'))
                                    ->disabled()
                                    ->dehydrated()
                            ]),
                    ])
                    ->collapsible(),
                Section::make(__('Pricing'))
                    ->icon('heroicon-o-currency-dollar')
                    ->description(__('Pricing Definition & Total Cost'))
                    ->collapsible()
                    ->columns(5)
                    ->schema([
                        TextInput::make('content.quantity')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->readonly()
                            ->required()
                            ->suffix('m³')
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            })
                            ->numeric(),
                        TextInput::make('content.price')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->label(__('Price per Unity (m³)'))
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.tax')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->prefix('+' . env('CURRENCY_SUFFIX'))
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.discount')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->numeric()
                            ->required()
                            ->prefix('-' . env('CURRENCY_SUFFIX'))
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.total')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->disabled()
                            ->numeric()
                            ->required()
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->step(0.01),
                    ]),
            ]);
    }

    public static function calculateTotal(Get $get, Set $set): void
    {
        $quantity = floatval($get('content.quantity') ?? 0);
        $price = floatval($get('content.price') ?? 0);
        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);

        $total = $quantity * $price + $tax - $discount;
        $set('content.total', number_format($total, 2, '.', ''));
    }
    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-currency-dollar')
                    ->label(__('New Budget')),
            ])
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->label(__('Code')),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'primary',
                        'on going' => 'warning',
                        'done' => 'success',
                        'ignored' => 'danger'
                    }),
                TextColumn::make('content.customer_name')
                    ->sortable()
                    ->searchable()
                    ->label(__('Name')),
                TextColumn::make('content.customer_email')
                    ->label(__('Email')),
                TextColumn::make('content.customer_phone')
                    ->label(__('Phone')),
                TextColumn::make('created_at')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->label(__('Date')),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->alignCenter()
                    ->boolean()
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->placeholder(__('Default'))
                    ->default(true)
                    ->label(__('Show Budgets'))
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Inactive')),
                SelectFilter::make('status')
                    ->placeholder(__('All Status'))
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'on going' => __('On Going'),
                        'done' => __('Done'),
                        'ignored' => __('Ignored'),
                    ])
                    ->searchable(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
            'bin' => Pages\BudgetBin::route('/bin'),
        ];
    }
}
