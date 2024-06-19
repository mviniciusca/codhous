<?php

namespace App\Livewire\Mail;

use App\Models\Mail as MailModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
    public function form(FilamentForm $form): FilamentForm
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
    public function create(): void
    {
        MailModel::create($this->form->getState());
        $this->form->fill();
        Notification::make()
            ->title(__('Message Sent'))
            ->success()
            ->send();
    }
    public function render()
    {
        return view('livewire.mail.form');
    }
}
