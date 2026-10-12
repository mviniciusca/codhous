<?php

namespace App\Filament\Resources\NewsletterCampaignResource\Pages;

use App\Filament\Clusters\NewsletterCluster\Pages\SendNewsletter;
use App\Filament\Resources\NewsletterCampaignResource;
use App\Models\NewsletterCampaign;
use App\Services\Newsletter\NewsletterSender;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewNewsletterCampaign extends ViewRecord
{
    protected static string $resource = NewsletterCampaignResource::class;

    public function getTitle(): string
    {
        return 'Detalhes do envio';
    }

    public function getSubheading(): ?string
    {
        return 'Conteúdo enviado, números do envio e destinatários com falha.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resume')
                ->label('Continuar envio')
                ->icon('heroicon-m-play')
                ->visible(fn (NewsletterCampaign $record) => $record->isSending())
                ->url(fn (NewsletterCampaign $record) => SendNewsletter::getUrl(['envio' => $record->id])),
            Action::make('retry')
                ->label('Reenviar para as falhas')
                ->icon('heroicon-m-arrow-path')
                ->color('gray')
                ->visible(fn (NewsletterCampaign $record) => ! $record->isSending() && $record->failed_count > 0)
                ->requiresConfirmation()
                ->modalDescription(fn (NewsletterCampaign $record) => "Tentar de novo o envio para {$record->failed_count} destinatário(s) que falharam? Quem já recebeu não recebe outra vez.")
                ->action(function (NewsletterCampaign $record, NewsletterSender $sender) {
                    $sender->retryFailed($record);
                    $this->redirect(SendNewsletter::getUrl(['envio' => $record->id]));
                }),
        ];
    }
}
