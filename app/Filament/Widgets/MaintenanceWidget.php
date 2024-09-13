<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Setting;
use Filament\Tables\Table;
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
                    ->take(5)
            )
            ->paginated(false)
            ->striped()
            ->columns([
                // ...
            ]);
    }
}
