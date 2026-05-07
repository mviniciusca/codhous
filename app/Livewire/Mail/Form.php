<?php

namespace App\Livewire\Mail;


use App\Mail\Contact;
use App\Models\Mail as MailModel;
use App\Models\User;
use App\Notifications\NewMessage;
use App\Services\TurnstileService;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Filament\Forms\Form as FilamentForm;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class Form extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    public bool $sent = false;
    public ?string $turnstileToken = null;
    public function mount(): void
    {
        $this->form->fill();
    }
    protected function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Completo')
                            ->placeholder('Seu nome completo')
                            ->minLength(7)
                            ->maxLength(140)
                            ->required(),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->placeholder('seu@email.com')
                            ->email()
                            ->required(),
                    ]),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->placeholder('(21) 90000-0000')
                    ->mask('(99) 99999-9999')
                    ->required(),
                Select::make('subject')
                    ->label('Assunto')
                    ->options([
                        'Dúvida' => 'Dúvida',
                        'Elogio' => 'Elogio',
                        'Outro' => 'Outro',
                    ])
                    ->default('Dúvida')
                    ->required(),
                Textarea::make('message')
                    ->label('Mensagem')
                    ->placeholder('Como podemos ajudar?')
                    ->required()
                    ->minLength(20)
                    ->maxLength(2000)
                    ->rows(4)
            ])
            ->statePath('data');
    }

    /**
     * TODO: Doc this Summary of create
     * @return void
     */
    public function create(TurnstileService $turnstile): void
    {
        $data = $this->form->getState();

        // Validação Cloudflare Turnstile
        if (!$turnstile->verify($this->turnstileToken, request()->ip())) {
            $this->addError('turnstileToken', 'A verificação anti-spam falhou. Por favor, tente novamente.');
            return;
        }

        // Sanitização de campos
        $data = array_map(function($value) {
            return is_string($value) ? strip_tags($value) : $value;
        }, $data);

        $user = User::first();

        Mail::to($user->email)
            ->send(new Contact($data));

        $mail = MailModel::create($data);

        $user->notify(new NewMessage($mail->toArray()));

        Notification::make()
            ->title('Mensagem enviada com sucesso!')
            ->body('Em breve entraremos em contato com você.')
            ->success()
            ->send();
        $this->form->fill();
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
