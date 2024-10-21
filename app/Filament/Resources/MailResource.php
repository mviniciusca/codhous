<?php

namespace App\Filament\Resources;

use App\Models\Mail;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MailResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MailResource extends Resource
{
    protected static ?string $model = Mail::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Mail';
    public static function count(): ?string
    {
        $count = Mail::query()
            ->where('is_read', false)
            ->where('is_sent', false)
            ->where('is_spam', false)
            ->count();
        return $count !== 0 ? $count : null;
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
            ->query(
                Mail::query()
                    ->where('is_spam', false)
                    ->where('is_sent', false)
                    ->where('is_read', false)
            )
            ->columns([
                IconColumn::make('is_sent')
                    ->wrap()
                    ->label(__(''))
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-airplane')
                    ->trueColor('primary')
                    ->falseIcon('heroicon-o-inbox')
                    ->falseColor('primary'),
                ToggleColumn::make('is_favorite')
                    ->label(__('Important'))
                    ->inline()
                    ->alignCenter()
                    ->onIcon('heroicon-s-star'),
                TextColumn::make('name')
                    ->limit(25)
                    ->label(__('From')),
                TextColumn::make('email')
                    ->limit(25)
                    ->label(__('Email')),
                TextColumn::make('subject')
                    ->limit(30)
                    ->label(ucfirst(__('Subject'))),
                TextColumn::make('created_at')
                    ->label(__('Received'))
                    ->date('d/m/Y H:i')
            ])
            ->searchable()
            ->striped()
            ->paginated(25)
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->persistFiltersInSession()
            ->actions([
                ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->label(__('Trash')),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Trash all Messages')),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    //Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListMails::route('/'),
            'starred' => Pages\StarredMail::route('/starred'),
            'sent' => Pages\SentMail::route('/sent'),
            'view' => Pages\ViewMail::route('/{record}/view'),
            'create' => Pages\CreateMail::route('/create'),
            'fav' => Pages\FavoriteMail::route('/fav'),
            'read' => Pages\ReadMail::route('/read'),
            'spam' => Pages\SpamMail::route('/spam'),
            'bin' => Pages\BinMail::route('/bin'),
        ];
    }
}
