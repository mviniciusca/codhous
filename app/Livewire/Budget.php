<?php

namespace App\Livewire;

use App\Models\Budget as BudgetModel;
use App\Models\Location;
use App\Models\Module;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewBudget;
use App\Services\PostcodeFinder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

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
     * @param Form $form
     * @return Form
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
                                        \Filament\Forms\Components\Repeater::make('content.products')
                                            ->label(__('Product List'))
                                            ->schema([
                                                TextInput::make('quantity')
                                                    ->live(true)
                                                    ->integer()
                                                    ->required()
                                                    ->minValue(3)
                                                    ->label(__('Quantity'))
                                                    ->suffix(__('m³'))
                                                    ->helperText(__('Min value is 3 (ABNT NBR 7212)'))
                                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                                        $this->calculateTotal($get, $set);
                                                    }),
                                                Select::make('product')
                                                    ->live()
                                                    ->dehydrated()
                                                    ->required()
                                                    ->label(__('Product'))
                                                    ->helperText(__('Product selected'))
                                                    ->options(Product::all()->pluck('name', 'id'))
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        $set('product_option', null);
                                                    }),
                                                Select::make('product_option')
                                                    ->live()
                                                    ->dehydrated()
                                                    ->label(__('Option'))
                                                    ->helperText(__('Option selected'))
                                                    ->options(fn (Get $get): Collection => $this->getProductOptions($get))
                                                    ->required(fn (Get $get): bool => $this->getProductOptions($get)->count() > 0)
                                                    ->hidden(fn (Get $get): bool => $this->getProductOptions($get)->count() == 0),
                                                Select::make('location')
                                                    ->dehydrated()
                                                    ->required()
                                                    ->label(__('Local / Area'))
                                                    ->helperText(__('Local or area to be concreted'))
                                                    ->options(Location::all()
                                                        ->pluck('name', 'id')),
                                            ])
                                            ->columns(2)
                                            ->itemLabel(fn (array $state): ?string => $state['product'] ? Product::find($state['product'])?->name.' ('.($state['quantity'] ?? 0).' m³)' : null
                                            )
                                            ->addActionLabel(__('Add Product'))
                                            ->collapsible()
                                            ->reorderable()
                                            ->defaultItems(1),
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
                                                fn ($state, Set $set, $livewire) => Action::make('search-action')
                                                    ->icon('heroicon-o-magnifying-glass')
                                                    ->action(function () use ($state, $livewire, $set) {
                                                        $livewire->validateOnly('data.content.postcode');
                                                        $postcode = new PostcodeFinder($state, $set);
                                                        $postcode->find();
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
     * Summary of getProductOptions
     * @param Get $get
     * @return Collection
     */
    private function getProductOptions(Get $get): Collection
    {
        return ProductOption::where('product_id', '=', $get('product'))
            ->get()
            ->pluck('name', 'id');
    }

    /**
     * Summary of calculateTotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private function calculateTotal(Get $get, Set $set): void
    {
        $products = $get('content.products') ?? [];
        $quantity = 0;

        // Calcular a quantidade total
        foreach ($products as $product) {
            $productQuantity = floatval($product['quantity'] ?? 0);
            $quantity += $productQuantity;
        }

        // Atualizar quantidade total
        $set('content.quantity', $quantity);
    }
}
