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
            ->subject(ucfirst($this->data['subject']) . __(' message from website'))
            ->greeting(__('Hello'))
            ->line(__('New message for you from ') . $this->data['name'])
            ->line(__('Phone: ') . $this->data['phone'])
            ->line($this->data['message'])
            ->salutation(__('Have a nice day!'))
            ->action(__('View on App'), url(env('APP_URL') . '/admin/mails/' . $this->data['id'] . '/view'));
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
