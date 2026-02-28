<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use App\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ActivityWidget extends BaseWidget
{
    protected static ?int $sort = 11;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->heading(__('Recent Site Activity'))
            ->description(__('Recent logins and user actions on the system'))
            ->headerActions([
                Action::make('view_all')
                    ->color('primary')
                    ->label(__('View All'))
                    ->icon('heroicon-o-arrow-up-right')
                    ->url(route('filament.admin.resources.activity-logs.index')),
            ])
            ->query(
                ActivityLog::query()
                    ->latest()
                    ->take(5)
            )
            ->columns([
                TextColumn::make('log_name')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Access'       => 'danger',
                        'Resource'     => 'success',
                        'Model'        => 'warning',
                        'Notification' => 'info',
                        default        => 'gray',
                    })
                    ->label(__('Type')),

                TextColumn::make('causer_id')
                    ->label(__('User'))
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return __('System');
                        }
                        $user = User::find($state);

                        return $user ? $user->name : __('Unknown User');
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('event')
                    ->label(__('Action'))
                    ->formatStateUsing(function ($state, $record) {
                        // Format the event name to be more readable
                        $formatted = ucfirst($state);

                        // For login events, add more context
                        if ($state === 'login' && $record->causer_id) {
                            $user = User::find($record->causer_id);
                            if ($user) {
                                return __(':action by :user', [
                                    'action' => $formatted,
                                    'user'   => $user->name,
                                ]);
                            }
                        }

                        return $formatted;
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'login'   => 'heroicon-o-lock-open',
                        'logout'  => 'heroicon-o-lock-closed',
                        'created' => 'heroicon-o-plus-circle',
                        'updated' => 'heroicon-o-pencil',
                        'deleted' => 'heroicon-o-trash',
                        default   => 'heroicon-o-document-text',
                    })
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('Details'))
                    ->limit(50)
                    ->tooltip(function ($state) {
                        return strlen($state) > 50 ? $state : null;
                    }),

                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),
            ]);
    }
}
