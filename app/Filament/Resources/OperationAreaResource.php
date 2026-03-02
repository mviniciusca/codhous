<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperationAreaResource\Pages;
use App\Models\OperationArea;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class OperationAreaResource extends Resource
{
    protected static ?string $model = OperationArea::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Budget';

    // the heroicon set included in the project doesn't contain a `location-marker` glyph,
    // fall back to the map icon which is already used elsewhere.
    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function getNavigationLabel(): string
    {
        return __('Operation Areas');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) OperationArea::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('General Information'))
                    ->description(__('Define the city, state and CEP prefix for this operational area'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('city')
                            ->label(__('City'))
                            ->required()
                            ->maxLength(255),
                        Select::make('state')
                            ->label(__('State'))
                            ->required()
                            ->options([
                                'AC' => 'AC',
                                'AL' => 'AL',
                                'AP' => 'AP',
                                'AM' => 'AM',
                                'BA' => 'BA',
                                'CE' => 'CE',
                                'DF' => 'DF',
                                'ES' => 'ES',
                                'GO' => 'GO',
                                'MA' => 'MA',
                                'MT' => 'MT',
                                'MS' => 'MS',
                                'MG' => 'MG',
                                'PA' => 'PA',
                                'PB' => 'PB',
                                'PR' => 'PR',
                                'PE' => 'PE',
                                'PI' => 'PI',
                                'RJ' => 'RJ',
                                'RN' => 'RN',
                                'RS' => 'RS',
                                'RO' => 'RO',
                                'RR' => 'RR',
                                'SC' => 'SC',
                                'SP' => 'SP',
                                'SE' => 'SE',
                                'TO' => 'TO',
                            ])
                            ->default('RJ'),
                        TextInput::make('postcode_prefix')
                            ->label(__('CEP prefix'))
                            ->required()
                            ->maxLength(5)
                            // use "9" pattern so the field is editable and still
                            // restricts to digits; "0" forces placeholder zeros.
                            ->mask('99999')
                            ->helperText(__('First five digits of the CEP')),
                    ]),
                Section::make(__('Settings'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->inline(),
                        Toggle::make('is_base')
                            ->label(__('Base'))
                            ->helperText(__('Mark this area as a company base (one or more)'))
                            ->inline(),
                        TextInput::make('shipping_fee')
                            ->label(__('Shipping Fee'))
                            ->numeric()
                            ->prefix('R$')
                            ->required()
                            ->step(0.01),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading(__('Operation Areas'))
            ->description(__('Manage the list of operational areas and the CEP prefixes they cover'))
            ->striped()
            ->searchable()
            ->searchPlaceholder(__('Search by city or CEP prefix'))
            ->columns([
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('postcode_prefix')
                    ->label(__('CEP prefix'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('is_base')
                    // only render text when value is truthy - otherwise badge will
                    // be empty and visually absent
                    ->formatStateUsing(fn($state) => $state ? __('Base') : '')
                    ->colors([
                        'success' => true,
                    ])
                    ->sortable(),
                TextColumn::make('shipping_fee')
                    ->label(__('Shipping Fee'))
                    ->money('BRL', true)
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->default(true)
                    ->label(__('Status'))
                    ->placeholder(__('Show All'))
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Inactive')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ]),
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
            // no relations yet
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOperationAreas::route('/'),
            'create' => Pages\CreateOperationArea::route('/create'),
            'edit' => Pages\EditOperationArea::route('/{record}/edit'),
        ];
    }
}
