<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductOptionRelationManager extends RelationManager
{
    protected static string $relationship = 'productOption';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix(env('CURRENCY_SUFFIX'))
                    ->maxValue(42949672.95)
                    ->label(__('Price'))
                    ->helperText(__('Price per unity')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->heading(__('Product Options'))
            ->description(__('Variations for this product'))
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name')),
                TextColumn::make('price')
                    ->label(__('Price per Unity'))
                    ->alignEnd()
                    ->prefix(env('CURRENCY_SUFFIX').' '),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('New Option'))
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
