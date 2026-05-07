<?php

namespace App\Filament\Resources\BackgroundImageResource\Pages;

use App\Filament\Resources\BackgroundImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBackgroundImage extends EditRecord
{
    protected static string $resource = BackgroundImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Excluir'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
