<?php

namespace App\Livewire;

use Filament\Forms\Components\Fieldset;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

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
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Fieldset::make(__('Construction Dimension'))
                                    ->columns(4)
                                    ->schema([
                                        Select::make('content.fck')
                                            ->options([
                                                15 => 15,
                                                20 => 20,
                                                25 => 25,
                                                30 => 30,
                                                35 => 35,
                                                40 => 40
                                            ])
                                            ->required()
                                            ->label(__('FCK (Feature Compression Know)'))
                                            ->helperText(__('Feature Compression Know')),
                                        Select::make('content.type')
                                            ->options([
                                                1 => __('Type 1'),
                                                2 => __('Type 2'),
                                            ])
                                            ->required()
                                            ->label(__('Type of Concrete'))
                                            ->helperText(__('Type of Concrete')),
                                        Select::make('content.object')
                                            ->label(__('Local / Area'))
                                            ->options([
                                                'pool' => __('Pool'),
                                                'wall' => __('Wall'),
                                                'floor' => __('Floor'),
                                                'foundation' => __('Foundation'),
                                                'other' => __('Other'),
                                            ])
                                            ->required()
                                            ->helperText(__('Local or area to be concreted')),
                                        TextInput::make('content.quantity')
                                            ->label(__('Estimative Quantity m³'))
                                            ->numeric()
                                            ->default(3)
                                            ->minValue(3)
                                            ->required()
                                            ->helperText(__('Min value is 3 (ABNT NBR 7212)')),
                                    ]),
                            ]),
                        Fieldset::make(__('Address & Contact'))
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
