<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Budget';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public function getTitle(): string|Htmlable
    {
        return __('Products');
    }

    public static function count(): ?string
    {
        $count = Product::query()
            ->where('is_active', true)
            ->count();
        return $count !== 0 ? $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Product'))
                    ->icon('heroicon-o-shopping-bag')
                    ->description(__('Manager your product for your Budget Tool'))
                    ->columns(5)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label(__('Active'))
                            ->helperText(__('Product status'))
                            ->inline(false)
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpan(2)
                            ->label(__('Name'))
                            ->helperText(__('Product name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->columnSpan(2)
                            ->numeric()
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->maxValue(42949672.95)
                            ->label(__('Price'))
                            ->helperText(__('Price per unity')),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('Price (Unity)'))
                    ->prefix(env('CURRENCY_SUFFIX') . ' ')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->alignCenter()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
