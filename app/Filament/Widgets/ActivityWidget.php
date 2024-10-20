<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;

class ActivityWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->heading(__('Access Recent Activity'))
            ->headerActions([
                Action::make('view_all')
                    ->color('primary')
                    ->label(__('View All'))
                    ->icon('heroicon-o-arrow-up-right')
                    ->url(route('filament.admin.resources.activity-logs.index'))
            ])
            ->query(
                ActivityLog::query()
                    ->where('event', '=', 'login')
                    ->take(5)
            )
            ->columns([
                TextColumn::make('log_name')->badge(),
                TextColumn::make('event'),
                TextColumn::make('description'),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i'),
            ]);
    }
}
