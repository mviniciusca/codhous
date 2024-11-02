<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\BudgetHistory;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\PostcodeFinder;
use App\Trait\BudgetStatus;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CreateBudget extends CreateRecord
{
    use BudgetStatus;

    protected static string $resource = BudgetResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('Budget Overview'))
                            ->columns(4)
                            ->description(__('Organize your budget report'))
                            ->icon('heroicon-o-document')
                            ->schema([
                                Toggle::make('is_active')
                                    ->helperText(__('Enable or disable this budget from the dashboard view'))
                                    ->label(__('Active'))
                                    ->default(true)
                                    ->inline(),
                                Select::make('status')
                                    ->helperText(__('Set the budget status'))
                                    ->options([
                                        'pending'  => __('Pending'),
                                        'on going' => __('On Going'),
                                        'done'     => __('Done'),
                                        'ignored'  => __('Ignored'),
                                    ])
                                    ->afterStateUpdated(fn (Get $get, Set $set, $state): string => $this->updateBudgetStatus($get, $set, $state))
                                    ->default('pending'),
                                TextInput::make('code')
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix('#')
                                    ->label(__('Budget Code'))
                                    ->helperText(__('Use this code to search'))
                                    ->default(rand(10000, 99999)),
                                DateTimePicker::make('created_at')
                                    ->format('Y-m-d H:i:s')
                                    ->displayFormat('d/m/Y H:i')
                                    ->default(fn (): string => Carbon::now()->format('Y-m-d H:i:s'))
                                    ->label(__('Date'))
                                    ->helperText(__('When this budget was created')),
                            ]),
                    ]),
                Section::make('Budget Content')
                    ->description(__('Here is the content from your budget'))
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Fieldset::make(__('Customer Information'))
                            ->columns(3)
                            ->schema([
                                Group::make()
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('content.customer_name')
                                            ->dehydrated()
                                            ->default('' ?? env('APP_NAME'))
                                            ->required()
                                            ->helperText(__('Customer name'))
                                            ->label(__('Customer Name')),
                                        TextInput::make('content.customer_email')
                                            ->dehydrated()
                                            ->email()
                                            ->required()
                                            ->helperText(__('Customer email address'))
                                            ->label(__('Email')),
                                        TextInput::make('content.customer_phone')
                                            ->required()
                                            ->helperText(__('Phone Number'))
                                            ->tel()
                                            ->mask('(99)99999-9999')
                                            ->placeholder(_('(xx) XXXX-XXXX'))
                                            ->helperText(__('Customer phone number'))
                                            ->label(__('Phone')),
                                    ]),
                                TextInput::make('content.postcode')
                                    ->required()
                                    ->minLength(9)
                                    ->mask('99999-999')
                                    ->placeholder('22022-000')
                                    ->helperText(__('Customer postcode'))
                                    ->maxLength(9)
                                    ->suffixAction(
                                        fn ($state, Set $set, $livewire) => Action::make('search-action')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->action(function () use ($state, $livewire, $set) {
                                                $livewire->validateOnly('content.data.postcode');
                                                $postcode = new PostcodeFinder($state, $set);
                                                $postcode->find();
                                            })
                                    )
                                    ->label(__('CEP')),
                                TextInput::make('content.street')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->helperText(__('Customer street.'))
                                    ->label(__('Street')),
                                TextInput::make('content.number')
                                    ->dehydrated()
                                    ->helperText(__('Customer street number. Optional'))
                                    ->label(__('Number')),
                                TextInput::make('content.city')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(__('Customer city.'))
                                    ->label(__('City')),
                                TextInput::make('content.neighborhood')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->helperText(__('Customer neighborhood.'))
                                    ->label(__('Neighborhood')),
                                TextInput::make('content.state')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->helperText(__('Customer UF.'))
                                    ->label(__('UF')),
                            ]),
                        Fieldset::make('Construction Components')
                            ->columns(4)
                            ->schema([
                                TextInput::make('content.quantity')
                                    ->live()
                                    ->debounce(500)
                                    ->integer()
                                    ->required()
                                    ->minValue(3)
                                    ->label(__('Quantity m³'))
                                    ->suffix(__('m³'))
                                    ->helperText(__('Min value is 3 (ABNT NBR 7212)'))
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $set('quantity', $state);
                                        $this->calculateTotal($get, $set);
                                    }),
                                Select::make('content.location')
                                    ->dehydrated()
                                    ->required()
                                    ->label(__('Local / Area'))
                                    ->helperText(__('Local or area to be concreted'))
                                    ->options(Location::all()
                                        ->pluck('name', 'id')),
                                Select::make('content.product')
                                    ->live()
                                    ->dehydrated()
                                    ->required()
                                    ->label(__('Product'))
                                    ->helperText(__('Product selected'))
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('content.product_option', null);
                                        $set('content.price', null);
                                    }),
                                Select::make('content.product_option')
                                    ->live()
                                    ->dehydrated()
                                    ->label(__('Option'))
                                    ->helperText(__('Option selected'))
                                    ->options(fn (Get $get): Collection => $this->getOptions($get))
                                    ->required(fn (Get $get): bool => $this->getOptions($get)->count() > 0)
                                    ->hidden(fn (Get $get): bool => $this->getOptions($get)->count() == 0)
                                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => $this->updatePrice($get, $set, $state)),
                            ]),
                    ]),
                Section::make(__('Pricing'))
                    ->icon('heroicon-o-currency-dollar')
                    ->description(__('Pricing Definition & Total Cost'))
                    ->columns(5)
                    ->schema([
                        TextInput::make('content.quantity')
                            ->live(onBlur: true)
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->integer()
                            ->minValue(3)
                            ->suffix('m³')
                            ->helperText(__('Quantity of items'))
                            ->afterStateHydrated(fn (Get $get, Set $set) => $this->calculateTotal($get, $set))
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateTotal($get, $set))
                            ->numeric(),
                        TextInput::make('content.price')
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->label(__('Price per Unity (m³)'))
                            ->numeric()
                            ->helperText(__('Price of product in '.env('CURRENCY_SUFFIX')))
                            ->step(0.01)
                            ->afterStateHydrated(fn (Get $get, Set $set) => $this->calculateTotal($get, $set))
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateTotal($get, $set)),
                        TextInput::make('content.tax')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->prefix('+'.env('CURRENCY_SUFFIX'))
                            ->numeric()
                            ->required()
                            ->helperText(__('Sum tax or other values in '.env('CURRENCY_SUFFIX')))
                            ->step(0.01)
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                                $this->updateBudgetStatus($get, $set, $state);
                            })
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                                $this->updateBudgetStatus($get, $set, $state);
                            }),
                        TextInput::make('content.discount')
                            ->live(onBlur: true)
                            ->numeric()
                            ->required()
                            ->prefix('-'.env('CURRENCY_SUFFIX'))
                            ->helperText(__('Applies a discount in '.env('CURRENCY_SUFFIX')))
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                                $this->updateBudgetStatus($get, $set, $state);
                            })
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                                $this->updateBudgetStatus($get, $set, $state);
                            }),
                        TextInput::make('content.total')
                            ->live()
                            ->suffixAction(function () {
                                Action::make('register')
                                    ->action(function () {
                                        $this->saveAction();
                                    });
                            })
                            ->readonly()
                            ->numeric()
                            ->required()
                            ->label(__('Total Price'))
                            ->helperText(__('The total budget value in '.env('CURRENCY_SUFFIX')))
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->step(0.01),
                    ]),
            ]);
    }

    protected function afterCreate()
    {
        BudgetHistory::create([
            'budget_id' => Budget::latest('created_at')->first()->id,
            'user_id'   => Auth::user()->id,
            'action'    => 'create',
        ]);
    }

    /**
     * Summary of getOptions
     * @param Get $get
     * @return Collection
     */
    private function getOptions(Get $get): Collection
    {
        return ProductOption::where('product_id', '=', $get('content.product'))
            ->get()
            ->pluck('name', 'id');
    }

    /**
     * Summary of getPrice
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private function getPrice(Get $get, Set $set)
    {
        $id = $get('content.product_option');
        $price = ProductOption::select(['price'])
            ->where('id', '=', $id)
            ->first();
        $set('content.price', $price->price ?? 0);
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
        $set('content.price', $price);
        $this->calculateTotal($get, $set);
    }

    /**
     * Summary of calculateTotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private function calculateTotal(Get $get, Set $set): void
    {
        $quantity = floatval($get('content.quantity') ?? 0);
        $price = floatval($get('content.price') ?? 0);
        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);

        $total = $quantity * $price + $tax - $discount;
        $set('content.total', number_format($total, 2, '.', ''));
    }
}
