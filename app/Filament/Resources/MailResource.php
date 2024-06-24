<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Mail;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MailResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MailResource\RelationManagers;
use Filament\Tables\Columns\IconColumn;

class MailResource extends Resource
{
    protected static ?string $model = Mail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    public static function getNavigationBadge(): ?string
    {
        if (static::getModel()::count() != 0) {
            return static::getModel()::count();
        }
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('Inbox');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_favorite')
                    ->wrap()
                    ->label(__(''))
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->trueColor('primary')
                    ->falseIcon('heroicon-o-star')
                    ->falseColor('gray'),
                IconColumn::make('is_read')
                    ->wrap()
                    ->label(__(''))
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->trueColor('gray')
                    ->falseIcon('heroicon-s-envelope')
                    ->falseColor('primary'),
                IconColumn::make('is_sent')
                    ->wrap()
                    ->label(__(''))
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-up-left')
                    ->trueColor('primary')
                    ->falseIcon('heroicon-o-arrow-down-right')
                    ->falseColor('secondary'),
                TextColumn::make('name')
                    ->limit(25)
                    ->label(__('Name')),
                TextColumn::make('email')
                    ->limit(30)
                    ->label(__('Email')),
                TextColumn::make('subject')
                    ->limit(50)
                    ->label(__('Subject')),
            ])
            ->searchable()
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ])
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
            'index' => Pages\ListMails::route('/'),
            'create' => Pages\CreateMail::route('/create'),
            'edit' => Pages\EditMail::route('/{record}/edit'),
        ];
    }
}
