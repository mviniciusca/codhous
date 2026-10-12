<?php

namespace App\Notifications;

use App\Models\UserInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * E-mail com o link do convite. O token não é guardado aqui: chega pronto no link.
 */
class TeamInvitation extends Notification
{
    use Queueable;

    public function __construct(
        public UserInvitation $invitation,
        public string $url,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $app = config('app.name');
        $inviter = $this->invitation->inviter?->name;

        return (new MailMessage)
            ->subject("Convite para a equipe {$app}")
            ->greeting(filled($this->invitation->name) ? "Olá, {$this->invitation->name}!" : 'Olá!')
            ->line(($inviter ? "{$inviter} convidou você" : 'Você foi convidado')." para acessar o painel da {$app} com o perfil {$this->invitation->role->name}.")
            ->action('Criar meu acesso', $this->url)
            ->line('O link vale por '.UserInvitation::EXPIRES_IN_DAYS.' dias e só pode ser usado uma vez.')
            ->line('Se você não esperava este convite, ignore este e-mail.')
            ->salutation("Equipe {$app}");
    }
}
