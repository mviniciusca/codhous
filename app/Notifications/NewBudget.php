<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBudget extends Notification
{
    use Queueable;

    public ?array $budget;

    /**
     * Create a new notification instance.
     */
    public function __construct($budget)
    {
        $this->budget = $budget;
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
            ->subject(__('New Budget Offer. ID:' . $this->budget['id']))
            ->greeting(__('Hey!'))
            ->line(__('You\'ve received a new budget offer.'))
            ->line(__('Code: ' . $this->budget['code']))
            ->line(__('Customer: ' . $this->budget['content']['customer_name']))
            ->action(__('View Now'), url(env('APP_URL') . '/admin/budgets/' . $this->budget['id'] . '/edit'))
            ->line(__('Check it out in your Dashboard'));
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
