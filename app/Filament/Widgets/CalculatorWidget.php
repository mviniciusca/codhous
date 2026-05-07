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

    protected static ?int $sort = 6;

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

        // Check if we have a selected product
        if (! $productId) {
            $this->dispatch('notify', [
                'style'   => 'danger',
                'message' => 'Selecione um produto para adicionar ao carrinho.',
            ]);

            return;
        }

        // Check if the product has available variations
        $hasOptions = ProductOption::where('product_id', $productId)->count() > 0;

        // If the product has variations, but none was selected
        if ($hasOptions && ! $productOptionId) {
            $this->dispatch('notify', [
                'style'   => 'danger',
                'message' => 'É necessário selecionar uma variação do produto.',
            ]);

            return;
        }

        // Check if the price is greater than zero
        if ($price <= 0) {
            $this->dispatch('notify', [
                'style'   => 'danger',
                'message' => 'Não é possível adicionar produto com preço R$ 0,00. Verifique a configuração do produto.',
            ]);

            return;
        }

        // Get product and option information
        $product = Product::find($productId);
        $productOption = $productOptionId ? ProductOption::find($productOptionId) : null;

        // Check if this product+variation already exists in the cart
        $existingItemIndex = $this->findProductInCart($productId, $productOptionId);

        if ($existingItemIndex !== false) {
            // If it already exists, just increment the quantity and recalculate the subtotal
            $this->cart[$existingItemIndex]['quantity'] += $quantity;
            $this->cart[$existingItemIndex]['subtotal'] = $this->cart[$existingItemIndex]['quantity'] * $this->cart[$existingItemIndex]['price'];
        } else {
            // If it doesn't exist, add as a new item
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

        // Recalculate the cart total
        $this->calculateCartTotal();

        // Clear the form for a new product
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

        // Notify the user that the product was successfully added
        $this->dispatch('notify', [
            'style'   => 'success',
            'message' => 'Produto adicionado ao carrinho!',
        ]);
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Reindex the array
            $this->calculateCartTotal();
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->cartTotal = 0;

        // Update the cart total field in the interface
        $this->dispatch('notify', [
            'style'   => 'success',
            'message' => 'Carrinho limpo com sucesso!',
        ]);

        // Also keep taxes and discounts, just reset products and total
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

        // Apply taxes and discounts
        $tax = floatval($this->data['content']['tax'] ?? 0);
        $discount = floatval($this->data['content']['discount'] ?? 0);

        $this->cartTotal = $this->cartTotal + $tax - $discount;
        $this->cartTotal = max(0, $this->cartTotal); // Ensure it's not negative
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Calculadora de Orçamento')
                ->description('Adicione produtos para gerar uma cotação rápida e criar orçamentos instantâneos.')
                ->icon('heroicon-o-calculator')
                ->columnSpanFull()
                ->schema([
                    Grid::make(12)->schema([
                        // Calculator Group (Left)
                        Section::make('Dados do Produto')
                            ->icon('heroicon-o-plus-circle')
                            ->columnSpan(7)
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        Select::make('content.product')
                                            ->live()
                                            ->label('Produto / Tipo de Concreto')
                                            ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                            ->afterStateHydrated(function (Get $get, Set $set, $state) {
                                                $this->updatePrice($get, $set, $state);
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                                $set('content.product_option', null);
                                                $this->updatePrice($get, $set, $state);
                                            })
                                            ->required()
                                            ->columnSpan(8),

                                        Select::make('content.product_option')
                                            ->live()
                                            ->label('Variação')
                                            ->options(function (Get $get) {
                                                $productId = $get('content.product');
                                                if (! $productId) return [];
                                                return ProductOption::where('product_id', $productId)->pluck('name', 'id');
                                            })
                                            ->hidden(fn (Get $get) => ! $get('content.product') || ProductOption::where('product_id', $get('content.product'))->count() === 0)
                                            ->required(fn (Get $get) => $get('content.product') && ProductOption::where('product_id', $get('content.product'))->count() > 0)
                                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                                if ($state) {
                                                    $price = ProductOption::find($state)->price ?? 0;
                                                    $set('content.price', $price);
                                                    $this->calculateTotal($get, $set);
                                                }
                                            })
                                            ->columnSpan(4),

                                        TextInput::make('content.quantity')
                                            ->live(onBlur: true)
                                            ->label('Quantidade')
                                            ->required()
                                            ->numeric()
                                            ->minValue(3)
                                            ->default(3)
                                            ->suffix('m³')
                                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->calculateTotal($get, $set))
                                            ->columnSpan(3),

                                        TextInput::make('content.price')
                                            ->label('Preço Unit.')
                                            ->prefix('R$')
                                            ->disabled()
                                            ->dehydrated()
                                            ->columnSpan(3),

                                        TextInput::make('content.tax')
                                            ->live(onBlur: true)
                                            ->label('Taxas (+)')
                                            ->prefix('R$')
                                            ->numeric()
                                            ->default(0)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                $this->calculateTotal($get, $set);
                                                $this->calculateCartTotal();
                                            })
                                            ->columnSpan(3),

                                        TextInput::make('content.discount')
                                            ->live(onBlur: true)
                                            ->label('Desconto (-)')
                                            ->prefix('R$')
                                            ->numeric()
                                            ->default(0)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                $this->calculateTotal($get, $set);
                                                $this->calculateCartTotal();
                                            })
                                            ->columnSpan(3),

                                        TextInput::make('content.total')
                                            ->label('Subtotal do Item')
                                            ->prefix('R$')
                                            ->readonly()
                                            ->extraInputAttributes(['class' => 'text-xl font-bold text-primary-600'])
                                            ->columnSpanFull(),

                                        \Filament\Forms\Components\Actions::make([
                                            Action::make('addToCart')
                                                ->label('Adicionar ao Orçamento')
                                                ->icon('heroicon-m-plus')
                                                ->color('primary')
                                                ->disabled(fn (Get $get): bool => ! $get('content.product'))
                                                ->action(fn () => $this->addToCart()),
                                        ])->columnSpanFull()->alignRight(),
                                    ]),
                            ]),

                        // Budget Group (Right)
                        Section::make('Resumo da Cotação')
                            ->icon('heroicon-o-document-text')
                            ->columnSpan(5)
                            ->schema([
                                ViewField::make('cart_items')
                                    ->label('Itens Adicionados')
                                    ->view('filament.widgets.cart-items'),

                                TextInput::make('cart_total')
                                    ->label('Total Geral')
                                    ->prefix('R$')
                                    ->disabled()
                                    ->extraInputAttributes(['class' => 'text-2xl font-black text-success-600'])
                                    ->afterStateHydrated(fn (Set $set) => $set('cart_total', number_format($this->cartTotal, 2, '.', '')))
                                    ->dehydrated(false),

                                \Filament\Forms\Components\Actions::make([
                                    Action::make('clearCart')
                                        ->label('Limpar Tudo')
                                        ->icon('heroicon-m-trash')
                                        ->color('danger')
                                        ->disabled(fn (): bool => empty($this->cart))
                                        ->action(fn () => $this->clearCart()),

                                    Action::make('createBudget')
                                        ->label('Gerar Orçamento')
                                        ->icon('heroicon-m-document-check')
                                        ->color('success')
                                        ->url(function () {
                                            if (empty($this->cart)) return route('filament.admin.resources.budgets.create');
                                            $productsJson = json_encode($this->cart);
                                            return route('filament.admin.resources.budgets.create', [
                                                'activeTab' => 'shopping_bag',
                                                'products'  => base64_encode($productsJson),
                                            ]);
                                        })
                                        ->disabled(fn (): bool => empty($this->cart)),
                                ])->columnSpanFull(),
                            ]),
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
                // If a product option is selected, use the option price
                $price = ProductOption::find($productOptionId)->price ?? 0;
            } else {
                // If no option, try to get the price of the first option
                $price = ProductOption::where('product_id', $productId)->value('price');

                // If there are no options, try to get the price directly from the product
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

        // Ensuring values are not negative
        $quantity = max(3, $quantity);
        $tax = max(0, $tax);
        $discount = max(0, $discount);

        $total = ($quantity * $price) + $tax - $discount;
        $total = max(0, $total); // Ensuring the total is not negative

        $set('content.total', number_format($total, 2, '.', ''));
    }

    /**
     * Finds a product+variation in the cart and returns its index
     *
     * @param int $productId Product ID
     * @param int|null $productOptionId Product variation ID
     * @return int|false Index of the item in the cart or false if not found
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
