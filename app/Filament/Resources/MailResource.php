<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Mail;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MailResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MailResource\RelationManagers;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;

class MailResource extends Resource
{
    protected static ?string $model = Mail::class;
    protected static ?string $navigationGroup = 'Mail';
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count != 0 ? $count : null;
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
                ToggleColumn::make('is_favorite')
                    ->label(__('Important'))
                    ->inline()
                    ->alignCenter()
                    ->onIcon('heroicon-s-star'),
                IconColumn::make('is_sent')
                    ->wrap()
                    ->label(__(''))
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-up-left')
                    ->trueColor('secondary')
                    ->falseIcon('heroicon-o-arrow-down-right')
                    ->falseColor('primary'),
                TextColumn::make('name')
                    ->limit(25)
                    ->label(__('Name')),
                TextColumn::make('email')
                    ->limit(30)
                    ->label(__('Email')),
                TextColumn::make('subject')
                    ->limit(30)
                    ->label(__('Subject')),
                CheckboxColumn::make('is_read')
                    ->alignCenter()
                    ->label(__('Mark as Read')),
                TextColumn::make('created_at')
                    ->label(__('Received'))
                    ->date('d-m-Y H:i')
            ])
            ->searchable()
            ->striped()
            ->paginated(25)
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_spam')
                    ->label(__('Spam'))
                    ->placeholder(__('All Messages'))
                    ->trueLabel(__('Spam'))
                    ->falseLabel(__('Not Spam'))
                    ->default(false),
                TernaryFilter::make('is_read')
                    ->label(__('Inbox'))
                    ->placeholder(__('All Messages'))
                    ->trueLabel(__('Read'))
                    ->falseLabel(__('Unread'))
                    ->default(false),
                TernaryFilter::make('is_sent')
                    ->label(__('Send Messages'))
                    ->placeholder(__('All Messages'))
                    ->trueLabel(__('Sent'))
                    ->falseLabel(__('Received'))
                    ->default(false),
                TernaryFilter::make('is_favorite')
                    ->label(__('Important Messages'))
                    ->placeholder(__('All Messages'))
                    ->trueLabel(__('With Star'))
                    ->falseLabel(__('Without Star')),
            ], layout: FiltersLayout::Modal)
            ->persistFiltersInSession()
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMails::route('/'),
            'view' => Pages\ViewMail::route('/{record}/view'),
            'create' => Pages\CreateMail::route('/create'),
        ];
    }
}
