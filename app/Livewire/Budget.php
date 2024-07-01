<?php

namespace App\Livewire;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class Budget extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    public function month(): void
    {
        $this->form->fill();
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),
                MarkdownEditor::make('content'),
                // ...
            ])
            ->statePath('data');
    }
    public function create(): void
    {
        dd($this->form->getState());
    }
    public function render()
    {
        return view('livewire.budget');
    }
}
