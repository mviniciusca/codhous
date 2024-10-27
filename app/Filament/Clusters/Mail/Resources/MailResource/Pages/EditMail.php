<?php

namespace App\Filament\Clusters\Mail\Resources\MailResource\Pages;

use App\Filament\Clusters\Mail\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMail extends EditRecord
{
    protected static string $resource = MailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
