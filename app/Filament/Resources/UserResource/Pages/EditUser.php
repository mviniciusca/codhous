<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets\UserStatsWidget;
use App\Models\Role;
use App\Models\User;
use App\Services\Team\TeamService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Gate;

/**
 * Detalhes de um membro da equipe. A própria conta não abre aqui (policy update):
 * é editada em “Meu perfil”, que pede a senha atual.
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        return $this->record->email.' · '.($this->record->role()?->name ?? 'Sem perfil').($this->record->is_active ? '' : ' · acesso desativado');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['role_id'] = $this->record->role()?->getKey();

        return $data;
    }

    protected function afterSave(): void
    {
        $roleId = $this->data['role_id'] ?? null;

        if ($roleId && (int) $roleId !== $this->record->role()?->getKey() && Gate::allows('changeRole', $this->record)) {
            app(TeamService::class)->changeRole($this->record, Role::findOrFail($roleId));
        }

        if (filled($this->data['password'] ?? null)) {
            app(TeamService::class)->log($this->record, 'password_changed_by_admin', "Definiu uma nova senha para {$this->record->name}");
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleActive')
                ->label(fn (User $record) => $record->is_active ? 'Desativar acesso' : 'Reativar acesso')
                ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                ->color('gray')
                ->disabled(fn (User $record) => Gate::denies('toggleActive', $record))
                ->tooltip(fn (User $record) => Gate::inspect('toggleActive', $record)->message())
                ->requiresConfirmation()
                ->modalHeading(fn (User $record) => ($record->is_active ? 'Desativar' : 'Reativar').' este acesso?')
                ->modalDescription(fn (User $record) => $record->is_active
                    ? 'A pessoa sai do painel na hora. O histórico e os orçamentos continuam.'
                    : 'A pessoa volta a entrar com o perfil atual.')
                ->action(function (User $record) {
                    app(TeamService::class)->setActive($record, ! $record->is_active);
                    Notification::make()->title($record->is_active ? 'Acesso reativado' : 'Acesso desativado')->success()->send();
                }),
            Actions\Action::make('delete')
                ->label('Remover')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->disabled(fn (User $record) => Gate::denies('delete', $record))
                ->tooltip(fn (User $record) => Gate::inspect('delete', $record)->message())
                ->requiresConfirmation()
                ->modalHeading('Remover este usuário?')
                ->modalDescription('A conta é apagada de vez. Para só bloquear o acesso, use “Desativar acesso”.')
                ->modalSubmitActionLabel('Remover')
                ->action(function (User $record) {
                    app(TeamService::class)->delete($record);
                    Notification::make()->title('Usuário removido')->success()->send();
                    $this->redirect(UserResource::getUrl('index'));
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsWidget::class,
        ];
    }
}
