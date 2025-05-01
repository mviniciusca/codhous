<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Component;

class Invoice extends Component
{
    protected string $view = 'forms.components.invoice';

    public static function make(): static
    {
        return app(static::class);
    }
}
