<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use App\Filament\Resources\NewsletterResource;
use App\Filament\Resources\NewsletterResource\Widgets\NewsletterOverwview;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListNewsletters extends ListRecords
{
    protected static string $resource = NewsletterResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Subscribers');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NewsletterOverwview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_bin')
                ->label(false)
                ->icon('heroicon-o-trash')
                ->url(route('filament.admin.resources.subscribers.bin'))
                ->color('gray'),
        ];
    }
}
