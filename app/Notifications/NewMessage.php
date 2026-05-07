<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->replyTo($this->data['email'])
            ->from($this->data['email'], $this->data['name'])
            ->subject('Nova mensagem de contato: ' . $this->data['name'])
            ->greeting('Olá!')
            ->line('Você recebeu uma nova mensagem de: ' . $this->data['name'])
            ->line('Assunto: ' . ($this->data['subject'] ?? 'Sem assunto'))
            ->action('Visualizar Mensagem', url(config('app.url') . '/admin/mails/' . $this->data['id'] . '/view'))
            ->salutation('Atenciosamente, Equipe ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
