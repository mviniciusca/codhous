<?php

namespace App\Livewire\Mail;

use App\Models\Mail;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Livewire\Component;
use Filament\Forms\Form as FilamentForm;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

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
                    ->required(),
                TextInput::make('email')
                    ->label(__('Email'))
                    ->required(),
                TextInput::make('phone')
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
                    ->label(__('Message'))
                    ->rows(3)
            ])
            ->statePath('data');
    }
    public function create(): void
    {
        Mail::create($this->form->getState());
        // Reinitialize the form to clear its data.
        $this->form->fill();
    }
    public function render()
    {
        return view('livewire.mail.form');
    }
}
