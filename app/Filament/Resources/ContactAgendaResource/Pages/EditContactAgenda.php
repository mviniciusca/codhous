<?php

namespace App\Filament\Resources\ContactAgendaResource\Pages;

use App\Filament\Resources\ContactAgendaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactAgenda extends EditRecord
{
    protected static string $resource = ContactAgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
