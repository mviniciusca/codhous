<?php

namespace App\Livewire;

use App\Models\Newsletter as NewsletterModel;
use App\Models\User;
use App\Notifications\NewSubscribe;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class Newsletter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public ?string $title;
    public ?string $subtitle;
    public ?string $info;
    public ?string $btn_text;
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(150)
                    ->minLength(10)
                    ->hiddenLabel()
                    ->placeholder(__('Name')),
                TextInput::make('email')
                    ->email()
                    ->unique(table: NewsletterModel::class)
                    ->required()
                    ->maxLength(150)
                    ->minLength(10)
                    ->hiddenLabel()
                    ->placeholder(__('Email'))
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $subscribe = NewsletterModel::create($this->form->getState());

        $user = new User();
        $user->first()->notify(new NewSubscribe($subscribe->toArray()));

        Notification::make()
            ->title(__('Thanks for subscribe!'))
            ->success()
            ->send();
        $this->form->fill();
    }
    public function render()
    {
        return view('livewire.newsletter');
    }
}
