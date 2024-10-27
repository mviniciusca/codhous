<?php

namespace App\Services;

use App\Models\Mail as MailModel;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendBudgetMail
{
    /**
     * Create a new class instance.
     */
    public function __construct(private array $state, private string $destinyEmailField, private Mailable $mailable)
    {
        $this->state = $state;
        $this->mailable = $mailable;
        $this->destinyEmailField = $destinyEmailField;
    }

    private function send()
    {
        Mail::to($this->state[$this->destinyEmailField])
            ->send($this->mailable);
    }

    private function save()
    {
        MailModel::create([
            'name'    => env('APP_NAME') ?? 'Codhous Software',
            'is_sent' => true,
        ]);
    }
}
