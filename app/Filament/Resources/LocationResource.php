<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Budget';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationParentItem = 'Products';

    public static function getNavigationLabel(): string
    {
        return __('Locations');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Location'))
                    ->description(__('Manager your location / operational area'))
                    ->icon('heroicon-o-map')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Location or operational area'))
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Ex.: street, school ...')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading(__('Locations & Operational Areas'))
            ->description('Manage your clients Locations & Operational Areas here.')
            ->striped()
            ->searchable()
            ->searchPlaceholder(__('Search'))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit'   => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
