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
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->heading('Atividade Recente')
            ->description('Logins recentes e ações de usuários no sistema')
            ->headerActions([
                Action::make('view_all')
                    ->color('primary')
                    ->label('Ver Tudo')
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
                    ->label('Tipo'),

                TextColumn::make('causer_id')
                    ->label('Usuário')
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return 'Sistema';
                        }
                        $user = User::find($state);

                        return $user ? $user->name : 'Usuário Desconhecido';
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('event')
                    ->label('Ação')
                    ->formatStateUsing(function ($state, $record) {
                        // Format the event name to be more readable
                        $events = [
                            'login' => 'Login',
                            'logout' => 'Logout',
                            'created' => 'Criou',
                            'updated' => 'Editou',
                            'deleted' => 'Excluiu',
                        ];
                        
                        $formatted = $events[$state] ?? ucfirst($state);

                        if ($state === 'login' && $record->causer_id) {
                            $user = User::find($record->causer_id);
                            if ($user) {
                                return "Login realizado por {$user->name}";
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
                    ->label('Detalhes')
                    ->limit(50)
                    ->tooltip(function ($state) {
                        return strlen($state) > 50 ? $state : null;
                    }),

                TextColumn::make('created_at')
                    ->label('Quando')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),
            ]);
    }
}
