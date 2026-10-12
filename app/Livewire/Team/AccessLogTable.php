<?php

namespace App\Livewire\Team;

use App\Livewire\Team\Concerns\AuthorizesTeamAccess;
use App\Services\Team\InvitationService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

/**
 * Entradas e saídas do painel (log "Access" do filament-logger) e ações sobre a
 * equipe (log "Equipe"). Nenhum desses registros guarda senha ou token.
 */
class AccessLogTable extends Component implements HasForms, HasTable
{
    use AuthorizesTeamAccess;
    use InteractsWithForms;
    use InteractsWithTable;

    /** evento => [rótulo, cor, ícone] */
    protected const EVENTS = [
        'Login' => ['Entrou', 'success', 'heroicon-m-arrow-right-end-on-rectangle'],
        'Logout' => ['Saiu', 'gray', 'heroicon-m-arrow-left-start-on-rectangle'],
        'invited' => ['Convite', 'info', 'heroicon-m-envelope'],
        'invitation_resent' => ['Convite', 'info', 'heroicon-m-envelope'],
        'invitation_revoked' => ['Convite', 'gray', 'heroicon-m-x-circle'],
        'invitation_accepted' => ['Novo membro', 'success', 'heroicon-m-user-plus'],
        'user_created' => ['Novo membro', 'success', 'heroicon-m-user-plus'],
        'role_changed' => ['Perfil', 'warning', 'heroicon-m-shield-check'],
        'role_created' => ['Perfil', 'warning', 'heroicon-m-shield-check'],
        'role_updated' => ['Perfil', 'warning', 'heroicon-m-shield-check'],
        'role_deleted' => ['Perfil', 'warning', 'heroicon-m-shield-check'],
        'user_activated' => ['Acesso', 'success', 'heroicon-m-check-circle'],
        'user_deactivated' => ['Acesso', 'danger', 'heroicon-m-no-symbol'],
        'user_deleted' => ['Remoção', 'danger', 'heroicon-m-trash'],
        'password_reset_sent' => ['Senha', 'gray', 'heroicon-m-key'],
        'password_changed_by_admin' => ['Senha', 'warning', 'heroicon-m-key'],
        'profile_password_changed' => ['Senha', 'gray', 'heroicon-m-key'],
        'profile_email_changed' => ['E-mail', 'warning', 'heroicon-m-at-symbol'],
    ];

    protected function logNames(): array
    {
        return [config('filament-logger.access.log_name', 'Access'), InvitationService::LOG];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->whereIn('log_name', $this->logNames())->with('causer'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Quando')
                    ->dateTime('d/m/Y H:i', config('app.display_timezone'))
                    ->description(fn (Activity $record) => $record->created_at->locale('pt_BR')->diffForHumans())
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('Quem')
                    ->weight('semibold')
                    ->placeholder('Sistema'),
                TextColumn::make('event')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => self::EVENTS[$state][0] ?? ($state ?: 'Ação'))
                    ->color(fn (?string $state) => self::EVENTS[$state][1] ?? 'gray')
                    ->icon(fn (?string $state) => self::EVENTS[$state][2] ?? null),
                TextColumn::make('description')
                    ->label('Detalhe')
                    ->formatStateUsing(fn (Activity $record) => match ($record->event) {
                        'Login' => 'Entrou no painel',
                        'Logout' => 'Saiu do painel',
                        default => $record->description,
                    })
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Tipo')
                    ->options([
                        config('filament-logger.access.log_name', 'Access') => 'Entradas e saídas',
                        InvitationService::LOG => 'Ações na equipe',
                    ])
                    ->placeholder('Tudo'),
            ])
            ->paginated([25, 50, 100])
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateHeading('Nenhum registro ainda')
            ->emptyStateDescription('Entradas no painel e ações na equipe aparecem aqui.');
    }

    public function render()
    {
        return view('livewire.team.table');
    }
}
