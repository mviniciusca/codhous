<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Customer;
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

            Stat::make('Mailing List', Newsletter::count())
                ->icon('heroicon-o-envelope')
                ->chart($this->chartData()->toArray())
                ->color('primary')
                ->description(__('Your Subscribers'))
                ->descriptionIcon('heroicon-m-user'),

            Stat::make('Budgets', Budget::count())
                ->icon('heroicon-o-currency-dollar')
                ->description(__('Total of Budgets'))
                ->descriptionIcon('heroicon-m-wallet')
                ->color('primary'),

            Stat::make(__('Customers'), Customer::count())
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
