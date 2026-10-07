<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\ProductOption;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Widgets\Widget;
use Illuminate\Support\HtmlString;

class QuickActionsWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.widgets.quick-actions-widget';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 4,
        'xl' => 4,
    ];

    public function calculateAction(): Action
    {
        return Action::make('calculate')
            ->label('Calculadora')
            ->icon('heroicon-o-calculator')
            ->color('primary')
            ->modalHeading('Calculadora de Orçamento')
            ->modalDescription('Adicione os produtos para gerar uma cotação e criar um orçamento.')
            ->modalSubmitActionLabel('Gerar Orçamento')
            ->modalWidth('6xl')
            ->form([
                Section::make('Itens da Cotação')
                    ->schema([
                        Repeater::make('items')
                            ->label('')
                            ->addActionLabel('Adicionar Produto')
                            ->schema([
                                Grid::make(12)->schema([
                                    Select::make('product')
                                        ->label('Produto / Tipo de Concreto')
                                        ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                        ->live()
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                            if ($state && ProductOption::where('product_id', $state)->count() === 1) {
                                                $set('product_option', ProductOption::where('product_id', $state)->value('id'));
                                            } else {
                                                $set('product_option', null);
                                            }
                                            $this->updateItemPrice($get, $set);
                                        })
                                        ->columnSpan(4),

                                    Select::make('product_option')
                                        ->label('Variação')
                                        ->options(function (Get $get) {
                                            $productId = $get('product');
                                            if (! $productId) return [];
                                            return ProductOption::where('product_id', $productId)->pluck('name', 'id');
                                        })
                                        ->live()
                                        ->searchable()
                                        ->preload()
                                        ->hidden(fn (Get $get) => ! $get('product') || ProductOption::where('product_id', $get('product'))->count() === 0)
                                        ->required(fn (Get $get) => $get('product') && ProductOption::where('product_id', $get('product'))->count() > 0)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => $this->updateItemPrice($get, $set))
                                        ->columnSpan(4),

                                    TextInput::make('quantity')
                                        ->label('Quantidade')
                                        ->numeric()
                                        ->default(3)
                                        ->minValue(3)
                                        ->suffix('m³')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => $this->updateItemPrice($get, $set))
                                        ->columnSpan(2),

                                    TextInput::make('price')
                                        ->label('Preço Unit.')
                                        ->prefix('R$')
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(2),
                                        
                                    Placeholder::make('subtotal_display')
                                        ->hiddenLabel()
                                        ->content(function (Get $get) {
                                            $qty = floatval($get('quantity') ?: 0);
                                            $price = floatval($get('price') ?: 0);
                                            $sub = $qty * $price;
                                            return new HtmlString('<div class="text-right text-sm font-bold text-primary-600">Subtotal: R$ ' . number_format($sub, 2, ',', '.') . '</div>');
                                        })
                                        ->columnSpan(12)
                                ])
                            ])
                            ->live()
                            ->defaultItems(1)
                            ->columns(1)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['product'] ? Product::find($state['product'])?->name : null),
                    ]),
            ])
            ->action(function (array $data) {
                $cartItems = [];
                $items = $data['items'] ?? [];
                
                foreach ($items as $item) {
                    $productId = $item['product'] ?? null;
                    if (!$productId) continue;
                    
                    $product = Product::find($productId);
                    $productOptionId = $item['product_option'] ?? null;
                    $productOption = $productOptionId ? ProductOption::find($productOptionId) : null;
                    
                    $qty = floatval($item['quantity'] ?? 0);
                    $price = floatval($item['price'] ?? 0);
                    
                    $cartItems[] = [
                        'product_id'          => $productId,
                        'product_name'        => $product ? $product->name : 'Produto',
                        'product_option_id'   => $productOptionId,
                        'product_option_name' => $productOption ? $productOption->name : null,
                        'quantity'            => $qty,
                        'price'               => $price,
                        'subtotal'            => $qty * $price,
                    ];
                }
                
                $payloadData = [
                    'items' => $cartItems,
                    'tax' => 0,
                    'discount' => 0,
                ];
                
                return redirect()->route('filament.admin.resources.budgets.create', [
                    'source' => 'calculator',
                    'payload' => base64_encode(json_encode($payloadData)),
                ]);
            });
    }

    private function updateItemPrice(Get $get, Set $set): void
    {
        $productId = $get('product');
        if (! $productId) {
            $set('price', 0);
            return;
        }

        $productOptionId = $get('product_option');
        if ($productOptionId) {
            $price = ProductOption::find($productOptionId)->price ?? 0;
        } else {
            $price = ProductOption::where('product_id', $productId)->value('price');
            if ($price === null) {
                $product = Product::find($productId);
                $price = $product ? $product->price : 0;
            }
        }
        $set('price', $price);
    }
}
