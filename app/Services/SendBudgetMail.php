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

    public function dispatch()
    {
        $this->send();

        return $this->save();
    }

    private function send()
    {
        return Mail::to($this->destinyEmailField)
            ->send($this->mailable);
    }

    private function save()
    {
        MailModel::create([
            'name'    => env('APP_NAME') ?? 'Codhous Software',
            'is_sent' => true,
            'email'   => $this->destinyEmailField,
            'phone'   => $this->phoneField,
            'subject' => $this->subject(),
            'message' => $this->message(),
        ]);

        return $this->notification();
    }

    private function subject()
    {
        return 'Budget Notification: '.$this->state['code'];
    }

    private function message()
    {
        return 'The Document from Budget ID: #'.$this->state['code'].' was sent via email with success.';
    }

    private function notification()
    {
        return Notification::make()
            ->title(__('Document was sent via email with success.'))
            ->success()
            ->send();
    }
}
