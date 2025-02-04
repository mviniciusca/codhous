<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Settings';

    public static function getNavigationLabel(): string
    {
        return __('User Settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('User Settings'))
                    ->icon('heroicon-o-user')
                    ->description(__('Manager your user here.'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->helperText(__('Your name'))
                            ->prefixIcon('heroicon-o-user')
                            ->required(),
                        TextInput::make('email')
                            ->label(__('Email'))
                            ->helperText(__('Your email. This affetcs your login.'))
                            ->prefixIcon('heroicon-o-envelope')
                            ->required(),
                        TextInput::make('password')
                            ->label(__('Password'))
                            ->helperText(__('Your password. This affetcs your login.'))
                            ->prefixIcon('heroicon-o-key')
                            ->required()
                            ->password()
                            ->revealable(),
                        TextInput::make('password_confirmation')
                            ->prefixIcon('heroicon-o-key')
                            ->helperText(__('Confirm your password. This affetcs your login.'))
                            ->label(__('Confirm Password'))
                            ->required()
                            ->same('password')
                            ->password()
                            ->revealable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('roles.name'),
            ])
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
