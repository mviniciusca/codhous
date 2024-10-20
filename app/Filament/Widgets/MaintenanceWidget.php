<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
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
            ->headerActions([
                Action::make('edit')
                    ->label(__('Security'))
                    ->icon('heroicon-o-shield-check')
                    ->color('primary')
                    ->url(route('filament.admin.resources.settings.edit_maintenance', Setting::first()->id))
            ])
            ->columns([
                ToggleColumn::make('maintenance_mode')
                    ->label(__('Maintenance Mode')),
                ToggleColumn::make('discovery_mode')
                    ->label(__('Discovery Mode')),
            ]);
    }
}
