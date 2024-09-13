<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationGroup = 'Customers & Partners';
    public static function getNavigationLabel(): string
    {
        return __('Customers');
    }
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function count(): ?string
    {
        $count = Customer::withoutTrashed()->count();
        return $count !== 0 ? $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Customer'))
                    ->description(__('Manager your Customer List'))
                    ->columns(3)
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Customer Name'))
                            ->helperText(__('Set the customer name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email Address'))
                            ->helperText(__('Place here the email address from your customer'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->helperText(__('Customer Phone number'))
                            ->tel()
                            ->mask('(99)99999-9999')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postcode')
                            ->label(__('Postcode'))
                            ->helperText(__('Customer Postcode'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address.street')
                            ->label(__('Address Street'))
                            ->helperText(__('Address Street'))
                            ->required(),
                        Forms\Components\TextInput::make('address.number')
                            ->label(__('Address Number'))
                            ->helperText(__('Address Number')),
                        Forms\Components\TextInput::make('address.neighborhood')
                            ->label(__('Neighborhood'))
                            ->helperText(__('Neighborhood'))
                            ->required(),
                        Forms\Components\TextInput::make('address.city')
                            ->label(__('City'))
                            ->helperText(__('City'))
                            ->required(),
                        Forms\Components\TextInput::make('address.state')
                            ->label(__('State'))
                            ->helperText(__('State'))
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('postcode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
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
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'bin' => Pages\CustomerBin::route('/bin'),
        ];
    }
}
