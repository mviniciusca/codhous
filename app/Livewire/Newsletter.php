<?php

namespace App\Livewire;

use App\Models\Newsletter as NewsletterModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class Newsletter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

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
                    ->required()
                    ->maxLength(150)
                    ->minLength(10)
                    ->label(__('Email'))
                    ->helperText(__('Enter with your email address'))
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        NewsletterModel::create($this->form->getState());
    }
    public function render()
    {
        return view('livewire.newsletter');
    }
}
