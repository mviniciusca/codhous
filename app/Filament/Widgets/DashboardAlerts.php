<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class DashboardAlerts extends Widget
{
    protected static ?int $sort = 0;
    protected int|string|array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.dashboard-alerts';

    public function pageNotification()
    {
        $notification = DB::table('pages')
            ->count();
        if ($notification >= 0) {
            return $notification;
        }
    }
}
