<?php

namespace App\Filament\Resources\NewsletterResource\Widgets;

use App\Models\Newsletter;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class NewsletterOverwview extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $data = Trend::model(Newsletter::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear()
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => __('Subscribers'),
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ]
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
