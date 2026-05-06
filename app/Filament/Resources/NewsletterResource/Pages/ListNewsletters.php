<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use App\Filament\Resources\NewsletterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsletters extends ListRecords
{
    protected static string $resource = NewsletterResource::class;

    public function getTitle(): string 
    {
        return 'Inscritos na Newsletter';
    }

    public function getSubheading(): ?string
    {
        return 'Acompanhe o crescimento da sua base de contatos e gerencie os usuários interessados em receber novidades.';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NewsletterResource\Widgets\NewsletterOverwview::class,
        ];
    }
}
