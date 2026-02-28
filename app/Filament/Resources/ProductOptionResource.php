<?php

namespace App\Filament\Resources;

use App\Enums\ProductUnit;
use App\Filament\Resources\ProductOptionResource\Pages;
use App\Filament\Resources\ProductOptionResource\RelationManagers;
use App\Filament\Resources\ProductOptionResource\RelationManagers\ProductRelationManager;
use App\Models\Product;
use App\Models\ProductOption;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductOptionResource extends Resource
{
    protected static ?string $model = ProductOption::class;

    protected static ?string $navigationGroup = 'Budget';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationParentItem = 'Products';

    public static function getNavigationLabel(): string
    {
        return __('Options');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Product Options & Variations'))
                    ->description(__('Create or edit variations for your product'))
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        Select::make('product_id')
                            ->required()
                            ->label(__('Associated to'))
                            ->helperText(__('Main Product'))
                            ->searchable()
                            ->preload()
                            ->options(
                                Product::get()->pluck('name', 'id'),
                            ),
                        TextInput::make('name')
                            ->label(__('Option / Variation'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(\Filament\Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state)))
                            ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, callable $get) {
                                return $rule->where('product_id', $get('product_id'));
                            })
                            ->helperText(__('Option or variation for main product (must be unique per product)')),
                        Select::make('unit')
                            ->label(__('Unit'))
                            ->required()
                            ->options(
                                ProductUnit::class,
                            ),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, callable $get) {
                                return $rule->where('product_id', $get('product_id'));
                            })
                            ->helperText(__('Unique identifier (auto-generated)')),
                        TextInput::make('price')
                            ->label(__('Price'))
                            ->required()
                            ->numeric()
                            ->prefix(env('CURRENCY_SUFFIX', 'R$'))
                            ->step(0.01)
                            ->helperText(__('Price for this option')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->sortable()
                    ->searchable()
                    ->label(__('Product')),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label(__('Option')),
                TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->label(__('Slug')),
                TextColumn::make('unit')
                    ->label(__('Unit'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('price')
                    ->sortable()
                    ->money(env('CURRENCY_CODE', 'BRL'))
                    ->label(__('Price')),
            ])
            ->defaultSort('product.name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            ProductRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProductOptions::route('/'),
            'create' => Pages\CreateProductOption::route('/create'),
            'edit'   => Pages\EditProductOption::route('/{record}/edit'),
        ];
    }
}
