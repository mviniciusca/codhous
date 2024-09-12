<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
use App\Models\Newsletter;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Notifications\Collection;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SubscribersWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        return [
            Stat::make('Application Status', '')
                ->icon('heroicon-o-wrench-screwdriver')
                ->value(view('components.badge', [
                    'status' => Setting::select(['discovery_mode', 'maintenance_mode'])
                        ->first()
                ]))
                ->description(Setting::first()
                    ->maintenance_mode ?
                    __('Maintenance Mode is Active') : __('Application is Live'))
                ->color('secondary'),

            Stat::make('Subscribers', Newsletter::count())
                ->icon('heroicon-o-envelope')
                ->chart($this->chartData()->toArray())
                ->color('primary')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Subscribers', 20)
                ->icon('heroicon-o-currency-dollar')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Subscribers', 30)
                ->icon('heroicon-o-wrench-screwdriver')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }

    /**
     * Summary of chartData
     * @return \Filament\Notifications\Collection
     */
    public function chartData()
    {
        $data = Trend::model(Newsletter::class)
            ->between(
                start: now()->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        $data = $data->map(fn(TrendValue $value) => $value->aggregate);

        return $data;
    }

}
