<?php

namespace App\Livewire;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
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
                Section::make(__('Budget Order'))
                    ->icon('heroicon-m-shopping-bag')
                    ->description('Review your basket')
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->columns(4)
                                    ->schema([
                                        Select::make('content.type')
                                            ->label(__('Cement Type'))
                                            ->options([
                                                'usinado' => __('Usinado'),
                                                'convencional' => __('Convencional'),
                                            ])
                                            ->default('usinado'),
                                        Select::make('content.object')
                                            ->options([
                                                'pool' => __('Pool'),
                                                'wall' => __('Wall'),
                                                'floor' => __('Floor'),
                                                'foundation' => __('Foundation'),
                                                'other' => __('Other'),
                                            ]),
                                        TextInput::make('content.quantity')
                                            ->label(__('Quantity m³'))
                                            ->numeric()
                                            ->minValue(3)
                                            ->helperText(__('Quantity. In meters cubic (m³). Min value is 3 ABNT NBR 7212')),
                                        Checkbox::make('content.quantity'),
                                        TextInput::make('content.cep')
                                            ->numeric()
                                    ])
                            ]),
                    ]),
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
