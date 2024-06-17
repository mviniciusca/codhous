<?php

namespace App\Filament\Resources\NewsletterResource\Widgets;

use Filament\Widgets\ChartWidget;

class NewsletterOverwview extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
