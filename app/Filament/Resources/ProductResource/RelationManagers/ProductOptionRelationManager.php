<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Enums\ProductUnit;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductOptionRelationManager extends RelationManager
{
    protected static string $relationship = 'productOption';

    protected static ?string $title = 'Variações e Preços';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Descrição da Variação')
                    ->placeholder('Ex: FCK 25 ou Média')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(\Filament\Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state)))
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule, RelationManager $livewire) => $rule->where('product_id', $livewire->ownerRecord->id)),
                TextInput::make('slug')
                    ->label('Slug (Identificador)')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule, RelationManager $livewire) => $rule->where('product_id', $livewire->ownerRecord->id)),
                Select::make('unit')
                    ->label('Unidade de Medida')
                    ->required()
                    ->options(ProductUnit::class),
                TextInput::make('price')
                    ->label('Preço Base')
                    ->required()
                    ->numeric()
                    ->prefix('R$')
                    ->maxValue(42949672.95)
                    ->helperText('Preço por unidade (ex: preço por m³ ou kg)'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->heading('Variações de Produtos')
            ->description('Gerencie os diferentes tipos, traços ou medidas deste produto.')
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Variação'),
                TextColumn::make('unit')
                    ->label('Unidade')
                    ->badge(),
                TextColumn::make('price')
                    ->label('Preço Unitário')
                    ->alignEnd()
                    ->money('BRL'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nova Variação')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
            ]);
    }
}
