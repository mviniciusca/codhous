<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-user')
                ->label(__('New Customer')),
            Action::make('view_bin')
                ->label(false)
                ->icon('heroicon-o-trash')
                ->url(route('filament.admin.resources.subscribers.bin'))
                ->color('gray'),
        ];
    }
}
