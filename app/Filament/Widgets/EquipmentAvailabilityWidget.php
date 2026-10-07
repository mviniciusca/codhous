<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class EquipmentAvailabilityWidget extends Widget
{
    protected static string $view = 'filament.widgets.equipment-availability-widget';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 4,
        'xl' => 4,
    ];

    protected function getViewData(): array
    {
        $available = \App\Models\Equipment::where('is_available', true)->count();
        $unavailable = \App\Models\Equipment::where('is_available', false)->count();
        
        $total = $available + $unavailable;
        
        return [
            'available' => $available,
            'rented' => 0,
            'maintenance' => 0,
            'unavailable' => $unavailable,
            'availablePercent' => $total > 0 ? round(($available / $total) * 100) : 0,
            'rentedPercent' => 0,
            'maintenancePercent' => 0,
            'unavailablePercent' => $total > 0 ? round(($unavailable / $total) * 100) : 0,
        ];
    }
}
