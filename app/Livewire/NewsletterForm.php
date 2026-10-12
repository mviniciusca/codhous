<?php

namespace App\Livewire;

use App\Models\Newsletter;
use App\Services\TurnstileService;
use Livewire\Component;

class NewsletterForm extends Component
{
    public string $name = '';
    public string $email = '';
    public bool $sent = false;
    public ?string $turnstileToken = null;

    protected $rules = [
        'name' => 'required|min:3|max:140',
        'email' => 'required|email|unique:newsletters,email',
    ];

    public function subscribe(TurnstileService $turnstile): void
    {
        $this->validate();

        if (!$turnstile->verify($this->turnstileToken, request()->ip())) {
            $this->addError('turnstileToken', 'A verificação anti-spam falhou. Por favor, tente novamente.');
            return;
        }

        Newsletter::create([
            'name' => strip_tags($this->name),
            'email' => strip_tags($this->email),
            'is_active' => true,
        ]);

        $this->reset(['name', 'email']);
        $this->sent = true;
    }

    public function render(TurnstileService $turnstile)
    {
        return view('livewire.newsletter-form', [
            'turnstileEnabled' => $turnstile->isEnabled(),
            'turnstileSiteKey' => $turnstile->getSiteKey(),
        ]);
    }
}
