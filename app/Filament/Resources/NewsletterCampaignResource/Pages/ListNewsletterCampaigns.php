<?php

namespace App\Filament\Resources\NewsletterCampaignResource\Pages;

use App\Filament\Clusters\NewsletterCluster\Pages\SendNewsletter;
use App\Filament\Resources\NewsletterCampaignResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListNewsletterCampaigns extends ListRecords
{
    protected static string $resource = NewsletterCampaignResource::class;

    public function getSubheading(): ?string
    {
        return 'Acompanhe os comunicados enviados, quantas pessoas receberam e o motivo de cada falha.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('compose')
                ->label('Enviar mensagem')
                ->icon('heroicon-m-paper-airplane')
                ->url(SendNewsletter::getUrl()),
        ];
    }
}
