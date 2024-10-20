<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class CalculatorWidget extends Widget implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.widgets.calculator-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 1;
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('Budget Calculator'))
                ->description(__('Calculate a budget in a easy way.'))
                ->columns(3)
                ->collapsed()
                ->icon('heroicon-o-calculator')
                ->schema([
                    Select::make('content.product')
                        ->live()
                        ->label(__('Product'))
                        ->helperText(__('Type of Concrete'))
                        ->options(Product::pluck('name', 'id'))
                        ->afterStateHydrated(function (Get $get, Set $set, $state) {
                            $this->updatePrice($get, $set, $state);
                        })
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            $this->updatePrice($get, $set, $state);
                        })
                        ->dehydrated(),
                    TextInput::make('content.quantity')
                        ->live(onBlur: true)
                        ->dehydrated()
                        ->required()
                        ->integer()
                        ->minValue(3)
                        ->suffix('m³')
                        ->helperText(__('Quantity of items'))
                        ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                            $this->calculateTotal($get, $set);
                        })
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            $this->calculateTotal($get, $set);
                        })
                        ->numeric(),
                    TextInput::make('content.price')
                        ->live()
                        ->disabled()
                        ->dehydrated()
                        ->prefix(env('CURRENCY_SUFFIX'))
                        ->label(__('Price per Unity (m³)'))
                        ->numeric()
                        ->helperText(__('Price of product in ' . env('CURRENCY_SUFFIX')))
                        ->step(0.01)
                        ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                            $this->calculateTotal($get, $set);
                        })
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            $this->calculateTotal($get, $set);
                        }),
                    TextInput::make('content.tax')
                        ->live(onBlur: true)
                        ->dehydrated()
                        ->prefix('+' . env('CURRENCY_SUFFIX'))
                        ->numeric()
                        ->required()
                        ->helperText(__('Sum tax or other values in ' . env('CURRENCY_SUFFIX')))
                        ->step(0.01)
                        ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                            $this->calculateTotal($get, $set);
                        })
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            $this->calculateTotal($get, $set);
                        }),
                    TextInput::make('content.discount')
                        ->live(onBlur: true)
                        ->numeric()
                        ->required()
                        ->prefix('-' . env('CURRENCY_SUFFIX'))
                        ->helperText(__('Applies a discount in ' . env('CURRENCY_SUFFIX')))
                        ->step(0.01)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            $this->calculateTotal($get, $set);
                        })
                        ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                            $this->calculateTotal($get, $set);
                        }),
                    TextInput::make('content.total')
                        ->live()
                        ->readonly()
                        ->numeric()
                        ->required()
                        ->label(__('Total Price'))
                        ->helperText(__('The total budget value in ' . env('CURRENCY_SUFFIX')))
                        ->prefix(env('CURRENCY_SUFFIX'))
                        ->step(0.01),
                ]),
        ])
            ->statePath('data');

    }

    private function getPrice(Get $get, Set $set)
    {
        $id = $get('content.product');
        $price = Product::select(['price'])
            ->where('id', '=', $id)
            ->first();
        $set('content.price', $price->price ?? 0);
    }
    private function updatePrice(Get $get, Set $set, $productId): void
    {
        if ($productId) {
            $price = Product::where('id', $productId)->value('price') ?? 0;
        } else {
            $price = 0;
        }
        $set('content.price', $price);
        $this->calculateTotal($get, $set);
    }

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
