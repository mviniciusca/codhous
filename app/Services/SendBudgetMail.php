<?php

namespace App\Services;

use App\Models\Mail as MailModel;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendBudgetMail
{
    /**
     * Create a new class instance.
     */
    private array|string|Closure $code;

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
        $this->code = $this->state['code'];
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
        return "Budget Notification: {$this->code}";
    }

    private function message()
    {
        $message = 'Hello!';
        $message .= 'The document with Budget ID: #';
        $message .= $this->code;
        $message .= ' was sent to customer email: ';
        $message .= $this->destinyEmailField;
        $message .= ' with success!';

        return $message;
    }

    private function notification()
    {
        return Notification::make()
            ->title(__('Document was sent via email with success.'))
            ->success()
            ->send();
    }
}
