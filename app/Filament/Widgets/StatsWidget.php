<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\Newsletter;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        return [
            Stat::make(__('Application Status'), '')
                ->label(__('App Status'))
                ->icon('heroicon-o-wifi')
                ->value(view('components.badge', [
                    'status' => Setting::select(['discovery_mode', 'maintenance_mode'])
                        ->first()
                ]))
                ->description(Setting::first()
                    ->maintenance_mode ?
                    __('Maintenance Mode is Active') : __('Application is Live'))
                ->color('secondary'),

            Stat::make('Mailing List', Newsletter::withoutTrashed()->count())
                ->icon('heroicon-o-envelope')
                ->chart($this->chartData(Newsletter::class)->toArray())
                ->description(__('Your Subscribers'))
                ->descriptionIcon('heroicon-m-inbox-stack'),

            Stat::make(__('Budgets'), Budget::withoutTrashed()->count())
                ->icon('heroicon-o-currency-dollar')
                ->chart($this->chartData(Budget::class)->toArray())
                ->description(__('Total of Budgets'))
                ->descriptionIcon('heroicon-m-wallet'),

            Stat::make(__('Customers'), Customer::withoutTrashed()->count())
                ->icon('heroicon-o-user')
                ->chart($this->chartData(Customer::class)->toArray())
                ->description(__('Total of Customers'))
                ->descriptionIcon('heroicon-m-user'),
        ];
    }

    /**
     * Summary of chartData
     * @return \Filament\Notifications\Collection
     */
    public function chartData($model)
    {
        $data = Trend::model($model)
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
