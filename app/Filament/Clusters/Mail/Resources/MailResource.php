<?php

namespace App\Filament\Clusters\Mail\Resources;

use App\Filament\Clusters\Mail;
use App\Filament\Clusters\Mail\Resources\MailResource\Pages;
use App\Filament\Clusters\Mail\Resources\MailResource\RelationManagers;
use App\Models\Mail as MailModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MailResource extends Resource
{
    protected static ?string $model = MailModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Mail::class;

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
                //
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
            'index'  => Pages\ListMails::route('/'),
            'create' => Pages\CreateMail::route('/create'),
            'edit'   => Pages\EditMail::route('/{record}/edit'),
        ];
    }
}
