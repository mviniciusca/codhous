<?php

namespace App\Livewire\Team;

use App\Livewire\Team\Concerns\AuthorizesTeamAccess;
use App\Models\UserInvitation;
use App\Services\Team\InvitationService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

/**
 * Convites ainda não aceitos (pendentes e vencidos).
 */
class InvitationsTable extends Component implements HasActions, HasForms, HasTable
{
    use AuthorizesTeamAccess;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    protected $listeners = ['invitation-sent' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(UserInvitation::query()->open()->with('role', 'inviter'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->label('Convidado')
                    ->weight('semibold')
                    ->description(fn (UserInvitation $record) => $record->name)
                    ->searchable(['email', 'name']),
                TextColumn::make('role.name')
                    ->label('Perfil')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('inviter.name')
                    ->label('Convidado por')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (UserInvitation $record) => $record->isExpired() ? 'Expirado' : 'Aguardando')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Expirado' ? 'danger' : 'warning')
                    ->icon(fn (string $state) => $state === 'Expirado' ? 'heroicon-m-exclamation-circle' : 'heroicon-m-clock'),
                TextColumn::make('expires_at')
                    ->label('Validade')
                    ->formatStateUsing(fn (UserInvitation $record) => $record->isExpired()
                        ? 'Venceu '.$record->expires_at->locale('pt_BR')->diffForHumans()
                        : 'Vence '.$record->expires_at->locale('pt_BR')->diffForHumans())
                    ->dateTimeTooltip('d/m/Y H:i', config('app.display_timezone'))
                    ->sortable(),
            ])
            ->actions([
                Action::make('resend')
                    ->label('Reenviar')
                    ->icon('heroicon-m-paper-airplane')
                    ->requiresConfirmation()
                    ->modalHeading('Reenviar convite?')
                    ->modalDescription(fn (UserInvitation $record) => "Um link novo vai para {$record->email}, válido por ".UserInvitation::EXPIRES_IN_DAYS.' dias. O link anterior deixa de funcionar.')
                    ->modalSubmitActionLabel('Reenviar')
                    ->action(function (UserInvitation $record) {
                        app(InvitationService::class)->resend($record);
                        Notification::make()->title('Convite reenviado')->success()->send();
                    }),
                Action::make('revoke')
                    ->label('Revogar')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Revogar convite?')
                    ->modalDescription(fn (UserInvitation $record) => "O link enviado para {$record->email} deixa de funcionar.")
                    ->modalSubmitActionLabel('Revogar')
                    ->action(function (UserInvitation $record) {
                        app(InvitationService::class)->revoke($record);
                        Notification::make()->title('Convite revogado')->success()->send();
                    }),
            ])
            ->paginated([10, 25, 50])
            ->emptyStateIcon('heroicon-o-envelope-open')
            ->emptyStateHeading('Nenhum convite pendente')
            ->emptyStateDescription('Convites enviados aparecem aqui até serem aceitos.');
    }

    public function render()
    {
        return view('livewire.team.table');
    }
}
