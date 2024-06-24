<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMail extends ViewRecord
{
    protected static string $resource = MailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): Htmlable|string
    {
        return __('Mail');
    }
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                    ->label(__('Name:')),
                TextEntry::make('email')
                    ->label(__('From:')),
                TextEntry::make('created_at')
                    ->date('d-m-Y H:i')
                    ->label(__('Date:')),
                TextEntry::make('subject')
                    ->label(__('Subject:'))
                    ->columnSpanFull(),
                IconEntry::make('is_favorite')->boolean(),
                IconEntry::make('is_read')->boolean(),
                TextEntry::make('message')
                    ->label(__('Message:'))
                    ->columnSpanFull()
            ])
            ->columns(3)
        ;
    }
}
