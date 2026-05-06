<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMails extends ListRecords
{
    protected static string $resource = MailResource::class;

    public function getTitle(): string 
    {
        return 'Caixa de Entrada e Mensagens';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie as comunicações recebidas através do seu site e responda aos seus clientes.';
    }

    protected function getHeaderActions(): array
    {
        return [
            // O botão de Nova Mensagem já está definido no headerActions da tabela no Resource
        ];
    }
}
