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

    protected static ?string $navigationGroup = 'Orçamentos';
    protected static ?int $navigationSort = 5;

    // the heroicon set included in the project doesn't contain a `location-marker` glyph,
    // fall back to the map icon which is already used elsewhere.
    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function getNavigationLabel(): string
    {
        return 'Áreas de Operação';
    }

    public static function getModelLabel(): string
    {
        return 'Área de Operação';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Áreas de Operação';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) OperationArea::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações Gerais')
                    ->icon('heroicon-o-map-pin')
                    ->description('Identifique a localização geográfica e a abrangência postal desta área.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('city')
                            ->label('Cidade')
                            ->helperText('Nome da cidade onde a operação ocorre.')
                            ->required()
                            ->maxLength(255),
                        Select::make('state')
                            ->label('Estado')
                            ->helperText('Sigla do estado da federação.')
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
                            ->label('Prefixo de CEP (representativo)')
                            ->required()
                            ->maxLength(5)
                            ->mask('99999')
                            ->helperText('Primeiros 5 dígitos representativos (ex: 25000). Use a faixa abaixo para cobrir toda a cidade.'),
                        TextInput::make('postcode_start')
                            ->label('Início da faixa de CEP')
                            ->maxLength(5)
                            ->mask('99999')
                            ->placeholder('20000')
                            ->helperText('Início da faixa (5 dígitos). Ex: Rio de Janeiro usa 20000 a 23999.'),
                        TextInput::make('postcode_end')
                            ->label('Fim da faixa de CEP')
                            ->maxLength(5)
                            ->mask('99999')
                            ->placeholder('23999')
                            ->helperText('Fim da faixa (5 dígitos). CEP é considerado na área se estiver entre início e fim.'),
                    ]),
                Section::make('Configurações')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->description('Ajuste o comportamento operacional e custos associados a esta localidade.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->helperText('Se desativado, esta área não aparecerá nas opções de orçamento.')
                            ->default(true)
                            ->inline(),
                        Toggle::make('is_base')
                            ->label('Base')
                            ->helperText('Marque esta área como uma base da empresa (uma ou mais).')
                            ->inline(),
                        TextInput::make('shipping_fee')
                            ->label('Taxa de Entrega')
                            ->helperText('Valor fixo cobrado para entregas nesta região.')
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
            ->striped()
            ->searchable()
            ->searchPlaceholder('Buscar por cidade ou prefixo de CEP')
            ->columns([
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->label('Estado')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('postcode_prefix')
                    ->label('Prefixo de CEP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('postcode_start')
                    ->label('Faixa CEP')
                    ->formatStateUsing(fn ($record) => $record->postcode_start && $record->postcode_end
                        ? "{$record->postcode_start} – {$record->postcode_end}"
                        : '–')
                    ->placeholder('–'),
                Tables\Columns\BadgeColumn::make('is_base')
                    // only render text when value is truthy - otherwise badge will
                    // be empty and visually absent
                    ->formatStateUsing(fn($state) => $state ? 'Base' : '')
                    ->colors([
                        'success' => true,
                    ])
                    ->sortable(),
                TextColumn::make('shipping_fee')
                    ->label('Taxa de Entrega')
                    ->money('BRL', true)
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->default(true)
                    ->label('Status')
                    ->placeholder('Mostrar Todos')
                    ->trueLabel('Ativo')
                    ->falseLabel('Inativo'),
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
