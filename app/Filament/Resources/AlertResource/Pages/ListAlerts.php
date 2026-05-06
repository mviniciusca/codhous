<?php

namespace App\Filament\Resources\AlertResource\Pages;

use App\Filament\Resources\AlertResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlerts extends ListRecords
{
    protected static string $resource = AlertResource::class;

    public function getTitle(): string 
    {
        return 'Alertas e Notificações';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie avisos, banners promocionais e mensagens de sistema que aparecem para os usuários no site.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Alerta')
                ->icon('heroicon-o-plus'),
        ];
    }
}
