<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendBudgetMail
{
    /**
     * Create a new class instance.
     */
    private array $state;

    private string $field;

    private Mailable $mailable;

    public function __construct()
    {
        //
    }

    private function send()
    {
        Mail::to($this->state[$this->field])
        ->send($this->mailable);
    }
}
