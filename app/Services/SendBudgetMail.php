<?php

namespace App\Services;

use App\Models\Mail as MailModel;
use Filament\Notifications\Notification;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendBudgetMail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private array $state,
        private string $destinyEmailField,
        private string $phoneField,
        private Mailable $mailable)
    {
        $this->state = $state;
        $this->mailable = $mailable;
        $this->destinyEmailField = $destinyEmailField;
        $this->phoneField = $phoneField;
    }

    private function dispatch()
    {
        $this->send();

        return $this->save();
    }

    private function send()
    {
        return Mail::to($this->state[$this->destinyEmailField])
            ->send($this->mailable);
    }

    private function save()
    {
        return $this->notification();
    }

    private function subject()
    {
        return 'Budget Notification: '.$this->state['code'];
    }

    private function message()
    {
        return 'Budget Notification: '.$this->state['code'].'was sent via email with success.';
    }

    private function notification()
    {
        return Notification::make()
            ->title(__('Document was sent via email with success.'))
            ->success()
            ->send();
    }
}
