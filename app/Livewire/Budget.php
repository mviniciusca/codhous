<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Module;
use App\Models\Product;
use App\Models\Setting;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;
use App\Models\Location;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use App\Models\ProductOption;
use App\Notifications\NewBudget;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Http;
use App\Models\Budget as BudgetModel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Concerns\InteractsWithForms;

class Budget extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    public $status;
    public $image;
    public ?bool $module;

    /**
     * Summary of mount
     * @return void
     */
    public function mount(): void
    {
        $this->status = $this->status();
        $this->image = $this->image();
        $this->module = $this->module();
        $this->form->fill();
    }

    /**
     * Summary of module
     * @return mixed
     */
    public function module()
    {
        return Module::first()->module['budget'];
    }

    /**
     * Summary of form
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
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
                                            ->helperText(__('Phone Number'))
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
                                        Select::make('content.product')
                                            ->live()
                                            ->options(
                                                Product::all()
                                                    ->pluck('name', 'id')
                                            )
                                            ->required()
                                            ->label(__('Type of Concrete'))
                                            ->helperText(__('Type of Concrete')),
                                        Select::make('content.product_option')
                                            ->live()
                                            ->options(function (Get $get) {
                                                return $this->getOptions($get);
                                            })
                                            ->required(function (Get $get) {
                                                return $this->getOptions($get)->count() > 0;
                                            })
                                            ->hidden(function (Get $get) {
                                                return $this->getOptions($get)->count() == 0;
                                            })
                                            ->label(__('Option'))
                                            ->helperText(__('Product Option')),
                                        Select::make('content.location')
                                            ->label(__('Location / Area'))
                                            ->options(
                                                Location::all()->pluck('name', 'id')
                                            )
                                            ->required()
                                            ->helperText(__('Local or area to be concreted')),
                                        TextInput::make('content.quantity')
                                            ->label(__('Estimative Quantity m³'))
                                            ->numeric()
                                            ->default(3)
                                            ->suffix('m³')
                                            ->minValue(3)
                                            ->required()
                                            ->helperText(__('Min value 3. (ABNT NBR 7212)')),
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
                                            ->label(__('Construction Address Postcode'))
                                            ->suffixAction(
                                                fn($state, Set $set, $livewire) =>
                                                Action::make('search-action')
                                                    ->icon('heroicon-o-magnifying-glass')
                                                    ->action(function () use ($state, $livewire, $set) {
                                                        $set('content.neighborhood', null);
                                                        $set('content.street', null);
                                                        $set('content.number', null);
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
                                            ->helperText(__('State'))
                                            ->label(__('State')),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * Summary of create
     * @return void
     */
    public function create(): void
    {
        $budget = BudgetModel::create($this->form->getState());

        $user = new User();
        $user->first()->notify(new NewBudget($budget->toArray()));

        Notification::make()
            ->title(__('Thanks! Our team will answer you until 24 hours!'))
            ->success()
            ->send();

        $this->form->fill();
    }


    /**
     * Summary of render
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.budget', [
            'status' => $this->status(),
        ]);
    }


    /**
     * Summary of status
     * @return bool
     */
    public function status(): bool
    {
        $status = Setting::query()
            ->select(['budget_is_active'])
            ->first()
            ->budget_is_active;
        return $status;
    }


    /**
     * Summary of image
     * @return string|null
     */
    public function image(): string|null
    {
        $image = Setting::query()
            ->select(['budget_image'])
            ->first()
            ->budget_image;
        return $image;
    }

    /**
     * Summary of getOptions
     * @param \Filament\Forms\Get $get
     * @return \Illuminate\Support\Collection
     */
    private function getOptions(Get $get): Collection
    {
        return ProductOption::where('product_id', '=', $get('content.product'))
            ->get()
            ->pluck('name', 'id');
    }

}
