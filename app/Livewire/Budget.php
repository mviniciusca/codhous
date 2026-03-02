<?php

namespace App\Livewire;

use App\Models\Budget as BudgetModel;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use App\Notifications\NewBudget;
use App\Services\BudgetCalculatorService;
use App\Services\PostcodeFinderService;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class Budget extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public bool $isSubmitted = false;

    public function mount(): void
    {
        $this->form->fill();
        $this->dispatch('cart-updated', count($this->data['content']['products'] ?? []));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Hidden::make('code')->default(Str::random(8)),
                    Hidden::make('content.price'),
                    Hidden::make('content.subtotal'),
                    Hidden::make('content.total'),
                    Hidden::make('content.quantity'),
                ]),
                Section::make('Suas Informações')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        TextInput::make('content.customer_name')
                            ->label('NOME COMPLETO')
                            ->required()
                            ->placeholder('Ex: Marcos Vinícius'),
                        Group::make([
                            TextInput::make('content.customer_phone')
                                ->label('WHATSAPP')
                                ->tel()
                                ->mask('(99) 99999-9999')
                                ->required(),
                            TextInput::make('content.customer_email')
                                ->label('E-MAIL')
                                ->email()
                                ->required(),
                        ])->columns(2),
                    ]),
                Section::make('Detalhes do Pedido')
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsed()
                    ->schema([
                        Repeater::make('content.products')
                            ->hiddenLabel()
                            ->itemLabel(
                                fn(array $state): ?string => ($state['product'] ?? null)
                                    ? (
                                        Product::find($state['product'])?->name
                                        // append option name if chosen
                                        . ($state['product_option'] ? ' – ' . ProductOption::find($state['product_option'])?->name : '')
                                        // append quantity with unit when available
                                        . ($state['quantity']
                                            ? ' — ' . $state['quantity'] . ' ' . (ProductOption::find($state['product_option'])?->unit?->value ?? '')
                                            : '')
                                    )
                                    : 'Novo Item'
                            )
                            ->collapsible()
                            ->cloneable()
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('product')
                                        ->label('PRODUTO')
                                        ->options(Product::all()->pluck('name', 'id'))
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(fn(Set $set) => $set('product_option', null)),
                                    Select::make('product_option')
                                        ->label('OPÇÃO / TRAÇO')
                                        ->options(fn(Get $get) => $this->getProductOptions($get))
                                        ->required(fn(Get $get) => $this->getProductOptions($get)->isNotEmpty())
                                        ->hidden(fn(Get $get) => $this->getProductOptions($get)->isEmpty())
                                        ->live()
                                        ->afterStateUpdated(fn(Get $get, Set $set, $state) => $this->updatePrice($get, $set, $state)),
                                ]),
                                Grid::make(2)->schema([
                                    Select::make('location')
                                        ->label('LOCAL DA OBRA')
                                        ->options(Location::all()->pluck('name', 'id'))
                                        ->required(),
                                    TextInput::make('quantity')
                                        ->label('QUANTIDADE')
                                        ->numeric()
                                        ->suffix(fn(Get $get) => ProductOption::find($get('product_option'))?->unit?->value ?? '')
                                        ->minValue(fn(Get $get) => $get('product_option') ? 1 : 3)
                                        ->required()
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            $this->calculateItemSubtotal($get, $set);
                                            $this->calculateTotal($get, $set);
                                            $this->dispatchCartUpdatedEvent($get);
                                        }),
                                ]),
                            ])
                            ->columns(1)
                            ->addActionLabel('Adicionar outro item'),
                    ]),
                Section::make('Local de Entrega')
                    ->icon('heroicon-o-map')
                    ->collapsed()
                    ->schema([
                        TextInput::make('content.postcode')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->required()
                            ->minLength(9)
                            ->maxLength(9)
                            ->live(debounce: 500)
                            ->suffixAction(
                                Action::make('search-action')
                                    ->icon('heroicon-m-magnifying-glass')
                                    ->color('primary')
                                    ->action(function ($state, Set $set, $livewire) {
                                        if (empty($state)) return;
                                        $livewire->validateOnly('data.content.postcode');
                                        $postcode = new PostcodeFinderService($state, $set);
                                        $postcode->find();
                                    })
                            )
                            ->afterStateUpdated(function ($state, Set $set, $livewire) {
                                if (strlen($state) === 9) {
                                    $livewire->validateOnly('data.content.postcode');
                                    $postcode = new PostcodeFinderService($state, $set);
                                    $postcode->find();
                                }
                            }),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('content.street')->label('RUA')->columnSpan(2)->disabled()->dehydrated(),
                                TextInput::make('content.number')->label('Nº')->required(),
                                TextInput::make('content.neighborhood')->label('BAIRRO')->disabled()->dehydrated(),
                                TextInput::make('content.city')->label('CIDADE')->disabled()->dehydrated(),
                                TextInput::make('content.state')->label('UF')->disabled()->dehydrated(),
                            ])
                            ->visible(fn(Get $get) => filled($get('content.street')))
                    ]),
            ])
            ->statePath('data')
            ->extraAttributes([
                'class' => 'font-mono'
            ]);
    }

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

    public function resetForm(): void
    {
        $this->form->fill();
        $this->isSubmitted = false;
    }

    public function render()
    {
        return view('livewire.budget', []);
    }

    private function getProductOptions(Get $get): Collection
    {
        return ProductOption::where('product_id', '=', $get('product'))
            ->get()
            ->pluck('name', 'id');
    }

    private function calculateTotal(Get $get, Set $set): void
    {
        $products = $get('content.products') ?? [];
        $result = BudgetCalculatorService::calculateTotal($products);
        $set('content.quantity', $result['quantity']);
        $set('content.price', $result['price']);
        $set('content.subtotal', $result['subtotal']);
        $set('content.total', $result['total']);
    }

    private function updatePrice(Get $get, Set $set, $productId): void
    {
        if ($productId) {
            $price = ProductOption::where('id', $productId)
                ->value('price') ?? 0;
        } else {
            $price = 0;
        }

        // whenever the selected option changes we should reset the quantity so
        // the user doesn't end up with a value from a previous unit and to let
        // the suffix update immediately
        $set('quantity', null);

        $set('price', $price);
        $this->calculateItemSubtotal($get, $set);
        $this->calculateTotal($get, $set);
    }

    private function calculateItemSubtotal(Get $get, Set $set): void
    {
        $quantity = intval($get('quantity') ?? 0);
        $price = floatval($get('price') ?? 0);
        $subtotal = BudgetCalculatorService::calculateItemSubtotal($quantity, $price);
        $set('subtotal', $subtotal);
    }

    public function updated($property): void
    {
        if (str_contains($property, 'products')) {
            $this->dispatch('cart-updated', count($this->data['content']['products'] ?? []));
        }
    }

    private function dispatchCartUpdatedEvent(Get $get): void
    {
        $products = $get('content.products') ?? [];
        $this->dispatch('cart-updated', count($products));
    }
}
