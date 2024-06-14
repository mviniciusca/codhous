<?php

namespace App\Livewire;

use App\Models\Newsletter as NewsletterModel;
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
            ->schema([
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
        NewsletterModel::create($this->form->getState());
        $this->form->fill();
        Notification::make()
            ->title(__('Thanks for subscribe!'))
            ->success()
            ->send();
    }
    public function render()
    {
        return view('livewire.newsletter');
    }
}
