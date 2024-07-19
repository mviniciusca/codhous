<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use App\Models\Mail;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SentMail extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    protected static string $resource = MailResource::class;
    protected static string $view = 'filament.resources.mail-resource.pages.sent-mail';
    public static function getNavigationLabel(): string
    {
        return __('Sent');
    }
    public static function table(Table $table): Table
    {
        return $table
            ->query(Mail::query())
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
                    //
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ]);
    }

}