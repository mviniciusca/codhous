<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = '';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string $view = 'filament.pages.dashboard';

    public function getGreeting(): string
    {
        $hour = now()->setTimezone('America/Sao_Paulo')->hour;
        if ($hour >= 5 && $hour < 12) {
            return 'Bom dia';
        }
        if ($hour >= 12 && $hour < 18) {
            return 'Boa tarde';
        }
        return 'Boa noite';
    }

    public function getUserName(): string
    {
        return explode(' ', auth()->user()?->name ?? 'Usuário')[0];
    }

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'md' => 6,
            'xl' => 12,
        ];
    }
}
