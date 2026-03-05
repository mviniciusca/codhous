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
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
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
                    Hidden::make('content.shipping')->default(0),
                ]),
                Wizard::make([
                    Step::make('Onde entregar')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            TextInput::make('content.postcode')
                                ->label('CEP do local de entrega')
                                ->placeholder('00000-000')
                                ->helperText('Digite o CEP da obra ou do local onde deseja receber o material. Consultamos automaticamente seu endereço.')
                                ->mask('99999-999')
                                ->required()
                                ->minLength(9)
                                ->maxLength(9)
                                ->live(debounce: 500)
                                ->rules($this->getPostcodeRules())
                                ->columnSpanFull()
                                ->suffixAction(
                                    Action::make('search-action')
                                        ->icon('heroicon-m-magnifying-glass')
                                        ->color('primary')
                                        ->action(function ($state, Set $set, $livewire) {
                                            if (empty($state)) return;
                                            $livewire->validateOnly('data.content.postcode');
                                            $postcode = new PostcodeFinderService($state, $set);
                                            $postcode->find();
                                            $livewire->applyShippingFromCep($state, $set);
                                        })
                                )
                                ->afterStateUpdated(function ($state, Set $set, $livewire) {
                                    if (strlen($state ?? '') === 9) {
                                        $livewire->validateOnly('data.content.postcode');
                                        $postcode = new PostcodeFinderService($state, $set);
                                        $postcode->find();
                                        $livewire->applyShippingFromCep($state, $set);
                                    }
                                }),
                            Placeholder::make('content.shipping_info')
                                ->label('')
                                ->content(fn (Get $get): string => $this->getShippingInfoContent($get))
                                ->visible(fn (Get $get): bool => filled($get('content.postcode')) && strlen(preg_replace('/\D/', '', $get('content.postcode') ?? '')) >= 8),
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('content.street')
                                        ->label('Logradouro')
                                        ->columnSpan(2)
                                        ->disabled()
                                        ->dehydrated(),
                                    TextInput::make('content.number')
                                        ->label('Número')
                                        ->required()
                                        ->placeholder('Ex.: 100'),
                                    TextInput::make('content.neighborhood')
                                        ->label('Bairro')
                                        ->disabled()
                                        ->dehydrated(),
                                    TextInput::make('content.city')
                                        ->label('Cidade')
                                        ->disabled()
                                        ->dehydrated(),
                                    TextInput::make('content.state')
                                        ->label('UF')
                                        ->disabled()
                                        ->dehydrated(),
                                ])
                                ->visible(fn (Get $get) => filled($get('content.street'))),
                        ]),
                    Step::make('Seus dados')
                        ->icon('heroicon-o-user')
                        ->schema([
                            TextInput::make('content.customer_name')
                                ->label('Nome completo')
                                ->required()
                                ->placeholder('Como deseja ser chamado'),
                            Group::make([
                                TextInput::make('content.customer_phone')
                                    ->label('WhatsApp')
                                    ->tel()
                                    ->mask('(99) 99999-9999')
                                    ->required()
                                    ->placeholder('(00) 00000-0000')
                                    ->helperText('Responderemos por aqui em até 24 horas.'),
                                TextInput::make('content.customer_email')
                                    ->label('E-mail')
                                    ->email()
                                    ->required()
                                    ->placeholder('seu@email.com'),
                            ])->columns(2),
                        ]),
                    Step::make('Seu pedido')
                        ->icon('heroicon-o-shopping-bag')
                        ->schema([
                            Repeater::make('content.products')
                                ->label('Itens do orçamento')
                                ->helperText('Adicione um ou mais produtos. Nossa equipe preparará um orçamento personalizado.')
                                ->itemLabel(
                                    fn (array $state): ?string => ($state['product'] ?? null)
                                        ? (
                                            Product::find($state['product'])?->name
                                            . ($state['product_option'] ? ' · ' . ProductOption::find($state['product_option'])?->name : '')
                                            . ($state['quantity'] ? ' — ' . $state['quantity'] . ' ' . (ProductOption::find($state['product_option'])?->unit?->value ?? '') : '')
                                        )
                                        : 'Novo item'
                                )
                                ->collapsible()
                                ->cloneable()
                                ->schema([
                                    Grid::make(2)->schema([
                                        Select::make('product')
                                            ->label('Produto')
                                            ->options(Product::all()->pluck('name', 'id'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('product_option', null)),
                                        Select::make('product_option')
                                            ->label('Opção / traço')
                                            ->options(fn (Get $get) => $this->getProductOptions($get))
                                            ->required(fn (Get $get) => $this->getProductOptions($get)->isNotEmpty())
                                            ->hidden(fn (Get $get) => $this->getProductOptions($get)->isEmpty())
                                            ->live()
                                            ->afterStateUpdated(fn (Get $get, Set $set, $state) => $this->updatePrice($get, $set, $state)),
                                    ]),
                                    Grid::make(2)->schema([
                                        Select::make('location')
                                            ->label('Local da obra')
                                            ->options(Location::all()->pluck('name', 'id'))
                                            ->required()
                                            ->helperText('Onde o material será aplicado (ex.: laje, piso).'),
                                        TextInput::make('quantity')
                                            ->label('Quantidade')
                                            ->numeric()
                                            ->suffix(fn (Get $get) => ProductOption::find($get('product_option'))?->unit?->value ?? '')
                                            ->minValue(fn (Get $get) => $get('product_option') ? 1 : 3)
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
                ])
                    ->nextAction(fn ($action) => $action
                        ->label('Próximo')
                        ->color('primary')
                        ->extraAttributes(['class' => '!bg-primary !text-primary-foreground hover:!bg-primary/90'])
                    )
                    ->previousAction(fn ($action) => $action
                        ->label('Voltar')
                        ->color('gray')
                        ->extraAttributes(['class' => '!bg-gray-200 !text-gray-900 dark:!bg-gray-700 dark:!text-gray-100 hover:!opacity-90'])
                    )
                    ->submitAction(view('livewire.budget-wizard-submit')),
            ])
            ->statePath('data');
    }

    /**
     * Regras de validação do CEP: obrigatório, 9 caracteres e dentro da área de operação.
     */
    protected function getPostcodeRules(): array
    {
        return [
            'required',
            'string',
            'size:9',
            new CepInOperationAreaRule,
        ];
    }

    public function create(): void
    {
        $this->form->validate();
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

    /**
     * @param Get|\Closure $get
     */
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

    /**
     * Aplica frete da área de operação ao CEP e recalcula o total.
     */
    public function applyShippingFromCep(?string $postcode, Set $set): void
    {
        if (empty($postcode) || strlen(preg_replace('/\D/', '', $postcode)) < 8) {
            return;
        }
        $result = OperationAreaService::resultForCep($postcode);
        $fee = $result['shipping_fee'] ?? 0;
        $set('content.shipping', (string) $fee);
        $get = fn (string $key) => data_get($this->data, $key);
        $this->calculateTotal($get, $set);
        if (! ($result['in_area'] ?? true)) {
            Notification::make()
                ->title(__('CEP fora da área de atendimento'))
                ->body($result['message'] ?? '')
                ->warning()
                ->send();
        }
    }

    /**
     * Mensagem de área de atendimento sem exibir valor de frete ao público.
     */
    private function getShippingInfoContent(Get $get): string
    {
        $postcode = $get('content.postcode');
        if (empty($postcode) || strlen(preg_replace('/\D/', '', $postcode)) < 8) {
            return '';
        }
        $result = OperationAreaService::resultForCep($postcode);
        if ($result['in_area']) {
            return __('Sua região está em nossa área de atendimento.');
        }
        return $result['message'] ?? '';
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
