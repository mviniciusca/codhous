<?php

namespace App\Filament\Resources\AlertResource\Concerns;

use App\Filament\Resources\AlertResource;
use App\Models\Alert;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Blade;
use Livewire\Attributes\Renderless;

/**
 * Editor de alertas (criação e edição): prévia ao vivo com o estado não salvo,
 * barra de ações inferior e aviso ao sair com alterações não salvas.
 */
trait InteractsWithAlertEditor
{
    /** O editor avisa antes de sair com alterações não salvas, mesmo sem a opção global do painel */
    protected function hasUnsavedDataChangesAlert(): bool
    {
        return true;
    }

    /**
     * HTML do alerta renderizado pelo próprio componente do site (x-site-alerts),
     * com o estado atual do formulário. A prévia (iframe) troca o alerta por este HTML.
     */
    #[Renderless]
    public function renderAlertPreview(): string
    {
        $data = is_array($this->data ?? null) ? $this->data : [];

        // Só valores simples: o estado vem do navegador e vai direto para a view do site
        $text = fn (string $key): ?string => isset($data[$key]) && is_scalar($data[$key]) ? (string) $data[$key] : null;

        $alert = new Alert;
        $alert->forceFill([
            'name' => $text('name'),
            'type' => array_key_exists((string) $text('type'), Alert::typeCatalog()) ? $text('type') : Alert::TYPE_MODAL,
            'style' => array_key_exists((string) $text('style'), Alert::styleCatalog()) ? $text('style') : Alert::STYLE_INFO,
            'position' => $text('position'),
            'title' => $text('title'),
            'message' => filled($text('message')) ? $text('message') : 'Escreva a mensagem principal para vê-la aqui.',
            'cta_label' => $text('cta_label'),
            'cta_url' => $text('cta_url'),
            'is_dismissible' => (bool) ($data['is_dismissible'] ?? true),
            'use_cookie' => false,
        ]);
        $alert->id = $this->getAlertRecord()?->getKey() ?? 0;

        try {
            return Blade::render('<x-site-alerts :alerts="$alerts" :preview="true" />', ['alerts' => collect([$alert])]);
        } catch (\Throwable $e) {
            app('view')->flushState();
            report($e);

            return '';
        }
    }

    public function getAlertRecord(): ?Alert
    {
        return method_exists($this, 'getRecord') && $this->getRecord() instanceof Alert ? $this->getRecord() : null;
    }

    /** Resultado do salvamento: título e texto conforme o status em que o alerta ficou */
    protected function alertSavedNotification(Alert $alert, ?string $title = null): Notification
    {
        $status = $alert->status();

        $body = match ($status) {
            Alert::STATUS_ACTIVE => 'O alerta já está aparecendo no site.',
            Alert::STATUS_SCHEDULED => 'O alerta aparece no site a partir de '.$alert->start_at->format('d/m/Y \à\s H:i').'.',
            Alert::STATUS_EXPIRED => 'O período de exibição já terminou. Ajuste as datas para ele voltar a aparecer.',
            default => 'O alerta não aparece no site até ser publicado.',
        };

        $title ??= match ($status) {
            Alert::STATUS_DRAFT => 'Rascunho salvo',
            Alert::STATUS_SCHEDULED => 'Alerta agendado',
            default => 'Alerta publicado',
        };

        return Notification::make()
            ->title($title)
            ->body($body)
            ->status($status === Alert::STATUS_EXPIRED ? 'warning' : 'success')
            ->actions([
                NotificationAction::make('list')
                    ->label('Ver todos os alertas')
                    ->url(AlertResource::getUrl('index')),
            ]);
    }
}
