<?php
 
namespace App\Filament\Widgets;
 
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
 
class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-widget';
 
    protected static ?int $sort = 0;
 
    protected int | string | array $columnSpan = 'full';
 
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
        return explode(' ', Auth::user()->name)[0];
    }
}
