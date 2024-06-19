<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterResource\Widgets\NewsletterOverwview;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Newsletter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NewsletterResource\Pages;
use App\Filament\Resources\NewsletterResource\RelationManagers;
use Filament\View\LegacyComponents\Widget;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    public static function getNavigationBadge(): ?string
    {
        if (static::getModel()::count() != 0) {
            return static::getModel()::count();
        }
        return null;
    }
    public static function getNavigationLabel(): string
    {
        return __('Subscribers');
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
                TextColumn::make('email')
                    ->label(__('Email')),
                IconColumn::make('active')
                    ->label(__('Status'))
                    ->boolean()
                    ->alignCenter()
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\DeleteAction::make(),
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

    public static function getWidgets(): array
    {
        return [
            NewsletterOverwview::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }
}
