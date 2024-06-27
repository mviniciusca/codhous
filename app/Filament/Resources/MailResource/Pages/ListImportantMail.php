<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListImportantMail extends ListRecords
{
    protected static string $resource = MailResource::class;
    protected static ?string $navigationGroup = 'Mail';
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count != 0 ? $count : null;
    }
    public static function getNavigationLabel(): string
    {
        return __('Favorites');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Favorites');
    }
    public function table(Table $table): Table
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
                    ->falseLabel(__('Not Spam')),
                TernaryFilter::make('is_read')
                    ->label(__('Inbox'))
                    ->placeholder(__('All Messages'))
                    ->trueLabel(__('Read'))
                    ->falseLabel(__('Unread')),
                TernaryFilter::make('is_sent')
                    ->label(__('Send Messages'))
                    ->placeholder(__('All Messages'))
                    ->trueLabel(__('Sent'))
                    ->falseLabel(__('Received')),
                TernaryFilter::make('is_favorite')
                    ->label(__('Important Messages'))
                    ->placeholder(__('All Messages'))
                    ->trueLabel(__('With Star'))
                    ->falseLabel(__('Without Star'))
                    ->default(true),
            ], layout: FiltersLayout::Modal)
            ->persistFiltersInSession()
            ->actions([
                ActionGroup::make([
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }

}
