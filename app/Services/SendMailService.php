<?php

namespace App\Services;

use App\Mail\Message;
use App\Models\Mail as MailModel;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class SendMailService
{
    private bool $error = false;

    /**
     * Create a new class instance.
     */
    public function __construct(public array $data) {}

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
        // Envio falhou: não registra como enviada
        if ($this->error) {
            return $this;
        }

        try {
            // Guarda o destinatário, como os demais envios (orçamentos), para o histórico do contato
            MailModel::create([
                'is_sent' => true,
                'email' => $this->data['email'],
                'name' => $this->data['name'] ?? $this->data['email'],
                'phone' => $this->data['phone'] ?? null,
                'subject' => $this->data['subject'],
                'message' => $this->data['message'],
            ]);
        } catch (Exception $e) {
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
                ->duration(10000)
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
        $this->createMail()->persistMail();

        // Erros já foram notificados no passo em que aconteceram
        if (! $this->error) {
            $this->validation();
        }

        return $this;
    }

    public function failed(): bool
    {
        return $this->error;
    }

    public function send()
    {
        return $this->save();
    }
}
