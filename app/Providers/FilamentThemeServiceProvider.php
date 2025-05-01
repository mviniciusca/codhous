<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

class FilamentThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentColor::register([
            'custom'    => Color::hex('#22c55e'),
            'danger'    => Color::hex('#e11d48'),
            'gray'      => Color::hex('#475569'),
            'info'      => Color::hex('#3b82f6'),
            'primary'   => Color::hex('#16a34a'),
            'secondary' => Color::hex('#0ea5e9'),
            'success'   => Color::hex('#22c55e'),
            'warning'   => Color::hex('#f59e0b'),
        ]);
    }
}
