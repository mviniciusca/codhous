<?php

namespace App\Filament\Resources\BackgroundImageResource\Pages;

use App\Filament\Resources\BackgroundImageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBackgroundImage extends CreateRecord
{
    protected static string $resource = BackgroundImageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
