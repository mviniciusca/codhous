<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use App\Models\Mail;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMails extends ListRecords
{
    protected static string $resource = MailResource::class;

    public function getTabs(): array
    {
        return [
            'inbox' => Tab::make(__('Inbox'))
                ->icon('heroicon-m-inbox')
                ->badge(Mail::query()->where('is_sent', false)->where('is_read', false)->where('is_spam', false)->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_sent', false)->where('is_spam', false)->withoutTrashed()),
            'sent' => Tab::make(__('Sent'))
                ->icon('heroicon-m-paper-airplane')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_sent', true)->withoutTrashed()),
            'starred' => Tab::make(__('Starred'))
                ->icon('heroicon-m-star')
                ->badge(Mail::query()->where('is_favorite', true)->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_favorite', true)->withoutTrashed()),
            'trash' => Tab::make(__('Trash'))
                ->icon('heroicon-m-trash')
                ->modifyQueryUsing(fn(Builder $query) => $query->onlyTrashed()),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'inbox';
    }

    public function getHeaderActions(): array
    {
        return [];
    }
}
