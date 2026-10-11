<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource\Concerns\InteractsWithLivePreview;
use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    use InteractsWithLivePreview;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Criar')
                ->action('create')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }
}
