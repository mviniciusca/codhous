<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\BudgetHistory;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\FakeBudgetDataService;
use App\Services\PostcodeFinder;
use App\Trait\BudgetStatus;
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
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CreateBudget extends CreateRecord
{
    use BudgetStatus;

    protected static string $resource = BudgetResource::class;

    protected string $js = '';

    // Propriedade para controlar a aba ativa
    public $activeTab = 'customer_information';

    // Propriedade para armazenar os produtos do carrinho
    public $cartProducts = [];

    public function mount(): void
    {
        // Verificar se há parâmetros na URL
        if (request()->has('activeTab')) {
            $this->activeTab = request('activeTab');
        }

        // Verificar se há produtos na URL
        if (request()->has('products')) {
            try {
                $productsJson = base64_decode(request('products'));
                $products = json_decode($productsJson, true);

                if (is_array($products)) {
                    // Transformar os produtos do carrinho para o formato esperado pelo orçamento
                    $formattedProducts = [];
                    foreach ($products as $product) {
                        $formattedProducts[] = [
                            'product'        => $product['product_id'],
                            'product_option' => $product['product_option_id'],
                            'quantity'       => $product['quantity'],
                            'price'          => $product['price'],
                            'subtotal'       => $product['subtotal'],
                            // Adicionar o campo location como primeiro ID disponível
                            'location' => Location::first()->id ?? 1,
                        ];
                    }
                    $this->cartProducts = $formattedProducts;
                }
            } catch (\Exception $e) {
                // Em caso de erro no processamento, apenas continua sem os produtos
                \Illuminate\Support\Facades\Log::error('Erro ao processar produtos do carrinho: '.$e->getMessage());
            }
        }

        parent::mount();

        // Após a montagem do formulário, preencher com os produtos do carrinho
        if (! empty($this->cartProducts)) {
            $this->fillFormWithCartProducts();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Gerar código único para o orçamento
        $data['code'] = Budget::generateUniqueCode();

        // Se temos produtos do carrinho e ainda não foram adicionados ao formulário
        if (! empty($this->cartProducts) && empty($data['content']['products'])) {
            $data['content']['products'] = $this->cartProducts;
            // Calcular o total baseado nos produtos
            $quantity = 0;
            $subtotal = 0;
            foreach ($this->cartProducts as $product) {
                $quantity += floatval($product['quantity'] ?? 0);
                $subtotal += floatval($product['subtotal'] ?? 0);
            }

            $data['content']['quantity'] = $quantity;
            // Incluir impostos e descontos no cálculo total
            $tax = floatval($data['content']['tax'] ?? 0);
            $discount = floatval($data['content']['discount'] ?? 0);
            $total = $subtotal + $tax - $discount;
            $data['content']['total'] = number_format($total, 2, '.', '');
        }

        return $data;
    }

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
                                    ->prefixIcon('heroicon-o-tag')
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
                                    ->placeholder('Generated automatically'),
                                DateTimePicker::make('created_at')
                                    ->format('Y-m-d H:i:s')
                                    ->displayFormat('d/m/Y H:i')
                                    ->default(fn (): string => Carbon::now()->format('Y-m-d H:i:s'))
                                    ->required()
                                    ->disabled()
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->label(__('Date'))
                                    ->helperText(__('When this budget was created')),
                            ]),
                    ]),
                Section::make('Customer Information')
                    ->description(__('This section contains information about the customer.'))
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->collapsible()
                    ->headerActions([
                        Action::make('fill_customer_data')
                            ->label(__('Quick Fill'))
                            ->icon('heroicon-o-sparkles')
                            ->color('primary')
                            ->action(function (Set $set) {
                                $fakeService = new FakeBudgetDataService();

                                // Gerar dados do cliente
                                $customerData = $fakeService->generateCustomerData();
                                foreach ($customerData as $key => $value) {
                                    $set('content.'.$key, $value);
                                }

                                // Gerar dados do endereço
                                $addressData = $fakeService->generateAddressData();
                                foreach ($addressData as $key => $value) {
                                    $set('content.'.$key, $value);
                                }

                                Notification::make()
                                    ->title(__('Customer data generated!'))
                                    ->body(__('All customer and address fields filled with test data'))
                                    ->success()
                                    ->send();
                            }),
                        Action::make('clear_customer_fields')
                            ->label(__('Clear'))
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function (Set $set) {
                                // Clear customer fields
                                $set('content.customer_name', '');
                                $set('content.customer_email', '');
                                $set('content.customer_phone', '');

                                // Clear address fields
                                $set('content.postcode', '');
                                $set('content.street', '');
                                $set('content.number', '');
                                $set('content.city', '');
                                $set('content.neighborhood', '');
                                $set('content.state', '');

                                Notification::make()
                                    ->title(__('Customer fields cleared!'))
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([Group::make()
                        ->columns(3)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('content.customer_name')
                                ->dehydrated()
                                ->default('' ?? env('APP_NAME'))
                                ->required()
                                ->prefixIcon('heroicon-o-user')
                                ->helperText(__('Customer name'))
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
                                ->tel()
                                ->prefixIcon('heroicon-o-phone')
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
                            ->required()
                            ->prefixIcon('heroicon-o-map-pin')
                            ->helperText(__('Customer neighborhood.'))
                            ->label(__('Neighborhood')),
                        TextInput::make('content.state')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->prefixIcon('heroicon-o-map-pin')
                            ->helperText(__('Customer UF.'))
                            ->label(__('UF')),
                    ]),
                Section::make(__('Shopping Bag'))
                    ->description(__('Products in the shopping bag.'))
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(8)
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
                                    ->options(fn (Get $get): Collection => $this->getOptions($get))
                                    ->required(fn (Get $get): bool => $this->getOptions($get)->count() > 0)
                                    ->hidden(fn (Get $get): bool => $this->getOptions($get)->count() == 0)
                                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => $this->updatePrice($get, $set, $state)),
                                TextInput::make('price')
                                    ->live(onBlur: true)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(__('Price of product in '.env('CURRENCY_SUFFIX')))
                                    ->afterStateHydrated(function (Get $get, Set $set) {
                                        $this->getPrice($get, $set);
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
                                    ->afterStateHydrated(fn (Get $get, Set $set) => $this->calculateItemSubtotal($get, $set)),
                            ])
                            ->columns(4)
                            ->itemLabel(fn (array $state): ?string => $state['product'] ? Product::find($state['product'])?->name.' ('.($state['quantity'] ?? 0).' m³)' : null
                            )
                            ->addActionLabel(__('Add Product'))
                            ->collapsible()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $this->calculateTotal($get, $set);
                            })
                            ->reorderable()
                            ->defaultItems(1)
                            ->columnSpanFull()
                            ->createItemButtonLabel(__('Adicionar Produto'))
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                // Add logging to debug
                                \Illuminate\Support\Facades\Log::debug('Saving repeater data:', $data);

                                return $data;
                            })
                            ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                // Add logging to debug
                                \Illuminate\Support\Facades\Log::debug('Loading repeater data:', $data);

                                return $data;
                            }),
                    ]),
                Section::make(__('Pricing Calculator'))
                    ->icon('heroicon-o-currency-dollar')
                    ->description(__('Pricing Definition & Total Cost.'))
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

    /**
     * Método para salvar os dados do orçamento e produtos relacionados
     */
    protected function handleRecordCreation(array $data): Budget
    {
        // Extrair produtos para processamento posterior
        $products = $data['content']['products'] ?? [];

        // Criar o orçamento primeiro usando o método padrão
        $budget = Budget::create($data);

        // Adicionar produtos no relacionamento muitos-para-muitos
        if (! empty($products)) {
            foreach ($products as $product) {
                if (isset($product['product']) && isset($product['quantity'])) {
                    $budget->products()->attach($product['product'], [
                        'product_option_id' => $product['product_option'] ?? null,
                        'location_id'       => $product['location'] ?? null,
                        'quantity'          => $product['quantity'] ?? 0,
                        'price'             => $product['price'] ?? 0,
                        'subtotal'          => $product['subtotal'] ?? 0,
                    ]);
                }
            }

            // Atualizar os totais no campo content se necessário
            $totalQuantity = $budget->products()->sum('quantity');
            $subtotal = $budget->products()->sum('subtotal');

            $tax = floatval($data['content']['tax'] ?? 0);
            $discount = floatval($data['content']['discount'] ?? 0);
            $total = $subtotal + $tax - $discount;

            $content = $budget->content;
            $content['quantity'] = $totalQuantity;
            $content['total'] = number_format($total, 2, '.', '');

            // Atualizar o orçamento com os valores atualizados
            $budget->update(['content' => $content]);
        }

        return $budget;
    }

    protected function afterCreate(): void
    {
        // Registrar na história do orçamento
        BudgetHistory::create([
            'budget_id' => $this->record->id,
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
    private function getPrice(Get $get, Set $set)
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
    private function updatePrice(Get $get, Set $set, $productId): void
    {
        if ($productId) {
            $price = ProductOption::where('id', $productId)
                ->value('price') ?? 0;
        } else {
            $price = 0;
        }
        $set('price', $price);
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
     * Summary of calculateItemSubtotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private function calculateItemSubtotal(Get $get, Set $set): void
    {
        $quantity = floatval($get('quantity') ?? 0);
        $price = floatval($get('price') ?? 0);
        $subtotal = $quantity * $price;
        $set('subtotal', number_format($subtotal, 2, '.', ''));
    }

    /**
     * Preenche o formulário com os produtos do carrinho
     */
    private function fillFormWithCartProducts(): void
    {
        // Verificamos se já temos acesso ao form
        if ($this->form && ! empty($this->cartProducts)) {
            try {
                // Remover o item padrão que o repeater cria automaticamente
                $currentData = $this->form->getRawState();
                if (! isset($currentData['content'])) {
                    $currentData['content'] = [];
                }

                // Preencher o formulário com os produtos do carrinho
                $currentData['content']['products'] = $this->cartProducts;

                // Calcular totais
                $quantity = 0;
                $subtotal = 0;
                foreach ($this->cartProducts as $product) {
                    $quantity += floatval($product['quantity']);
                    $subtotal += floatval($product['subtotal']);
                }

                // Atualizar totais
                $currentData['content']['quantity'] = $quantity;
                $currentData['content']['total'] = number_format($subtotal, 2, '.', '');

                // Preencher o formulário com os novos dados
                $this->form->fill($currentData);

                // Registrar para debug
                \Illuminate\Support\Facades\Log::info('Formulário preenchido com produtos do carrinho', [
                    'productsCount' => count($this->cartProducts),
                    'totalQuantity' => $quantity,
                    'totalValue'    => $subtotal,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erro ao preencher formulário com produtos: '.$e->getMessage());
            }
        }
    }

    /**
     * Método executado antes de preencher o formulário
     * Usado para configurar scripts personalizados para abrir a aba correta
     */
    protected function beforeFill(): void
    {
        // Adicione um script para navegar automaticamente para a aba "Shopping Bag" quando necessário
        $this->js = <<<'JS'
            document.addEventListener('DOMContentLoaded', function() {
                // Verificar se estamos na página de criação de orçamento
                if (window.location.href.includes('budgets/create')) {
                    // Verificar se há o parâmetro activeTab=shopping_bag na URL
                    const params = new URLSearchParams(window.location.search);
                    const activeTab = params.get('activeTab');

                    if (activeTab === 'shopping_bag') {
                        // Pequeno atraso para garantir que o Filament já renderizou as abas
                        setTimeout(function() {
                            // Encontrar e clicar na aba "Shopping Bag"
                            const shoppingBagTab = Array.from(document.querySelectorAll('button')).find(
                                button => button.textContent.includes('Shopping Bag')
                            );

                            if (shoppingBagTab) {
                                shoppingBagTab.click();
                            }
                        }, 500);
                    }
                }
            });
        JS;
    }
}
