<?php

namespace App\Filament\Widgets;

use App\Models\Newsletter;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SubscribersWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    protected function getStats(): array
    {
        return [
            Stat::make('Application Status', '')
                ->icon('heroicon-o-wrench-screwdriver')
                ->value(view('components.badge', [
                    'status' => Setting::select(['discovery_mode', 'maintenance_mode'])->first()
                ]))
                ->description(Setting::first()->maintenance_mode ? __('Maintenance Mode is Active') : __('Application is Live'))
                ->color('secondary'),
            Stat::make('Subscribers', $this->count())->icon('heroicon-o-wrench-screwdriver')->description('32k increase')->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Subscribers', $this->count())->icon('heroicon-o-wrench-screwdriver')->description('32k increase')->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Subscribers', $this->count())->icon('heroicon-o-wrench-screwdriver')->description('32k increase')->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }

    public function count(): ?int
    {
        return Newsletter::all()->count();
    }
}
