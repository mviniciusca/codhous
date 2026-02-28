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
use App\Services\PostcodeFinderService;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Services\BudgetCalculatorService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class Budget extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public $status;

    public $image;

    public ?bool $module;

    public bool $isSubmitted = false;

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
        
        // Initial dispatch
        $this->dispatch('cart-updated', count: count($this->data['content']['products'] ?? []));
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
                Hidden::make('code')
                    ->default(Str::random(8)),
                
                Hidden::make('content.price'),
                Hidden::make('content.subtotal'),
                Hidden::make('content.total'),
                Hidden::make('content.quantity'),

                Fieldset::make('customer_info')
                    ->label(__('Customer Information'))
                    ->schema([
                        TextInput::make('content.customer_name')
                            ->required()
                            ->placeholder(__('Full Name *'))
                            ->hiddenLabel(),
                        TextInput::make('content.customer_phone')
                            ->required()
                            ->tel()
                            ->mask('(99)99999-9999')
                            ->placeholder(__('Phone Number *'))
                            ->hiddenLabel(),
                        TextInput::make('content.customer_email')
                            ->required()
                            ->email()
                            ->placeholder(__('Email Address *'))
                            ->hiddenLabel(),
                    ])
                    ->columns(3),

                Fieldset::make('products')
                    ->label(__('Product Selection'))
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('content.products')
                            ->label(__('Product List'))
                            ->live()
                            ->cloneable()
                            ->schema([
                                Select::make('product')                                
                                    ->live()
                                    ->dehydrated()
                                    ->required()
                                    ->placeholder(__('Select Product *'))
                                    ->hiddenLabel()
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('product_option', null);
                                        $this->dispatchCartUpdatedEvent($get);
                                    }),
                                Select::make('product_option')
                                    ->live()
                                    ->dehydrated()
                                    ->placeholder(__('Select Option'))
                                    ->hiddenLabel()
                                    ->options(fn (Get $get): Collection => $this->getProductOptions($get))
                                    ->required(fn (Get $get): bool => $this->getProductOptions($get)->count() > 0)
                                    ->hidden(fn (Get $get): bool => $this->getProductOptions($get)->count() == 0)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $this->updatePrice($get, $set, $state);
                                    }),
                                Select::make('location')
                                    ->dehydrated()
                                    ->required()
                                    ->placeholder(__('Select Area *'))
                                    ->hiddenLabel()
                                    ->options(Location::all()->pluck('name', 'id')),
                                TextInput::make('quantity')
                                    ->live(true)
                                    ->integer()
                                    ->required()
                                    ->minValue(3)
                                    ->placeholder(__('Quantity (m³) *'))
                                    ->hiddenLabel()
                                    ->suffix(__('m³'))
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $this->calculateItemSubtotal($get, $set);
                                        $this->calculateTotal($get, $set);
                                        $this->dispatchCartUpdatedEvent($get);
                                    }),
                                Hidden::make('price'),
                                Hidden::make('subtotal'),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['product'] ? Product::find($state['product'])?->name.' ('.($state['quantity'] ?? 0).' m³)' : null)
                            ->addActionLabel(__('Add Product'))
                            ->collapsible()
                            ->reorderable()
                            ->defaultItems(1),
                    ])
                    ->columns(1),

                Fieldset::make('address')
                    ->label(__('Delivery Address'))
                    ->schema([
                        TextInput::make('content.postcode')
                            ->required()
                            ->minLength(9)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, Set $set, $livewire) {
                                if (strlen($state) === 9) {
                                    $livewire->validateOnly('data.content.postcode');
                                    $postcode = new PostcodeFinderService($state, $set);
                                    $postcode->find();
                                }
                            })
                            ->mask('99999-999')
                            ->placeholder(__('Postcode (e.g. 22022-000) *'))
                            ->hiddenLabel()
                            ->maxLength(9)
                            ->suffixAction(
                                fn ($state, Set $set, $livewire) => Action::make('search-action')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->action(function () use ($state, $livewire, $set) {
                                        if (empty($state)) return;
                                        $livewire->validateOnly('data.content.postcode');
                                        $postcode = new PostcodeFinderService($state, $set);
                                        $postcode->find();
                                    })
                            ),
                        Group::make()
                            ->schema([
                                TextInput::make('content.number')
                                    ->placeholder(__('Number'))
                                    ->hiddenLabel(),
                                TextInput::make('content.street')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->placeholder(__('Street *'))
                                    ->hiddenLabel(),
                                TextInput::make('content.city')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->placeholder(__('City *'))
                                    ->hiddenLabel(),
                                TextInput::make('content.neighborhood')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->placeholder(__('Neighborhood *'))
                                    ->hiddenLabel(),
                                TextInput::make('content.state')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->placeholder(__('State *'))
                                    ->hiddenLabel(),
                            ])
                            ->columns(3)
                            ->visible(fn (Get $get) => filled($get('content.street')))
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
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

        $this->isSubmitted = true;
    }

    /**
     * Reset the form to allow for a new submission.
     */
    public function resetForm(): void
    {
        $this->form->fill();
        $this->isSubmitted = false;
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
        
        // Use the service to calculate everything
        $result = BudgetCalculatorService::calculateTotal($products);

        // Update the hidden summary fields
        $set('content.quantity', $result['quantity']);
        $set('content.price', $result['price']);
        $set('content.subtotal', $result['subtotal']);
        $set('content.total', $result['total']);
    }

    /**
     * Summary of updatePrice
     * @param Get $get
     * @param Set $set
     * @param mixed $productId
     * @return void
     */
    private function updatePrice(Get $get, Set $set, $productId): void
    {
        if ($productId) {
            $price = ProductOption::where('id', $productId)
                ->value('price') ?? 0;
        } else {
            $price = 0;
        }
        $set('price', $price);
        $this->calculateItemSubtotal($get, $set);
        $this->calculateTotal($get, $set);
    }

    /**
     * Summary of calculateItemSubtotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private function calculateItemSubtotal(Get $get, Set $set): void
    {
        $quantity = intval($get('quantity') ?? 0);
        $price = floatval($get('price') ?? 0);
        $subtotal = BudgetCalculatorService::calculateItemSubtotal($quantity, $price);
        $set('subtotal', $subtotal);
    }

    /**
     * @param $property
     */
    public function updated($property): void
    {
        if (str_contains($property, 'products')) {
            $this->dispatch('cart-updated', count: count($this->data['content']['products'] ?? []));
        }
    }

    /**
     * @param Get $get
     */
    private function dispatchCartUpdatedEvent(Get $get): void
    {
        $products = $get('content.products') ?? [];
        $this->dispatch('cart-updated', count: count($products));
    }
}
