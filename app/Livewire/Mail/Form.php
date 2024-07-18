<?php

namespace App\Livewire\Mail;

use App\Mail\Contact;
use App\Models\Mail as MailModel;
use App\Models\User;
use App\Notifications\NewMessage;
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
    public function mount(): void
    {
        $this->form->fill();
    }
    protected function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Full Name'))
                    ->minLength(6)
                    ->maxLength(140)
                    ->required(),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->minLength(5)
                    ->maxLength(200)
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->minLength(8)
                    ->maxLength(200)
                    ->label(__('Phone'))
                    ->mask('(99)99999-9999')
                    ->required(),
                Select::make('subject')
                    ->required()
                    ->options([
                        'compliment' => __('Compliment'),
                        'business' => __('Business'),
                        'complaint' => __('Complaint'),
                    ])
                    ->default('business')
                    ->label(__('Subject')),
                Textarea::make('message')
                    ->required()
                    ->minLength(20)
                    ->maxLength(600)
                    ->label(__('Message'))
                    ->rows(3)
            ])
            ->statePath('data');
    }

    /**
     * TODO: Doc this Summary of create
     * @return void
     */
    public function create(): void
    {
        $data = $this->form->getState();
        $mail = MailModel::create($data);

        $user = User::first();
        $user->notify(new NewMessage($mail->toArray()));

        Notification::make()
            ->title(__('Message Sent'))
            ->success()
            ->send();
        $this->form->fill();
    }
    public function render()
    {
        return view('livewire.mail.form');
    }
}
