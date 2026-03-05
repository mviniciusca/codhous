<?php

namespace App\Filament\Resources\ContactAgendaResource\Pages;

use App\Filament\Resources\ContactAgendaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactAgenda extends CreateRecord
{
    protected static string $resource = ContactAgendaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
