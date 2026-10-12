<?php

namespace App\Services\Team;

use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\TeamInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Convites da equipe: criar, reenviar, revogar e aceitar.
 * Toda ação fica no registro de atividades (log "Equipe"), sem tokens nem senhas.
 */
class InvitationService
{
    public const LOG = 'Equipe';

    public function invite(string $email, ?string $name, Role $role, User $inviter): UserInvitation
    {
        $email = Str::lower(trim($email));

        if (User::query()->where('email', $email)->exists()) {
            throw ValidationException::withMessages(['email' => 'Já existe um usuário com este e-mail.']);
        }

        if (UserInvitation::query()->pending()->where('email', $email)->exists()) {
            throw ValidationException::withMessages(['email' => 'Já existe um convite pendente para este e-mail. Use “Reenviar”.']);
        }

        // Convites vencidos para o mesmo e-mail saem da lista
        UserInvitation::query()->open()->where('email', $email)->delete();

        $invitation = new UserInvitation(['email' => $email, 'name' => $name ?: null]);
        $invitation->role()->associate($role);
        $invitation->inviter()->associate($inviter);
        $token = $invitation->issueToken();

        $this->send($invitation, $token);
        $this->log($invitation, 'invited', "Convidou {$email} como {$role->name}");

        return $invitation;
    }

    public function resend(UserInvitation $invitation): void
    {
        $token = $invitation->issueToken();

        $this->send($invitation, $token);
        $this->log($invitation, 'invitation_resent', "Reenviou o convite de {$invitation->email}");
    }

    public function revoke(UserInvitation $invitation): void
    {
        $this->log($invitation, 'invitation_revoked', "Revogou o convite de {$invitation->email}");
        $invitation->delete();
    }

    /**
     * Cria o usuário com o perfil do convite e encerra o convite.
     */
    public function accept(UserInvitation $invitation, string $name, string $password): User
    {
        return DB::transaction(function () use ($invitation, $name, $password) {
            // Trava a linha: dois envios simultâneos não criam dois usuários
            $invitation = UserInvitation::query()->pending()->lockForUpdate()->findOrFail($invitation->getKey());

            if (User::query()->where('email', $invitation->email)->exists()) {
                throw ValidationException::withMessages(['email' => 'Este e-mail já tem acesso ao painel.']);
            }

            $user = new User(['name' => $name, 'email' => $invitation->email, 'password' => $password]);
            $user->forceFill(['is_active' => true, 'email_verified_at' => now()])->save();
            $user->syncRoles([$invitation->role]);

            $invitation->forceFill(['accepted_at' => now()])->save();

            activity(self::LOG)
                ->causedBy($user)
                ->performedOn($user)
                ->event('invitation_accepted')
                ->withProperties(['role' => $invitation->role->name])
                ->log("Aceitou o convite e entrou como {$invitation->role->name}");

            return $user;
        });
    }

    protected function send(UserInvitation $invitation, string $token): void
    {
        Notification::route('mail', $invitation->email)
            ->notify(new TeamInvitation($invitation->loadMissing('role', 'inviter'), $invitation->url($token)));
    }

    protected function log(UserInvitation $invitation, string $event, string $description): void
    {
        activity(self::LOG)
            ->causedBy(auth()->user())
            ->event($event)
            ->withProperties(['email' => $invitation->email, 'role' => $invitation->role?->name])
            ->log($description);
    }
}
