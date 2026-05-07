<?php

namespace App\Filament\Resources\BackgroundImageResource\Pages;

use App\Filament\Resources\BackgroundImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBackgroundImages extends ListRecords
{
    protected static string $resource = BackgroundImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Adicionar Fundo'),
        ];
    }
}
