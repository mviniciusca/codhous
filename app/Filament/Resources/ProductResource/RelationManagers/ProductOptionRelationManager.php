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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(\Filament\Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state)))
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule, RelationManager $livewire) => $rule->where('product_id', $livewire->ownerRecord->id)),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule, RelationManager $livewire) => $rule->where('product_id', $livewire->ownerRecord->id)),
                Select::make('unit')
                    ->label(__('Unit'))
                    ->required()
                    ->options(ProductUnit::class),
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
                TextColumn::make('slug')
                    ->label(__('Slug')),
                TextColumn::make('unit')
                    ->label(__('Unit'))
                    ->badge(),
                TextColumn::make('price')
                    ->label(__('Price per Unity'))
                    ->alignEnd()
                    ->prefix(env('CURRENCY_SUFFIX') . ' '),
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
