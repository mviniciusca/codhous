<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use App\Models\Mail;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SpamMail extends ListRecords
{
    public static function count(): string
    {
        return Mail::query()
            ->where('is_spam', true)
            ->where('is_sent', false)
            ->count();
    }
    protected static string $resource = MailResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Mail::query()
                    ->where('is_sent', false)
                    ->where('is_spam', true)
            )
            ->columns([
                IconColumn::make('is_sent')
                    ->wrap()
                    ->label(__(''))
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-airplane')
                    ->trueColor('secondary')
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
                    DeleteAction::make()->label(__('Trash')),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Trash all Messages')),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    //Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
