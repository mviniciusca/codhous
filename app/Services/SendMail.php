<?php

namespace App\Services;

use App\Mail\Message;
use App\Models\Mail as MailModel;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class SendMail
{
    private string $fromEmail;

    private bool $error = false;

    /**
     * Create a new class instance.
     */
    public function __construct(public array $data)
    {
        $this->fromEmail = env('MAIL_FROM_ADDRESS');
    }

    private function createMail()
    {
        try {
            Mail::to($this->data['email'])
                ->send(new Message($this->data));
        } catch (Exception $e) {
            $this->error = true;

            return $this->validation(__('Error on sending: ').$e->getMessage());
        }

        return $this;
    }

    private function persistMail()
    {
        try {
            MailModel::create([
                'is_sent' => true,
                'email'   => $this->fromEmail,
                'name'    => $this->data['name'],
                'subject' => $this->data['subject'],
                'message' => $this->data['message'],
            ]);
        } catch(Exception $e) {
            $this->error = true;

            return $this->validation(__('Error on sending: ').$e->getMessage());
        }

        return $this;
    }

    private function validation(?string $message = null)
    {
        if ($this->error) {
            Notification::make()
                ->danger()
                ->duration(12000)
                ->title(__($message ?? __('Message was not sent. Try again!')))
                ->send();
        } else {
            Notification::make()
                ->success()
                ->title(__($message ?? __('Message was sent with success!')))
                ->send();
        }

        return $this;
    }

    private function save()
    {
        $this->createMail()->persistMail()->validation();

        return $this;
    }

    public function send()
    {
        return $this->save();
    }
}
