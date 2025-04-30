<?php

namespace App\Filament\Widgets;

use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Widgets\Widget;

class CalculatorWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.calculator-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public ?array $data = [];

    public array $cart = [];

    public float $cartTotal = 0;

    public function mount(): void
    {
        $this->form->fill([
            'content' => [
                'tax'      => 0,
                'discount' => 0,
                'quantity' => 3,
            ],
        ]);
    }

    public function addToCart()
    {
        $productId = $this->data['content']['product'] ?? null;
        $productOptionId = $this->data['content']['product_option'] ?? null;
        $quantity = floatval($this->data['content']['quantity'] ?? 3);
        $price = floatval($this->data['content']['price'] ?? 0);

        // Verificar se temos um produto selecionado
        if (! $productId) {
            $this->dispatch('notify', [
                'style'   => 'danger',
                'message' => 'Selecione um produto para adicionar ao carrinho.',
            ]);

            return;
        }

        // Verificar se o produto tem variações disponíveis
        $hasOptions = ProductOption::where('product_id', $productId)->count() > 0;

        // Se o produto tem variações, mas nenhuma foi selecionada
        if ($hasOptions && ! $productOptionId) {
            $this->dispatch('notify', [
                'style'   => 'danger',
                'message' => 'É necessário selecionar uma variação do produto.',
            ]);

            return;
        }

        // Verificar se o preço é maior que zero
        if ($price <= 0) {
            $this->dispatch('notify', [
                'style'   => 'danger',
                'message' => 'Não é possível adicionar produto com preço R$ 0,00. Verifique a configuração do produto.',
            ]);

            return;
        }

        // Obter informações do produto e opção
        $product = Product::find($productId);
        $productOption = $productOptionId ? ProductOption::find($productOptionId) : null;

        // Verificar se este produto+variação já existe no carrinho
        $existingItemIndex = $this->findProductInCart($productId, $productOptionId);

        if ($existingItemIndex !== false) {
            // Se já existe, apenas incrementa a quantidade e recalcula o subtotal
            $this->cart[$existingItemIndex]['quantity'] += $quantity;
            $this->cart[$existingItemIndex]['subtotal'] = $this->cart[$existingItemIndex]['quantity'] * $this->cart[$existingItemIndex]['price'];
        } else {
            // Se não existe, adiciona como novo item
            $this->cart[] = [
                'product_id'          => $productId,
                'product_name'        => $product ? $product->name : 'Produto',
                'product_option_id'   => $productOptionId,
                'product_option_name' => $productOption ? $productOption->name : null,
                'quantity'            => $quantity,
                'price'               => $price,
                'subtotal'            => $quantity * $price,
            ];
        }

        // Recalcular o total do carrinho
        $this->calculateCartTotal();

        // Limpar o formulário para um novo produto
        $this->form->fill([
            'content' => [
                'product'        => null,
                'product_option' => null,
                'quantity'       => 3,
                'price'          => 0,
                'tax'            => $this->data['content']['tax'] ?? 0,
                'discount'       => $this->data['content']['discount'] ?? 0,
                'total'          => 0,
            ],
        ]);

        // Notificar o usuário que o produto foi adicionado com sucesso
        $this->dispatch('notify', [
            'style'   => 'success',
            'message' => 'Produto adicionado ao carrinho!',
        ]);
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Reindexar o array
            $this->calculateCartTotal();
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->cartTotal = 0;

        // Atualizar o campo de total do carrinho na interface
        $this->dispatch('notify', [
            'style'   => 'success',
            'message' => 'Carrinho limpo com sucesso!',
        ]);

        // Também manter as taxas e descontos, apenas zerar os produtos e o total
        $this->form->fill([
            'content' => [
                'tax'        => $this->data['content']['tax'] ?? 0,
                'discount'   => $this->data['content']['discount'] ?? 0,
                'cart_total' => '0.00',
            ],
        ]);
    }

    private function calculateCartTotal()
    {
        $this->cartTotal = 0;
        foreach ($this->cart as $item) {
            $this->cartTotal += floatval($item['subtotal']);
        }

        // Aplicar impostos e descontos
        $tax = floatval($this->data['content']['tax'] ?? 0);
        $discount = floatval($this->data['content']['discount'] ?? 0);

        $this->cartTotal = $this->cartTotal + $tax - $discount;
        $this->cartTotal = max(0, $this->cartTotal); // Garantir que não seja negativo
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Section::make(__('Calculadora de Orçamento'))
                    ->description(__('Adicione produtos ao carrinho para gerar um orçamento rápido.'))
                    ->icon('heroicon-o-calculator')
                    ->columnSpan(1)
                    ->schema([
                        Grid::make(4) // Alterado para 4 colunas em vez de 2
                            ->schema([
                                Select::make('content.product')
                                    ->live()
                                    ->label(__('Produto'))
                                    ->helperText(__('Tipo de Concreto'))
                                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                    ->afterStateHydrated(function (Get $get, Set $set, $state) {
                                        $this->updatePrice($get, $set, $state);
                                    })
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $set('content.product_option', null); // Reset da opção ao mudar o produto
                                        $this->updatePrice($get, $set, $state);
                                    })
                                    ->required()
                                    ->dehydrated()
                                    ->columnSpan(2), // Ocupa 2 de 4 colunas

                                Select::make('content.product_option')
                                    ->live()
                                    ->label(__('Variação'))
                                    ->helperText(__('Escolha uma variação'))
                                    ->options(function (Get $get) {
                                        $productId = $get('content.product');
                                        if (! $productId) {
                                            return [];
                                        }

                                        return ProductOption::where('product_id', $productId)
                                            ->pluck('name', 'id');
                                    })
                                    ->hidden(function (Get $get) {
                                        $productId = $get('content.product');
                                        if (! $productId) {
                                            return true;
                                        }

                                        return ProductOption::where('product_id', $productId)->count() === 0;
                                    })
                                    ->required(function (Get $get) {
                                        $productId = $get('content.product');
                                        if (! $productId) {
                                            return false;
                                        }

                                        return ProductOption::where('product_id', $productId)->count() > 0;
                                    })
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        if ($state) {
                                            $price = ProductOption::find($state)->price ?? 0;
                                            $set('content.price', $price);
                                            $this->calculateTotal($get, $set);
                                        }
                                    })
                                    ->dehydrated()
                                    ->columnSpan(2), // Ocupa 2 de 4 colunas

                                TextInput::make('content.quantity')
                                    ->live(onBlur: true)
                                    ->dehydrated()
                                    ->required()
                                    ->integer()
                                    ->minValue(3)
                                    ->default(3)
                                    ->suffix('m³')
                                    ->placeholder('3')
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $this->calculateTotal($get, $set);
                                    })
                                    ->numeric()
                                    ->columnSpan(1), // Ocupa 1 de 4 colunas

                                TextInput::make('content.price')
                                    ->live()
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix(env('CURRENCY_SUFFIX'))
                                    ->label(__('Preço'))
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->step(0.01)
                                    ->columnSpan(1), // Ocupa 1 de 4 colunas

                                TextInput::make('content.tax')
                                    ->live(onBlur: true)
                                    ->dehydrated()
                                    ->prefix('+'.env('CURRENCY_SUFFIX'))
                                    ->label(__('Taxa'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->placeholder('0.00')
                                    ->step(0.01)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $this->calculateTotal($get, $set);
                                        $this->calculateCartTotal();
                                    })
                                    ->columnSpan(1), // Ocupa 1 de 4 colunas

                                TextInput::make('content.discount')
                                    ->live(onBlur: true)
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->prefix('-'.env('CURRENCY_SUFFIX'))
                                    ->label(__('Desconto'))
                                    ->placeholder('0.00')
                                    ->step(0.01)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $this->calculateTotal($get, $set);
                                        $this->calculateCartTotal();
                                    })
                                    ->columnSpan(1), // Ocupa 1 de 4 colunas

                                TextInput::make('content.total')
                                    ->live()
                                    ->readonly()
                                    ->numeric()
                                    ->required()
                                    ->label(__('Preço Total'))
                                    ->placeholder('0.00')
                                    ->prefix(env('CURRENCY_SUFFIX'))
                                    ->step(0.01)
                                    ->columnSpan(1), // Ocupa 1 de 4 colunas

                                \Filament\Forms\Components\Actions::make([
                                    Action::make('addToCart')
                                        ->label(__('Adicionar ao Carrinho'))
                                        ->icon('heroicon-m-shopping-cart')
                                        ->color('primary')
                                        ->disabled(fn (Get $get): bool => ! $get('content.product'))
                                        ->action(fn () => $this->addToCart()),
                                ])
                                    ->columnSpan(4), // Ocupa toda a linha
                            ]),
                    ]),

                Section::make(__('Carrinho'))
                    ->description(__('Produtos adicionados ao orçamento'))
                    ->icon('heroicon-o-shopping-bag')
                    ->columnSpan(1)
                    ->schema([
                        ViewField::make('cart_items')
                            ->view('filament.widgets.cart-items'),

                        TextInput::make('cart_total')
                            ->label(__('Total do Carrinho'))
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->disabled()
                            ->extraInputAttributes(['style' => 'width: 120px;'])
                            ->placeholder('0.00')
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                $set('cart_total', number_format($this->cartTotal, 2, '.', ''));
                            })
                            ->dehydrated(false),

                        \Filament\Forms\Components\Actions::make([
                            Action::make('clearCart')
                                ->label(__('Limpar Carrinho'))
                                ->icon('heroicon-m-trash')
                                ->color('danger')
                                ->disabled(fn (): bool => empty($this->cart))
                                ->action(fn () => $this->clearCart()),

                            Action::make('createBudget')
                                ->label(__('Gerar Orçamento'))
                                ->icon('heroicon-m-document-text')
                                ->color('success')
                                ->url(function () {
                                    // Verificar se há produtos no carrinho
                                    if (empty($this->cart)) {
                                        return route('filament.admin.resources.budgets.create');
                                    }

                                    // Transformar o carrinho em um formato compatível com o orçamento
                                    $productsJson = json_encode($this->cart);

                                    // Gerar URL com os produtos do carrinho como parâmetros e indicação para abrir a aba Shopping Bag
                                    return route('filament.admin.resources.budgets.create', [
                                        'activeTab' => 'shopping_bag',
                                        'products'  => base64_encode($productsJson),
                                    ]);
                                })
                                ->disabled(fn (): bool => empty($this->cart)),
                        ])->columnSpanFull(),
                    ]),
            ]),
        ])
            ->statePath('data');
    }

    private function updatePrice(Get $get, Set $set, $productId): void
    {
        if ($productId) {
            $productOptionId = $get('content.product_option');

            if ($productOptionId) {
                // Se tiver uma opção de produto selecionada, usa o preço da opção
                $price = ProductOption::find($productOptionId)->price ?? 0;
            } else {
                // Se não tiver opção, tenta pegar o preço da primeira opção
                $price = ProductOption::where('product_id', $productId)->value('price');

                // Se não tiver opções, tenta pegar o preço diretamente do produto
                if ($price === null) {
                    $product = Product::find($productId);
                    $price = $product ? $product->price : 0;
                }
            }
        } else {
            $price = 0;
        }

        $set('content.price', $price);
        $this->calculateTotal($get, $set);
    }

    private function calculateTotal(Get $get, Set $set): void
    {
        $quantity = floatval($get('content.quantity') ?? 3);
        $price = floatval($get('content.price') ?? 0);
        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);

        // Garantindo que os valores não sejam negativos
        $quantity = max(3, $quantity);
        $tax = max(0, $tax);
        $discount = max(0, $discount);

        $total = ($quantity * $price) + $tax - $discount;
        $total = max(0, $total); // Garantindo que o total não seja negativo

        $set('content.total', number_format($total, 2, '.', ''));
    }

    /**
     * Encontra um produto+variação no carrinho e retorna seu índice
     *
     * @param int $productId ID do produto
     * @param int|null $productOptionId ID da variação do produto
     * @return int|false Índice do item no carrinho ou false se não encontrado
     */
    private function findProductInCart($productId, $productOptionId)
    {
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] == $productId && $item['product_option_id'] == $productOptionId) {
                return $index;
            }
        }

        return false;
    }
}
