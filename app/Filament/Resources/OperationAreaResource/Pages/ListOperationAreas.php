<?php

namespace App\Filament\Resources\OperationAreaResource\Pages;

use App\Filament\Resources\OperationAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperationAreas extends ListRecords
{
    protected static string $resource = OperationAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                // use a supported icon to avoid SvgNotFound exception
                ->icon('heroicon-o-map')
                ->label(__('New Operation Area')),
        ];
    }
}
