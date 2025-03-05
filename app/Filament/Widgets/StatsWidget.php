<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Customer;
use App\Models\Newsletter;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class StatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        return [
            Stat::make(__('Application Status'), '')
                ->label(__('Website Status'))
                ->icon('heroicon-o-wifi')
                ->url(route('filament.admin.resources.settings.edit_maintenance', Setting::first()->value('id')))
                ->value(view('components.badge', [
                    'status' => Setting::select(['discovery_mode', 'maintenance_mode'])
                        ->first(),
                ]))
                ->description(Setting::first()
                    ->maintenance_mode ?
                    __('Maintenance Mode is Active') : __('Application is Live'))
                ->color('primary'),

            Stat::make('Mailing List', Newsletter::withoutTrashed()->count())
                ->icon('heroicon-o-envelope')
                ->chart($this->chartData(Newsletter::class)->toArray())
                ->color('primary')
                ->description(__('Your Subscribers'))
                ->url(route('filament.admin.resources.subscribers.index'))
                ->descriptionIcon('heroicon-m-inbox-stack'),

            Stat::make(__('Budgets'), Budget::withoutTrashed()->count())
                ->icon('heroicon-o-currency-dollar')
                ->color('primary')
                ->chart($this->chartData(Budget::class)->toArray())
                ->description(__('Total of Budgets'))
                ->url(route('filament.admin.resources.budgets.index'))
                ->descriptionIcon('heroicon-m-wallet'),

            Stat::make(__('Customers'), Customer::withoutTrashed()->count())
                ->icon('heroicon-o-user')
                ->color('primary')
                ->chart($this->chartData(Customer::class)->toArray())
                ->url(route('filament.admin.resources.customers.index'))
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

        $data = $data->map(fn (TrendValue $value) => $value->aggregate);

        return $data;
    }
}
