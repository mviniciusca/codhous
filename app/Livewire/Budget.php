<?php

namespace App\Livewire;

use App\Models\Budget as BudgetModel;
use Filament\Forms\Components\Fieldset;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class Budget extends Component implements HasForms
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
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Hidden::make('code')
                                    ->default(Str::random(8)),
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
                                                'usi' => __('Type 1'),
                                                'bom' => __('Type 2'),
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
                            ->schema([
                                Group::make()
                                    ->columnSpanFull()
                                    ->columns(4)
                                    ->schema([
                                        TextInput::make('content.cep')
                                            ->required()
                                            ->minLength(9)
                                            ->mask('99999-999')
                                            ->placeholder('22022-000')
                                            ->maxLength(9)
                                            ->helperText(__(''))
                                            ->required()
                                            ->label(__('Address CEP')),
                                        TextInput::make('content.customer_name')
                                            ->required()
                                            ->helperText(__(''))
                                            ->label(__('Name')),
                                        TextInput::make('content.customer_phone')
                                            ->required()
                                            ->helperText(__(''))
                                            ->tel()
                                            ->mask('(99)99999-9999')
                                            ->placeholder(_('(DD)XXXX-XXXX'))
                                            ->label(__('Phone')),
                                        TextInput::make('content.customer_email')
                                            ->required()
                                            ->helperText(__(''))
                                            ->label(__('Email')),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }
    public function create(): void
    {
        BudgetModel::create($this->form->getState());
        Notification::make()
            ->title(__('Thanks for send your budget. Our team will answer you until 24 hours!'))
            ->send();
        $this->form->fill();
    }
    public function render()
    {
        return view('livewire.budget');
    }
}
