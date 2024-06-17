<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\NewsletterResource;
use App\Filament\Resources\NewsletterResource\Widgets\NewsletterOverwview;

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
            // Actions\CreateAction::make(),
        ];
    }
}
