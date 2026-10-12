<?php

namespace App\Services\Team;

use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Ações administrativas sobre usuários. Cada método confere a policy de novo
 * (defesa em profundidade, além da ação do Filament) e registra no log "Equipe".
 */
class TeamService
{
    public function changeRole(User $user, Role $role): void
    {
        Gate::authorize('changeRole', $user);

        $previous = $user->role()?->name;
        if ($previous === $role->name) {
            return;
        }

        $user->syncRoles([$role]);

        $this->log($user, 'role_changed', "Alterou o perfil de {$user->name}: ".($previous ?? 'nenhum')." → {$role->name}", [
            'old' => $previous,
            'new' => $role->name,
        ]);
    }

    public function setActive(User $user, bool $active): void
    {
        Gate::authorize('toggleActive', $user);

        $user->forceFill(['is_active' => $active])->save();

        if (! $active) {
            $this->endSessions($user);
        }

        $this->log($user, $active ? 'user_activated' : 'user_deactivated', ($active ? 'Reativou' : 'Desativou')." a conta de {$user->name}");
    }

    public function delete(User $user): void
    {
        Gate::authorize('delete', $user);

        $this->log($user, 'user_deleted', "Removeu a conta de {$user->name} ({$user->email})");
        $this->endSessions($user);
        $user->delete();
    }

    /**
     * Envia o link de redefinição de senha do próprio painel.
     */
    public function sendPasswordReset(User $user): string
    {
        Gate::authorize('update', $user);

        $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
            ['email' => $user->email],
            function (User $user, string $token): void {
                $notification = new ResetPassword($token);
                $notification->url = Filament::getResetPasswordUrl($token, $user);
                $user->notify($notification);
            },
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->log($user, 'password_reset_sent', "Enviou link de redefinição de senha para {$user->name}");
        }

        return $status;
    }

    /**
     * Encerra sessões abertas (driver database) e invalida o "lembrar de mim".
     */
    public function endSessions(User $user): void
    {
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->delete();
        }

        $user->forceFill(['remember_token' => Str::random(60)])->saveQuietly();
    }

    public function log(User $subject, string $event, string $description, array $properties = []): void
    {
        activity(InvitationService::LOG)
            ->causedBy(auth()->user())
            ->performedOn($subject)
            ->event($event)
            ->withProperties($properties)
            ->log($description);
    }
}
