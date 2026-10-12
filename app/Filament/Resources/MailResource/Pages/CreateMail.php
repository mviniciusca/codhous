<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Resources\Pages\Page;

/**
 * Rota antiga de criação: abre a caixa com o modal "Nova mensagem".
 */
class CreateMail extends Page
{
    protected static string $resource = MailResource::class;

    public function mount(): void
    {
        $this->redirect(MailResource::getUrl('index', ['action' => 'compose']));
    }
}
