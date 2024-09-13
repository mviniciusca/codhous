<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Get;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class MaintenanceWidget extends BaseWidget
{
    protected static ?int $sort = 6;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Setting::query()
                    ->select()
            )
            ->heading(__('Quick Access'))
            ->description(__('Manage your app visibility here.'))
            ->paginated(false)
            ->striped()
            ->columns([
                ToggleColumn::make('maintenance_mode')
                    ->label(__('Maintenance Mode')),
                ToggleColumn::make('discovery_mode')
                    ->label(__('Discovery Mode')),
            ]);
    }
}
