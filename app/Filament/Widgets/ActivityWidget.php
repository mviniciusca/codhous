<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class ActivityWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->where('event', '=', 'login')
                    ->take(5)
            )
            ->columns([
                TextColumn::make('event'),
                TextColumn::make('description'),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i'),
            ]);
    }
}
