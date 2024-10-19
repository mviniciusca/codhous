<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMail extends EditRecord
{
    protected static string $resource = MailResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Important';
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
