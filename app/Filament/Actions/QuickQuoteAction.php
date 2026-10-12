<?php

namespace App\Filament\Actions;

use App\Filament\Resources\BudgetResource;
use App\Models\Product;
use App\Models\ProductOption;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\HtmlString;

/**
 * Calculadora de cotação: monta os itens com o preço de tabela e abre um
 * orçamento novo já preenchido (CreateBudget lê o payload "calculator").
 */
class QuickQuoteAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'quickQuote';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Cotação rápida')
            ->icon('heroicon-o-calculator')
            ->color('gray')
            ->modalHeading('Cotação rápida')
            ->modalDescription('Monte os itens com o preço de tabela e gere um orçamento a partir deles.')
            ->modalSubmitActionLabel('Gerar orçamento')
            ->modalWidth('5xl')
            ->form([
                Repeater::make('items')
                    ->hiddenLabel()
                    ->addActionLabel('Adicionar produto')
                    ->schema([
                        Grid::make(12)->schema([
                            Select::make('product')
                                ->label('Produto')
                                ->options(fn () => Product::where('is_active', true)->orderBy('sort')->orderBy('name')->pluck('name', 'id'))
                                ->live()
                                ->searchable()
                                ->preload()
                                ->required()
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    $options = $state ? self::optionsQuery($state)->pluck('id') : collect();
                                    $set('product_option', $options->count() === 1 ? $options->first() : null);

                                    if ($state && ($product = Product::find($state))) {
                                        $set('quantity', (float) $get('quantity') < (float) $product->min_quantity ? (float) $product->min_quantity : $get('quantity'));
                                    }

                                    self::updateItemPrice($get, $set);
                                })
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            Select::make('product_option')
                                ->label('Variação')
                                ->options(fn (Get $get) => $get('product') ? self::optionsQuery($get('product'))->pluck('name', 'id') : [])
                                ->live()
                                ->searchable()
                                ->hidden(fn (Get $get) => ! $get('product') || ! self::optionsQuery($get('product'))->exists())
                                ->required(fn (Get $get) => $get('product') && self::optionsQuery($get('product'))->exists())
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::updateItemPrice($get, $set))
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('quantity')
                                ->label('Quantidade')
                                ->numeric()
                                ->default(3)
                                ->minValue(fn (Get $get) => (float) (Product::find($get('product'))?->min_quantity ?? 0))
                                ->suffix(fn (Get $get) => Product::find($get('product'))?->unit?->short() ?? 'm³')
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::updateItemPrice($get, $set))
                                ->columnSpan(['default' => 6, 'md' => 2]),

                            TextInput::make('price')
                                ->label('Preço unit.')
                                ->prefix('R$')
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(['default' => 6, 'md' => 2]),

                            Placeholder::make('subtotal_display')
                                ->hiddenLabel()
                                ->content(fn (Get $get) => new HtmlString(
                                    '<div class="text-right text-sm font-semibold text-primary-600 dark:text-primary-400">Subtotal: R$ '
                                    .number_format((float) $get('quantity') * (float) $get('price'), 2, ',', '.')
                                    .'</div>'
                                ))
                                ->columnSpan(12),
                        ]),
                    ])
                    ->live()
                    ->defaultItems(1)
                    ->reorderable(false)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => filled($state['product'] ?? null) ? Product::find($state['product'])?->name : null),
            ])
            ->action(function (array $data) {
                $items = collect($data['items'] ?? [])
                    ->filter(fn (array $item) => filled($item['product'] ?? null))
                    ->map(function (array $item) {
                        $quantity = (float) ($item['quantity'] ?? 0);
                        $price = (float) ($item['price'] ?? 0);

                        return [
                            'product_id' => $item['product'],
                            'product_name' => Product::find($item['product'])?->name ?? 'Produto',
                            'product_option_id' => $item['product_option'] ?? null,
                            'product_option_name' => filled($item['product_option'] ?? null) ? ProductOption::find($item['product_option'])?->name : null,
                            'quantity' => $quantity,
                            'price' => $price,
                            'subtotal' => $quantity * $price,
                        ];
                    })
                    ->values()
                    ->all();

                return redirect()->to(BudgetResource::getUrl('create', [
                    'source' => 'calculator',
                    'payload' => base64_encode(json_encode(['items' => $items, 'tax' => 0, 'discount' => 0])),
                ]));
            });
    }

    protected static function optionsQuery(int|string $productId)
    {
        return ProductOption::where('product_id', $productId)->where('is_active', true)->orderBy('sort')->orderBy('name');
    }

    protected static function updateItemPrice(Get $get, Set $set): void
    {
        $productId = $get('product');
        if (! $productId) {
            $set('price', 0);

            return;
        }

        $optionId = $get('product_option');
        $price = $optionId
            ? ProductOption::find($optionId)?->price
            : self::optionsQuery($productId)->value('price');

        $set('price', $price ?? 0);
    }
}
