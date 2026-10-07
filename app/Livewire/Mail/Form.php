<?php

namespace App\Livewire\Mail;

use App\Mail\Contact;
use App\Models\Mail as MailModel;
use App\Models\User;
use App\Notifications\NewMessage;
use App\Services\TurnstileService;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Form extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $subject = 'Dúvida';
    public string $message = '';
    
    public bool $sent = false;
    public ?string $turnstileToken = null;

    protected $rules = [
        'name' => 'required|min:7|max:140',
        'email' => 'required|email',
        'phone' => 'required',
        'subject' => 'required',
        'message' => 'required|min:20|max:2000',
    ];

    public function create(TurnstileService $turnstile): void
    {
        $this->validate();

        // Validação Cloudflare Turnstile
        if (!$turnstile->verify($this->turnstileToken, request()->ip())) {
            $this->addError('turnstileToken', 'A verificação anti-spam falhou. Por favor, tente novamente.');
            return;
        }

        $data = [
            'name' => strip_tags($this->name),
            'email' => strip_tags($this->email),
            'phone' => strip_tags($this->phone),
            'subject' => strip_tags($this->subject),
            'message' => strip_tags($this->message),
        ];

        $user = User::first();

        if ($user && $user->email) {
            Mail::to($user->email)->send(new Contact($data));
            
            $mail = MailModel::create($data);
            $user->notify(new NewMessage($mail->toArray()));
        }

        $this->reset(['name', 'email', 'phone', 'subject', 'message']);
        $this->sent = true;
    }

    public function render(TurnstileService $turnstile)
    {
        return view('livewire.mail.form', [
            'turnstileEnabled' => $turnstile->isEnabled(),
            'turnstileSiteKey' => $turnstile->getSiteKey(),
        ]);
    }
}
