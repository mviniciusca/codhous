<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Resources\Pages\Page;

/**
 * Rota antiga de visualização: redireciona para a caixa com a mensagem aberta.
 */
class ViewMail extends Page
{
    protected static string $resource = MailResource::class;

    public function mount(int|string $record): void
    {
        $mail = MailResource::getEloquentQuery()->findOrFail($record);

        $this->redirect(MailResource::getUrl('index', [
            'pasta' => match (true) {
                $mail->trashed() => 'trash',
                (bool) $mail->is_spam => 'spam',
                (bool) $mail->is_sent => 'sent',
                default => 'inbox',
            },
            'm' => $mail->id,
        ]));
    }
}
