<?php

namespace App\Livewire;

use Closure;
use Exception;
use App\Models\Setting;
use Filament\Forms\Set;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Http;
use App\Models\Budget as BudgetModel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Http\Client\RequestException;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Concerns\InteractsWithForms;

class Budget extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    public $status;
    public $image;
    public function mount(): void
    {
        $this->status = $this->status();
        $this->image = $this->image();
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
                                Fieldset::make(__('Contact'))
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('content.customer_name')
                                            ->required()
                                            ->helperText(__('Your Full Name'))
                                            ->label(__('Full Name')),
                                        TextInput::make('content.customer_phone')
                                            ->required()
                                            ->helperText(__(''))
                                            ->tel()
                                            ->mask('(99)99999-9999')
                                            ->placeholder(_('(xx) XXXX-XXXX'))
                                            ->helperText(__('Your phone with local area'))
                                            ->label(__('Phone')),
                                        TextInput::make('content.customer_email')
                                            ->required()
                                            ->email()
                                            ->helperText(__('Enter your contact email'))
                                            ->label(__('Email')),
                                    ]),
                                Fieldset::make(__('Construction Dimension'))
                                    ->columns(4)
                                    ->schema([
                                        Select::make('content.fck')
                                            ->options(
                                                Setting::select(['budget'])
                                                    ->get()
                                                    ->pluck('budget.fck', 'id')
                                            )
                                            ->required()
                                            ->label(__('FCK'))
                                            ->helperText(__('Feature Compression Know')),
                                        Select::make('content.type')
                                            ->options(
                                                Setting::select(['budget'])
                                                    ->get()
                                                    ->pluck('budget.type', 'id')
                                            )
                                            ->required()
                                            ->label(__('Type of Concrete'))
                                            ->helperText(__('Type of Concrete')),
                                        Select::make('content.object')
                                            ->label(__('Local / Area'))
                                            ->options(
                                                Setting::select(['budget'])
                                                    ->get()
                                                    ->pluck('budget.area', 'id')
                                            )
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
                        Fieldset::make(__('Construction Address & Location'))
                            ->schema([
                                Group::make()
                                    ->columnSpanFull()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('content.postcode')
                                            ->required()
                                            ->minLength(9)
                                            ->mask('99999-999')
                                            ->placeholder('22022-000')
                                            ->maxLength(9)
                                            ->helperText(__('Postcode for your construction'))
                                            ->required()
                                            ->label(__('Construction Address Postcode'))
                                            ->suffixAction(
                                                fn($state, Set $set, $livewire) =>
                                                Action::make('search-action')
                                                    ->icon('heroicon-o-magnifying-glass')
                                                    ->action(function () use ($state, $livewire, $set) {
                                                        $set('content.neighborhood', null);
                                                        $set('content.street', null);
                                                        $set('content.city', null);
                                                        $set('content.state', null);
                                                        $livewire->validateOnly('data.content.postcode');
                                                        $cepData = Http::get("https://viacep.com.br/ws/{$state}/json/")
                                                            ->throw()
                                                            ->json();
                                                        if (isset($cepData['erro'])) {
                                                            throw ValidationException::withMessages([
                                                                'data.content.postcode' => __('CEP not Found'),
                                                            ]);
                                                        }
                                                        $set('content.neighborhood', $cepData['bairro'] ?? null);
                                                        $set('content.street', $cepData['logradouro'] ?? null);
                                                        $set('content.city', $cepData['localidade'] ?? null);
                                                        $set('content.state', $cepData['uf'] ?? null);
                                                    })
                                            ),
                                        TextInput::make('content.number')
                                            ->helperText(__('Number'))
                                            ->label(__('Number')),
                                        TextInput::make('content.street')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->helperText(__('Street Address'))
                                            ->label(__('Street')),
                                        TextInput::make('content.city')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->helperText(__('City'))
                                            ->label(__('City')),
                                        TextInput::make('content.neighborhood')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->helperText(__('Neighborhood'))
                                            ->label(__('Neighborhood')),
                                        TextInput::make('content.state')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->helperText(__('UF'))
                                            ->label(__('UF')),
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
            ->title(__('Thanks! Our team will answer you until 24 hours!'))
            ->send();
        $this->form->fill();
    }
    public function render()
    {
        return view('livewire.budget', [
            'status' => $this->status(),
        ]);
    }

    public function status(): bool
    {
        $status = Setting::query()
            ->select(['budget_is_active'])
            ->first()
            ->budget_is_active;
        return $status;
    }

    public function image(): string|null
    {
        $image = Setting::query()
            ->select(['budget_image'])
            ->first()
            ->budget_image;
        return $image;
    }
}
