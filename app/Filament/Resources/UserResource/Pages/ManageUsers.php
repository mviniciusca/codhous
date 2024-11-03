<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Team Manager');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalHeading(__('Team Member'))
                ->label(__('New Member'))
                ->icon('heroicon-o-user'),
        ];
    }
}
