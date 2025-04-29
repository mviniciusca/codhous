<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Mail\BudgetMail;
use App\Models\Budget;
use App\Models\BudgetHistory;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\PdfGenerator;
use App\Services\PostcodeFinder;
use App\Services\SendBudgetMail;
use App\Trait\BudgetStatus;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EditBudget extends EditRecord
{
    use BudgetStatus;

    protected static string $resource = BudgetResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Budget Details');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('Budget Overview'))
                            ->headerActions([
                                Action::make('send_mail')
                                    ->icon('heroicon-o-envelope')
                                    ->label(__('Notify Email'))
                                    ->disabled(function (Get $get, ?array $state) {
                                        return self::checkId($get, $state);
                                    })
                                    ->color(function (Get $get, ?array $state) {
                                        if (self::checkId($get, $state)) {
                                            return 'gray';
                                        } else {
                                            return 'primary';
                                        }
                                    })
                                    ->requiresConfirmation()
                                    ->action(function (Get $get, ?array $state) {
                                        $mail = new SendBudgetMail($state,
                                            $get('content.customer_email'),
                                            new BudgetMail()
                                        );
                                        $mail->dispatch();
                                    }),
                                Action::make('download_pdf')
                                    ->label(__('Download PDF'))
                                    ->color(function (Get $get, ?array $state) {
                                        if (self::checkId($get, $state)) {
                                            return 'gray';
                                        } else {
                                            return 'primary';
                                        }
                                    })
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->requiresConfirmation()
                                    ->disabled(function (Get $get, ?array $state) {
                                        return self::checkId($get, $state);
                                    })
                                    ->action(function ($state) {
                                        $pdf = new PdfGenerator($state);

                                        return $pdf->generate();
                                    }),
                            ])
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
                                    ->prefixIcon('heroicon-o-tag')
                                    ->options([
                                        'pending'  => __('Pending'),
                                        'on going' => __('On Going'),
                                        'done'     => __('Done'),
                                        'ignored'  => __('Ignored'),
                                    ])
                                    ->default('pending'),
                                TextInput::make('code')
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix('#')
                                    ->label(__('Budget Code'))
                                    ->helperText(__('Use this code to search'))
                                    ->default('ADMIN'.rand(10000, 99999)),
                                DateTimePicker::make('created_at')
                                    ->disabled()
                                    ->dehydrated()
                                    ->format('Y-m-d H:i:s')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->displayFormat('d/m/Y H:i')
                                    ->default(fn () => Carbon::now()->format('Y-m-d H:i:s'))
                                    ->label(__('Date'))
                                    ->helperText(__('When this budget was created')),
                            ]),
                    ]),
                Section::make('Customer Information')
                    ->description(__('This section contains information about the customer.'))
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
                        Group::make()
                            ->columns(3)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('content.customer_name')
                                    ->dehydrated()
                                    ->required()
                                    ->prefixIcon('heroicon-o-user')
                                    ->helperText(__('Customer name'))
                                    ->default('Admin')
                                    ->label(__('Customer Name')),
                                TextInput::make('content.customer_email')
                                    ->dehydrated()
                                    ->email()
                                    ->required()
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->helperText(__('Customer email address'))
                                    ->label(__('Email')),
                                TextInput::make('content.customer_phone')
                                    ->required()
                                    ->helperText(__('Phone Number'))
                                    ->prefixIcon('heroicon-o-phone')
                                    ->tel()
                                    ->mask('(99)99999-9999')
                                    ->placeholder(_('(--) ---- ----'))
                                    ->helperText(__('Customer phone number'))
                                    ->label(__('Phone')),
                            ]),
                        TextInput::make('content.postcode')
                            ->required()
                            ->minLength(9)
                            ->mask('99999-999')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->placeholder('----- ---')
                            ->helperText(__('Customer postcode'))
                            ->maxLength(9)
                            ->suffixAction(
                                fn ($state, Set $set, $livewire) => Action::make('search-action')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->action(function () use ($state, $livewire, $set) {
                                        $livewire->validateOnly('data.content.postcode');
                                        $postcode = new PostcodeFinder($state, $set);
                                        $postcode->find();
                                    })
                            )
                            ->label(__('CEP')),
                        TextInput::make('content.street')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->prefixIcon('heroicon-o-map-pin')
                            ->helperText(__('Customer street.'))
                            ->label(__('Street')),
                        TextInput::make('content.number')
                            ->dehydrated()
                            ->prefixIcon('heroicon-o-map-pin')
                            ->helperText(__('Customer street number. Optional'))
                            ->label(__('Number')),
                        TextInput::make('content.city')
                            ->disabled()
                            ->dehydrated()
                            ->prefixIcon('heroicon-o-map-pin')
                            ->helperText(__('Customer city.'))
                            ->label(__('City')),
                        TextInput::make('content.neighborhood')
                            ->disabled()
                            ->dehydrated()
                            ->prefixIcon('heroicon-o-map-pin')
                            ->helperText(__('Customer neighborhood.'))
                            ->label(__('Neighborhood')),
                        TextInput::make('content.state')
                            ->disabled()
                            ->dehydrated()
                            ->prefixIcon('heroicon-o-map-pin')
                            ->helperText(__('Customer UF.'))
                            ->label(__('State')),

                    ]),
                Section::make('Budget Content')
                    ->description(__('Here is the content from your budget'))
                    ->icon('heroicon-o-shopping-bag')
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
                                        self::calculateTotal($get, $set);
                                    }),
                                Select::make('location')
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(2)
                                    ->label(__('Local / Area'))
                                    ->helperText(__('Local or area to be concreted'))
                                    ->searchable()
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->options(Location::all()
                                        ->pluck('name', 'id')),
                                Select::make('product')
                                    ->live()
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(2)
                                    ->searchable()
                                    ->prefixIcon('heroicon-o-shopping-cart')
                                    ->label(__('Product'))
                                    ->helperText(__('Product selected'))
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('product_option', null);
                                        $set('price', null);
                                    }),
                                Select::make('product_option')
                                    ->live()
                                    ->dehydrated()
                                    ->searchable()
                                    ->prefixIcon('heroicon-o-shopping-bag')
                                    ->columnSpan(2)
                                    ->label(__('Option'))
                                    ->helperText(__('Option selected'))
                                    ->options(fn (Get $get): Collection => self::getOptions($get))
                                    ->required(fn (Get $get): bool => self::getOptions($get)->count() > 0)
                                    ->hidden(fn (Get $get): bool => self::getOptions($get)->count() == 0)
                                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => self::updatePrice($get, $set, $state)),
                                TextInput::make('price')
                                    ->live(onBlur: true)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(__('Price of product in '.env('CURRENCY_SUFFIX')))
                                    ->afterStateHydrated(function (Get $get, Set $set) {
                                        self::getPrice($get, $set);
                                    })
                                    ->prefix(env('CURRENCY_SUFFIX'))
                                    ->label(__('Price per Unity'))
                                    ->required(),
                                TextInput::make('subtotal')
                                    ->live()
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix(env('CURRENCY_SUFFIX'))
                                    ->label(__('Subtotal'))
                                    ->helperText(__('Product quantity x price'))
                                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateItemSubtotal($get, $set)),
                            ])
                            ->columns(4)
                            ->itemLabel(fn (array $state): ?string => $state['product'] ? Product::find($state['product'])?->name.' ('.($state['quantity'] ?? 0).' m³)' : null
                            )
                            ->addActionLabel(__('Add Product'))
                            ->collapsible()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateTotal($get, $set);
                            })
                            ->reorderable()
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Pricing'))
                    ->icon('heroicon-o-currency-dollar')
                    ->description(__('Pricing Definition & Total Cost'))
                    ->collapsible()
                    ->columns(5)
                    ->schema([
                        TextInput::make('content.quantity')
                            ->live(onBlur: true)
                            ->disabled()
                            ->dehydrated()
                            ->readonly()
                            ->required()
                            ->suffix('m³')
                            ->label(__('Total Quantity'))
                            ->helperText(__('Total quantity for all products'))
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.tax')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->prefix('+'.env('CURRENCY_SUFFIX'))
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->helperText(__('Sum tax or other values in '.env('CURRENCY_SUFFIX')))
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.discount')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('-'.env('CURRENCY_SUFFIX'))
                            ->helperText(__('Applies a discount in '.env('CURRENCY_SUFFIX')))
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.total')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->label(__('Total Price'))
                            ->helperText(__('The total budget value in '.env('CURRENCY_SUFFIX')))
                            ->suffixAction(function () {
                                return Action::make('calculator')
                                    ->icon('heroicon-o-calculator')
                                    ->color('gray')
                                    ->disabled()
                                    ->visible(true)
                                    ->action(fn () => self::generateCode($this->data));
                            }),
                    ]),
            ]);
    }

    protected function afterSave()
    {
        BudgetHistory::create([
            'budget_id' => $this->data['id'],
            'user_id'   => Auth::user()->id,
            'action'    => 'update',
        ]);
    }

    /**
     * Summary of calculateTotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private static function calculateTotal(Get $get, Set $set): void
    {
        $products = $get('content.products') ?? [];
        $subtotal = 0;
        $quantity = 0;

        // Calcular subtotais de cada produto
        foreach ($products as $index => $product) {
            $productQuantity = floatval($product['quantity'] ?? 0);
            $productPrice = floatval($product['price'] ?? 0);
            $itemSubtotal = $productQuantity * $productPrice;

            // Atualizar subtotal do item
            $set("content.products.{$index}.subtotal", number_format($itemSubtotal, 2, '.', ''));

            // Somar ao total geral
            $subtotal += $itemSubtotal;
            $quantity += $productQuantity;
        }

        // Atualizar total de todos os produtos
        $set('content.quantity', $quantity);

        // Aplicar taxas e descontos
        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);

        $total = $subtotal + $tax - $discount;
        $set('content.total', number_format($total, 2, '.', ''));
    }

    /**
     * Summary de calculateItemSubtotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private static function calculateItemSubtotal(Get $get, Set $set): void
    {
        $quantity = floatval($get('quantity') ?? 0);
        $price = floatval($get('price') ?? 0);
        $subtotal = $quantity * $price;
        $set('subtotal', number_format($subtotal, 2, '.', ''));
    }

    /**
     * Summary of getOptions
     * @param Get $get
     * @return Collection
     */
    private static function getOptions(Get $get): Collection
    {
        // Corrigir o caminho para obter o ID do produto
        return ProductOption::where('product_id', '=', $get('product'))
            ->get()
            ->pluck('name', 'id');
    }

    /**
     * Summary of getPrice
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private static function getPrice(Get $get, Set $set): void
    {
        $id = $get('product_option');
        $price = ProductOption::select(['price'])
            ->where('id', '=', $id)
            ->first();
        $set('price', $price->price ?? 0);
    }

    /**
     * Summary of updatePrice
     * @param Get $get
     * @param Set $set
     * @param mixed $productId
     * @return void
     */
    private static function updatePrice(Get $get, Set $set, $productId): void
    {
        if ($productId) {
            $price = ProductOption::where('id', $productId)
                ->value('price') ?? 0;
        } else {
            $price = 0;
        }
        $set('price', $price);
        self::calculateTotal($get, $set);
    }

    /**
     * Summary of checkId
     * @param Get $get
     * @param mixed $state
     * @return bool
     */
    private function checkId(Get $get, ?array $state): bool
    {
        $field = Budget::select('content')
            ->where('id', '=', $get('id'))
            ->first()
            ->content;

        $discount = $field['discount'] ?? null;
        $tax = $field['tax'] ?? null;

        if ($discount === null or $tax === null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Summary of getHeaderActions
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('new_budget')
                ->label(__('New Budget'))
                ->color('primary')
                ->url(route('filament.admin.resources.budgets.create'))
                ->icon('heroicon-o-currency-dollar'),

        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getDeleteFormAction(),

        ];
    }

    protected function getDeleteFormAction(): DeleteAction
    {
        return DeleteAction::make('delete')
            ->requiresConfirmation()
            ->icon('heroicon-o-trash')
            ->label(__('Delete'));
    }
}
