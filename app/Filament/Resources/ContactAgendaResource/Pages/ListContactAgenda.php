<?php

namespace App\Filament\Resources\ContactAgendaResource\Pages;

use App\Filament\Resources\ContactAgendaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactAgenda extends ListRecords
{
    protected static string $resource = ContactAgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Novo contato'))
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie seus contatos, acompanhe o histórico de conversas e organize sua agenda comercial.';
    }
}
