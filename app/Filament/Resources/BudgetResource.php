<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use App\Models\Budget;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BudgetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BudgetResource\RelationManagers;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

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
                                TextInput::make('code')
                                    ->label(__('Budget Code'))
                                    ->helperText(__('Use this code to search'))
                                    ->readOnly(),
                                Select::make('status')
                                    ->helperText(__('Set the budget status'))
                                    ->options([
                                        'pending' => __('Pending'),
                                        'on going' => __('On Going'),
                                        'done' => __('Done'),
                                        'ignored' => __('Ignored'),
                                    ]),
                                DateTimePicker::make('created_at')
                                    ->format('d/m/Y H:i')
                                    ->label(__('Date'))
                                    ->readOnly()
                                    ->helperText(__('When this budget was created'))
                            ]),
                    ]),
                Section::make('Budget Content')
                    ->description(__('Here is the content from your budget'))
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Fieldset::make(__('Customer Information'))
                            ->columns(4)
                            ->schema([
                                TextInput::make('content.customer_name')
                                    ->disabled()
                                    ->label(__('Customer Name')),
                                TextInput::make('content.customer_email')
                                    ->disabled()
                                    ->label(__('Email')),
                                TextInput::make('content.customer_phone')
                                    ->disabled()
                                    ->label(__('Phone')),
                                TextInput::make('content.cep')
                                    ->disabled()
                                    ->label(__('CEP')),
                            ]),
                        Fieldset::make('Construction Components')
                            ->columns(4)
                            ->schema([
                                TextInput::make('content.object')
                                    ->label(__('Local / Area'))
                                    ->helperText(__('Local or area to be concreted'))
                                    ->disabled(),
                                TextInput::make('content.quantity')
                                    ->label(__('Estimative Quantity m³'))
                                    ->helperText(__('Min value is 3 (ABNT NBR 7212)'))
                                    ->disabled(),
                                TextInput::make('content.type')
                                    ->label(__('Type of Concrete'))
                                    ->helperText(__('Type of Concrete'))
                                    ->disabled()
                            ]),
                    ]),
                Section::make('Budget Calculator')
                    ->description(__('Here is the calculator for your budget. Price and other values can be edited on Settings / Budget'))
                    ->icon('heroicon-o-pencil')
                    ->schema([]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'primary',
                        'on going' => 'warning',
                        'done' => 'success',
                        'ignored' => 'danger'
                    }),
                TextColumn::make('content.customer_name')
                    ->label(__('Name')),
                TextColumn::make('content.customer_email')
                    ->label(__('Email')),
                TextColumn::make('content.customer_phone')
                    ->label(__('Phone')),
            ])
            ->filters([
                //
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
        ];
    }
}
