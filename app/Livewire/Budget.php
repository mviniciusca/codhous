<?php

namespace App\Livewire;

use App\Models\Budget as BudgetModel;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use App\Notifications\NewBudget;
use App\Services\BudgetCalculatorService;
use App\Services\OperationAreaService;
use App\Services\PostcodeFinderService;
use App\Rules\CepInOperationAreaRule;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
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
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('code')->default(Str::random(8)),
                Hidden::make('content.price'),
                Hidden::make('content.subtotal'),
                Hidden::make('content.total'),
                Hidden::make('content.quantity'),
                Hidden::make('content.shipping')->default(0),

                Grid::make(12)
                    ->schema([
                        // Coluna da Esquerda (Formulário)
                        Group::make([
                            Section::make('1. Local de Entrega')
                                ->description('Onde o material deve ser entregue?')
                                ->icon('heroicon-o-map-pin')
                                ->extraAttributes(['class' => '!shadow-none'])
                                ->schema([
                                    TextInput::make('content.postcode')
                                        ->label('CEP')
                                        ->placeholder('Digite o CEP para localizar o endereço')
                                        ->mask('99999-999')
                                        ->required()
                                        ->live(debounce: 500)
                                        ->extraInputAttributes(['class' => '!bg-white shadow-sm'])
                                        ->rules($this->getPostcodeRules())
                                        ->afterStateUpdated(function ($state, Set $set, $livewire) {
                                            if (strlen($state ?? '') === 9) {
                                                $livewire->validateOnly('data.content.postcode');
                                                $postcode = new PostcodeFinderService($state, $set);
                                                $postcode->find();
                                                $livewire->applyShippingFromCep($state, $set);
                                            }
                                        }),
                                    
                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('content.street')
                                                ->label('Rua/Av')
                                                ->columnSpan(2)
                                                ->disabled()
                                                ->dehydrated()
                                                ->extraInputAttributes(['class' => '!bg-zinc-100/80 !opacity-90 !cursor-not-allowed border-zinc-200']),
                                            TextInput::make('content.number')
                                                ->label('Nº')
                                                ->required()
                                                ->live(onBlur: true)
                                                ->placeholder('Nº ou KM da obra')
                                                ->extraInputAttributes(['class' => '!bg-white shadow-sm']),
                                        ])
                                        ->visible(fn (Get $get) => filled($get('content.street'))),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('content.neighborhood')->label('Bairro')->disabled()->dehydrated()->extraInputAttributes(['class' => '!bg-zinc-100/80 !opacity-90 !cursor-not-allowed border-zinc-200']),
                                            TextInput::make('content.city')->label('Cidade')->disabled()->dehydrated()->extraInputAttributes(['class' => '!bg-zinc-100/80 !opacity-90 !cursor-not-allowed border-zinc-200']),
                                        ])
                                        ->visible(fn (Get $get) => filled($get('content.street'))),
                                ])->collapsible(),

                            Section::make('2. Seus Dados')
                                ->description('Como podemos entrar em contato?')
                                ->icon('heroicon-o-user')
                                ->extraAttributes(['class' => 'shadow-none'])
                                ->schema([
                                    TextInput::make('content.customer_name')
                                        ->label('Nome Completo')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->placeholder('Nome do responsável pela obra')
                                        ->extraInputAttributes(['class' => '!bg-white shadow-sm']),
                                    Grid::make(2)->schema([
                                        TextInput::make('content.customer_phone')
                                            ->label('WhatsApp')
                                            ->tel()
                                            ->mask('(99)99999-9999')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->placeholder('(00) 00000-0000 - WhatsApp para retorno')
                                            ->extraInputAttributes(['class' => '!bg-white shadow-sm']),
                                        TextInput::make('content.customer_email')
                                            ->label('E-mail')
                                            ->email()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->placeholder('E-mail para envio da proposta')
                                            ->extraInputAttributes(['class' => '!bg-white shadow-sm']),
                                    ]),
                                ])->collapsible(),

                            Section::make('3. Itens do Pedido')
                                ->description('Quais produtos ou serviços você precisa?')
                                ->icon('heroicon-o-shopping-bag')
                                ->extraAttributes(['class' => 'shadow-none'])
                                ->schema([
                                    Repeater::make('content.products')
                                        ->label('')
                                        ->live()
                                        ->itemLabel(fn (array $state): ?string => $this->getItemLabel($state))
                                        ->schema([
                                            Grid::make(2)->schema([
                                                Select::make('product')
                                                    ->label('Produto')
                                                    ->options(Product::all()->pluck('name', 'id'))
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set) => $set('product_option', null)),
                                                Select::make('product_option')
                                                    ->label('Opção / Traço')
                                                    ->options(fn (Get $get) => $this->getProductOptions($get))
                                                    ->required(fn (Get $get) => $this->getProductOptions($get)->isNotEmpty())
                                                    ->hidden(fn (Get $get) => $this->getProductOptions($get)->isEmpty())
                                                    ->live()
                                                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => $this->updatePrice($get, $set, $state)),
                                            ]),
                                            Grid::make(2)->schema([
                                                Select::make('location')
                                                    ->label('Local da Obra')
                                                    ->options(Location::all()->pluck('name', 'id'))
                                                    ->required()
                                                    ->live(),
                                                TextInput::make('quantity')
                                                    ->label('Quantidade')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->suffix(fn (Get $get) => ProductOption::find($get('product_option'))?->unit?->value ?? '')
                                                    ->required()
                                                    ->live(debounce: 500)
                                                    ->placeholder('Ex: 5')
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        $this->calculateItemSubtotal($get, $set);
                                                        $this->calculateTotal($get, $set);
                                                    }),
                                            ]),
                                        ])
                                        ->addActionLabel('Adicionar Produto')
                                        ->collapsible(),
                                ])->collapsible(),
                        ])->columnSpan(8),

                        // Coluna da Direita (Resumo / Checkout Sidebar)
                        Group::make([
                            Section::make('Resumo do Orçamento')
                                ->description('Confira os itens selecionados para sua obra.')
                                ->icon('heroicon-o-shopping-cart')
                                ->extraAttributes(['class' => 'shadow-none'])
                                ->schema([
                                    Placeholder::make('summary')
                                        ->label('')
                                        ->content(fn (Get $get) => view('livewire.budget-checkout-summary', [
                                            'data' => ['content' => $get('content')]
                                        ])),
                                ])
                                ->extraAttributes(['class' => 'sticky top-24'])
                        ])->columnSpan(4),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getItemLabel(array $state): string
    {
        if (empty($state['product'])) return 'Novo Item';
        $productName = Product::find($state['product'])?->name;
        $optionName = $state['product_option'] ? ' · ' . ProductOption::find($state['product_option'])?->name : '';
        $qty = $state['quantity'] ? ' (' . $state['quantity'] . ')' : '';
        return $productName . $optionName . $qty;
    }

    protected function getPostcodeRules(): array
    {
        return ['required', 'string', 'size:9', new CepInOperationAreaRule];
    }

    public function create(): void
    {
        $this->form->validate();
        $budget = BudgetModel::create($this->form->getState());
        $user = new User();
        $user->first()?->notify(new NewBudget($budget->toArray()));
        
        Notification::make()
            ->title('Pedido Enviado com Sucesso!')
            ->body('Nossa equipe entrará em contato em breve.')
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
        return view('livewire.budget');
    }

    private function getProductOptions(Get $get): Collection
    {
        return ProductOption::where('product_id', '=', $get('product'))->get()->pluck('name', 'id');
    }

    private function calculateTotal(Get|\Closure $get, Set $set): void
    {
        $products = $get('content.products') ?? [];
        $shipping = (float) ($get('content.shipping') ?? 0);
        $result = BudgetCalculatorService::calculateTotal($products, $shipping, 0, 0);
        $set('content.quantity', $result['quantity']);
        $set('content.price', $result['price']);
        $set('content.subtotal', $result['subtotal']);
        $set('content.total', $result['total']);
    }

    public function applyShippingFromCep(?string $postcode, Set $set): void
    {
        if (empty($postcode) || strlen(preg_replace('/\D/', '', $postcode)) < 8) return;
        $result = OperationAreaService::resultForCep($postcode);
        $fee = $result['shipping_fee'] ?? 0;
        $set('content.shipping', (string) $fee);
        $get = fn (string $key) => data_get($this->data, $key);
        $this->calculateTotal($get, $set);
    }

    private function updatePrice(Get $get, Set $set, $productId): void
    {
        $price = $productId ? (ProductOption::find($productId)?->price ?? 0) : 0;
        $set('quantity', null);
        $set('price', $price);
        $this->calculateItemSubtotal($get, $set);
        $this->calculateTotal($get, $set);
    }

    private function calculateItemSubtotal(Get $get, Set $set): void
    {
        $quantity = intval($get('quantity') ?? 0);
        $price = floatval($get('price') ?? 0);
        $set('subtotal', BudgetCalculatorService::calculateItemSubtotal($quantity, $price));
    }
}
