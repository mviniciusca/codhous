<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class BudgetsByStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.budgets-by-status-widget';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 4,
        'xl' => 4,
    ];

    protected function getViewData(): array
    {
        $pending = \App\Models\Budget::where('status', 'pending')->count();
        $onGoing = \App\Models\Budget::where('status', 'on going')->count();
        $done = \App\Models\Budget::where('status', 'done')->count();
        $ignored = \App\Models\Budget::where('status', 'ignored')->count();
        
        $total = $pending + $onGoing + $done + $ignored;
        
        return [
            'pending' => $pending,
            'onGoing' => $onGoing,
            'done' => $done,
            'ignored' => $ignored,
            'pendingPercent' => $total > 0 ? round(($pending / $total) * 100) : 0,
            'onGoingPercent' => $total > 0 ? round(($onGoing / $total) * 100) : 0,
            'donePercent' => $total > 0 ? round(($done / $total) * 100) : 0,
            'ignoredPercent' => $total > 0 ? round(($ignored / $total) * 100) : 0,
        ];
    }
}
