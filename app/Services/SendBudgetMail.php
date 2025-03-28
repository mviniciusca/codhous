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
    private string $code;

    public function __construct(
        private array $state,
        private string $destinyEmailField,
        private Mailable $mailable)
    {
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
            'is_sent' => true,
            'phone'   => null,
            'name'    => env('MAIL_FROM_NAME') ?? 'Codhous Software',
            'email'   => env('MAIL_FROM_ADDRESS'),
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
        return "The document with Budget ID: #{$this->code} was sent to customer email: {$this->destinyEmailField} with success!";
    }

    private function notification()
    {
        return Notification::make()
            ->title(__('Document was sent via email with success.'))
            ->success()
            ->send();
    }
}
