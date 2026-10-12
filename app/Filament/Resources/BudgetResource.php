<?php

namespace App\Filament\Resources;

use App\Enums\FakeDataEnum;
use App\Filament\Resources\BudgetResource\Pages;
use App\Filament\Resources\BudgetResource\RelationManagers\BudgetHistoryRelationManager;
use App\Filament\Resources\BudgetResource\RelationManagers\DocumentsRelationManager;
use App\Mail\BudgetCustomMessageMail;
use App\Models\Budget;
use App\Models\BudgetPdf;
use App\Models\Mail;
use App\Models\OperationArea;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Setting;
use App\Services\AddressFinderService;
use App\Services\BudgetCalculatorService;
use App\Services\FakeBudgetDataService;
use App\Services\PdfGeneratorService;
use App\Services\SendBudgetMailService;
use App\Services\WhatsappService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class BudgetResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'Orçamento';

    protected static ?string $model = Budget::class;

    protected static ?string $modelLabel = 'Orçamento';

    protected static ?string $pluralModelLabel = 'Orçamentos';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return 'Orçamento';
    }

    protected static ?string $navigationGroup = 'Orçamentos';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'content'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Cliente' => $record->content['customer_name'] ?? 'N/A',
            'Código' => $record->code,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count != 0 ? $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Visão Geral')
                    ->description('Acompanhe o identificador, o progresso comercial e as notificações deste pedido.')
                    ->icon('heroicon-o-information-circle')
                    ->columns(12)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true)
                            ->inline(false)
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->helperText('Visibilidade.')
                            ->live()
                            ->afterStateUpdated(function ($state, ?Budget $record) {
                                if ($record && $record->exists) {
                                    $record->update(['is_active' => $state]);
                                    Notification::make()
                                        ->title($state ? 'Orçamento ativado!' : 'Orçamento desativado!')
                                        ->success()
                                        ->send();
                                }
                            })
                            ->columnSpan([
                                'default' => 12,
                                'md' => 1,
                            ]),

                        TextInput::make('code')
                            ->label('Identificador')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->default(fn () => Budget::generateUniqueCode())
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Código único.')
                            ->columnSpan([
                                'default' => 12,
                                'md' => 2,
                            ]),

                        ToggleButtons::make('status')
                            ->label('Status do Negócio')
                            ->default('pending')
                            ->helperText('Estágio comercial.')
                            ->options([
                                'pending' => 'Pendente',
                                'on going' => 'Andamento',
                                'done' => 'Concluído',
                                'ignored' => 'Arquivado',
                            ])
                            ->colors([
                                'pending' => 'warning',
                                'on going' => 'info',
                                'done' => 'success',
                                'ignored' => 'danger',
                            ])
                            ->icons([
                                'pending' => 'heroicon-o-clock',
                                'on going' => 'heroicon-o-arrow-path',
                                'done' => 'heroicon-o-check-circle',
                                'ignored' => 'heroicon-o-archive-box-x-mark',
                            ])
                            ->inline()
                            ->live()
                            ->afterStateUpdated(function ($state, ?Budget $record) {
                                if ($record && $record->exists) {
                                    $record->update(['status' => $state]);
                                    Notification::make()
                                        ->title('Status salvo!')
                                        ->success()
                                        ->send();
                                }
                            })
                            ->columnSpan([
                                'default' => 12,
                                'md' => 5,
                            ]),

                        DateTimePicker::make('created_at')
                            ->label('Registro')
                            ->prefixIcon('heroicon-m-calendar-days')
                            ->displayFormat('d/m/Y H:i')
                            ->disabled()
                            ->helperText('Data/Hora.')
                            ->columnSpan([
                                'default' => 12,
                                'md' => 2,
                            ]),

                        Placeholder::make('overview_total')
                            ->label('Valor Total')
                            ->content(fn (Get $get) => new HtmlString(
                                '<span class="text-xl font-bold text-primary-600 dark:text-primary-400">R$ '.e($get('content.total') ?: '0,00').'</span>'
                            ))
                            ->helperText('Itens + frete + taxas − desconto.')
                            ->columnSpan([
                                'default' => 12,
                                'md' => 2,
                            ]),
                    ]),

                Tabs::make('Conteúdo do Orçamento')
                    ->persistTabInQueryString()
                    ->afterStateHydrated(function (Get $get, Set $set) {
                        self::calculateTotalFromRepeater($get, $set);
                    })
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Informações do Cliente')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Dados do Cliente e Obra')
                                    ->description('Informações detalhadas de contato do cliente e endereço de execução do serviço.')
                                    ->icon('heroicon-o-user-circle')
                                    ->headerActions([
                                        Action::make('ai_smart_fill')
                                            ->label('Preenchimento IA')
                                            ->icon('heroicon-o-sparkles')
                                            ->color('success')
                                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateBudget)
                                            ->action(function (Set $set, Get $get) {
                                                $fakeService = new FakeBudgetDataService;
                                                $fakeData = $fakeService->generateCompleteBudgetData();

                                                foreach ($fakeData as $key => $value) {
                                                    $set('content.'.$key, $value);
                                                }

                                                Notification::make()
                                                    ->title('Preenchimento inteligente aplicado!')
                                                    ->success()
                                                    ->send();
                                            }),
                                        Action::make('clear_all_fields')
                                            ->label('Limpar Tudo')
                                            ->hiddenLabel()
                                            ->icon('heroicon-o-arrow-uturn-left')
                                            ->color('danger')
                                            ->visible(fn ($livewire, Get $get) => $livewire instanceof Pages\CreateBudget &&
                                                FakeDataEnum::matches($get('content') ?? [])
                                            )
                                            ->requiresConfirmation()
                                            ->action(function (Set $set) {
                                                $set('content', []);
                                                Notification::make()
                                                    ->title('Campos limpos!')
                                                    ->success()
                                                    ->send();
                                            }),
                                    ])
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('content.customer_name')
                                            ->required()
                                            ->live()
                                            ->label('Nome do Cliente')
                                            ->helperText('Nome completo fornecido pelo cliente.'),
                                        TextInput::make('content.customer_email')
                                            ->required()
                                            ->email()
                                            ->live(onBlur: true)
                                            ->label('E-mail')
                                            ->helperText('Endereço de e-mail para contato.')
                                            ->suffixAction(
                                                Action::make('send_mailto')
                                                    ->icon('heroicon-o-envelope')
                                                    ->tooltip('Escrever e-mail')
                                                    ->url(fn ($state) => filled($state) ? 'mailto:'.$state : null)
                                                    ->visible(fn ($state) => filled($state))
                                            ),
                                        TextInput::make('content.customer_phone')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->tel()
                                            ->mask(RawJs::make(<<<'JS'
                                                $input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-99999'
                                            JS))
                                            ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                                                $digits = strlen(preg_replace('/\D/', '', (string) $value));
                                                if ($digits < 10 || $digits > 11) {
                                                    $fail('Informe um telefone válido com DDD.');
                                                }
                                            })
                                            ->dehydrateStateUsing(fn ($state) => \App\Livewire\Budget::formatPhone((string) $state))
                                            ->label('Telefone/WhatsApp')
                                            ->helperText('Celular ou fixo, com DDD.')
                                            ->suffixAction(
                                                Action::make('open_whatsapp')
                                                    ->icon('heroicon-o-chat-bubble-left-right')
                                                    ->tooltip('Abrir conversa no WhatsApp')
                                                    ->url(fn ($state) => filled($state)
                                                        ? (new WhatsappService)->generateUrl($state, 'Olá! Aqui é da '.(Setting::get('company.trade_name') ?: config('app.name')).', sobre o seu pedido de orçamento.')
                                                        : null, shouldOpenInNewTab: true)
                                                    ->visible(fn ($state) => strlen(preg_replace('/\D/', '', (string) $state)) >= 10)
                                            ),
                                        TextInput::make('content.postcode')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->mask('99999-999')
                                            ->label('CEP')
                                            ->helperText('Formato: 00000-000')
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if (strlen(preg_replace('/[^0-9]/', '', $state ?? '')) === 8) {
                                                    try {
                                                        $finder = new AddressFinderService($state, $set, [
                                                            'logradouro' => 'content.street',
                                                            'bairro' => 'content.neighborhood',
                                                            'localidade' => 'content.city',
                                                            'uf' => 'content.state',
                                                        ], 'content.postcode');
                                                        $finder->find();

                                                        // Busca automática de frete baseada na faixa de CEP
                                                        $area = OperationArea::findOperationAreaByCep($state);
                                                        if ($area) {
                                                            $set('content.shipping', number_format((float) $area->shipping_fee, 2, ',', '.'));

                                                            // Recalcula o total final com o novo frete
                                                            self::calculateTotalFromRepeater($get, $set);

                                                            Notification::make()
                                                                ->title('Frete carregado: R$ '.number_format($area->shipping_fee, 2, ',', '.'))
                                                                ->success()
                                                                ->send();
                                                        }
                                                    } catch (\Exception $e) {
                                                        // Fallback silencioso para não travar a UI
                                                    }
                                                }
                                            }),
                                        TextInput::make('content.street')
                                            ->required()
                                            ->live()
                                            ->label('Logradouro')
                                            ->helperText('Rua, Avenida, Praça, etc.'),
                                        TextInput::make('content.number')
                                            ->required()
                                            ->maxLength(20)
                                            ->live()
                                            ->label('Número')
                                            ->helperText('Número, KM ou S/N.'),
                                        TextInput::make('content.complement')
                                            ->maxLength(255)
                                            ->label('Complemento / Referência')
                                            ->helperText('Lote, quadra, ponto de referência.'),
                                        TextInput::make('content.city')
                                            ->required()
                                            ->live()
                                            ->label('Cidade')
                                            ->helperText('Cidade onde o serviço será executado.'),
                                        TextInput::make('content.neighborhood')
                                            ->required()
                                            ->live()
                                            ->label('Bairro')
                                            ->helperText('Nome do bairro ou localidade.'),
                                        TextInput::make('content.state')
                                            ->required()
                                            ->live()
                                            ->label('UF')
                                            ->helperText('Estado (Ex: SP, RJ, MG).'),
                                    ]),

                                Section::make('Entrega')
                                    ->description('Quando e como o cliente quer receber o concreto.')
                                    ->icon('heroicon-o-truck')
                                    ->columns(3)
                                    ->schema([
                                        DatePicker::make('content.delivery_date')
                                            ->label('Data desejada')
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->prefixIcon('heroicon-m-calendar-days'),
                                        Select::make('content.delivery_period')
                                            ->label('Período')
                                            ->options(\App\Livewire\Budget::DELIVERY_PERIODS),
                                        Select::make('content.needs_pump')
                                            ->label('Bombeamento')
                                            ->options(\App\Livewire\Budget::PUMP_OPTIONS),
                                        Textarea::make('content.notes')
                                            ->label('Observações do cliente')
                                            ->helperText('Tipo de obra, acesso do caminhão, restrições de horário.')
                                            ->rows(3)
                                            ->maxLength(1000)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tabs\Tab::make('Itens do Pedido')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([

                                Repeater::make('budgetItems')
                                    ->relationship()
                                    ->label('Itens do Orçamento')
                                    ->itemLabel(fn (array $state): ?string => ($state['product_id'] ? Product::find($state['product_id'])?->name : 'Novo Item').
                                        ($state['quantity'] ? ' - Qtd: '.BudgetCalculatorService::formatQuantity(floatval($state['quantity'])) : '')
                                    )
                                    ->collapsible()
                                    ->cloneable()
                                    ->helperText('Adicione aqui os itens que farão parte do orçamento final.')
                                    ->afterStateHydrated(function (Repeater $component, $state, ?Budget $record, Set $set, Get $get) {
                                        if (is_array($state) && count($state) > 0) {
                                            $needsUpdate = false;
                                            foreach ($state as $key => $item) {
                                                if (empty($item['price']) || floatval($item['price']) == 0) {
                                                    if (! empty($item['product_option_id'])) {
                                                        $option = ProductOption::find($item['product_option_id']);
                                                        if ($option && $option->price > 0) {
                                                            $state[$key]['price'] = number_format($option->price, 2, ',', '.');
                                                            $needsUpdate = true;
                                                        }
                                                    }
                                                } else {
                                                    $rawPrice = $item['price'];
                                                    if (is_numeric($rawPrice) && ! str_contains((string) $rawPrice, ',')) {
                                                        $state[$key]['price'] = number_format(floatval($rawPrice), 2, ',', '.');
                                                        $needsUpdate = true;
                                                    }
                                                }
                                            }
                                            if ($needsUpdate) {
                                                $component->state($state);
                                                self::calculateTotalFromRepeater($get, $set);
                                            }
                                        }

                                        // ... existing hydrated logic ...
                                        if (empty($state) && $record && ! empty($record->content['products'] ?? [])) {
                                            $items = [];
                                            foreach ($record->content['products'] as $req) {
                                                // Prioritize the price from the request (snapshot)
                                                $snapshotPrice = $req['price'] ?? null;

                                                if ($snapshotPrice === null) {
                                                    $option = ProductOption::find($req['product_option'] ?? 0);
                                                    $snapshotPrice = $option?->price ?? 0;
                                                }

                                                $items[] = [
                                                    'product_id' => $req['product'] ?? null,
                                                    'product_option_id' => $req['product_option'] ?? null,
                                                    'quantity' => $req['quantity'] ?? 1,
                                                    'price' => $snapshotPrice,
                                                ];
                                            }
                                            $component->state($items);

                                            // Trigger total calculation after auto-filling
                                            self::calculateTotalFromRepeater($get, $set);
                                        }
                                    })
                                    ->schema([
                                        Grid::make(12)
                                            ->schema([
                                                Select::make('product_id')
                                                    ->label('Produto')
                                                    ->options(Product::pluck('name', 'id'))
                                                    ->searchable()
                                                    ->required()
                                                    ->reactive()
                                                    ->columnSpan(6)
                                                    ->afterStateUpdated(fn (Set $set) => $set('product_option_id', null)),
                                                Select::make('product_option_id')
                                                    ->label('Opção/Resistência')
                                                    ->options(fn (Get $get) => ProductOption::where('product_id', $get('product_id'))->get()->pluck('name', 'id'))
                                                    ->required()
                                                    ->live()
                                                    ->columnSpan(6)
                                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                        if ($state) {
                                                            $option = ProductOption::find($state);
                                                            $set('price', number_format($option?->price ?? 0, 2, ',', '.'));
                                                        }
                                                        self::calculateTotalInsideRepeater($get, $set);
                                                    })
                                                    ->searchable(),
                                                TextInput::make('quantity')
                                                    ->label('Quantidade')
                                                    ->numeric()
                                                    ->required()
                                                    ->default(1)
                                                    ->live(debounce: 500)
                                                    ->columnSpan(4)
                                                    ->step(0.5)
                                                    ->minValue(0.5)
                                                    ->suffix(fn (Get $get) => ProductOption::find($get('product_option_id'))?->unit?->value ?? '')
                                                    ->formatStateUsing(fn ($state) => $state === null ? null : round(floatval($state), 2))
                                                    ->dehydrateStateUsing(fn ($state) => round(floatval($state), 2))
                                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                        self::calculateTotalInsideRepeater($get, $set);
                                                    }),
                                                TextInput::make('price')
                                                    ->label('Preço Unit.')
                                                    ->prefix('R$')
                                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
                                                    ->stripCharacters('.')
                                                    ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (float) $state : (float) str_replace(',', '.', str_replace('.', '', (string) $state)))
                                                    ->required()
                                                    ->dehydrated()
                                                    ->readonly()
                                                    ->extraInputAttributes(['style' => 'cursor: not-allowed;'])
                                                    ->live()
                                                    ->columnSpan(4),
                                                Placeholder::make('total_item')
                                                    ->label('Total Item')
                                                    ->columnSpan(4)
                                                    ->content(function (Get $get) {
                                                        $qty = floatval($get('quantity') ?? 0);
                                                        $rawPrice = $get('price') ?? 0;
                                                        $price = is_numeric($rawPrice) ? floatval($rawPrice) : floatval(str_replace(',', '.', str_replace('.', '', (string) $rawPrice)));

                                                        return 'R$ '.number_format($qty * $price, 2, ',', '.');
                                                    }),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->live(debounce: 1000)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateTotalFromRepeater($get, $set);
                                    }),

                                Section::make('Precificação e Custos')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->description('Definição de valores, impostos e descontos.')
                                    ->columns(4)
                                    ->schema([
                                        TextInput::make('content.subtotal')
                                            ->live(debounce: 1000)
                                            ->dehydrated()
                                            ->readonly()
                                            ->extraInputAttributes(['style' => 'cursor: not-allowed;'])
                                            ->label('Subtotal de Itens')
                                            ->helperText('Soma dos itens acima.')
                                            ->prefix('R$')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
                                            ->stripCharacters('.')
                                            ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (float) $state : (float) str_replace(',', '.', str_replace('.', '', (string) $state))),
                                        TextInput::make('content.shipping')
                                            ->label('Frete')
                                            ->live(debounce: 1000)
                                            ->dehydrated()
                                            ->prefix('+ R$')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
                                            ->stripCharacters('.')
                                            ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (float) $state : (float) str_replace(',', '.', str_replace('.', '', (string) $state)))
                                            ->required()
                                            ->default(0)
                                            ->helperText('Custo de deslocamento.')
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calculateTotalFromRepeater($get, $set);
                                            }),
                                        TextInput::make('content.tax')
                                            ->label('Taxas / Outros')
                                            ->live(debounce: 1000)
                                            ->dehydrated()
                                            ->prefix('+ R$')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
                                            ->stripCharacters('.')
                                            ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (float) $state : (float) str_replace(',', '.', str_replace('.', '', (string) $state)))
                                            ->required()
                                            ->default(0)
                                            ->helperText('Serviços extras ou taxas.')
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calculateTotalFromRepeater($get, $set);
                                            }),
                                        TextInput::make('content.discount')
                                            ->label('Desconto')
                                            ->live(debounce: 1000)
                                            ->dehydrated()
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
                                            ->stripCharacters('.')
                                            ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (float) $state : (float) str_replace(',', '.', str_replace('.', '', (string) $state)))
                                            ->required()
                                            ->default(0)
                                            ->prefix('- R$')
                                            ->helperText('Valor a subtrair.')
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calculateTotalFromRepeater($get, $set);
                                            }),
                                        TextInput::make('content.total')
                                            ->label('Valor Total Final')
                                            ->live()
                                            ->dehydrated()
                                            ->readonly()
                                            ->extraInputAttributes(['style' => 'cursor: not-allowed;'])
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
                                            ->stripCharacters('.')
                                            ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (float) $state : (float) str_replace(',', '.', str_replace('.', '', (string) $state)))
                                            ->required()
                                            ->prefix('R$')
                                            ->helperText('Valor final do orçamento.'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Rota')
                            ->icon('heroicon-o-map')
                            ->schema([
                                Placeholder::make('map_route')
                                    ->label('')
                                    ->content(function (Get $get) {
                                        // Endereço da Empresa
                                        $companyAddressParts = array_filter([
                                            Setting::get('company.address.street'),
                                            Setting::get('company.address.number'),
                                            Setting::get('company.address.neighborhood'),
                                            Setting::get('company.address.city'),
                                            Setting::get('company.address.state'),
                                            Setting::get('company.address.postcode'),
                                        ], fn ($val) => ! is_null($val) && $val !== '');

                                        $companyAddress = implode(', ', $companyAddressParts);

                                        // Endereço do Cliente
                                        $customerAddressParts = array_filter([
                                            $get('content.street'),
                                            $get('content.number'),
                                            $get('content.neighborhood'),
                                            $get('content.city'),
                                            $get('content.state'),
                                            $get('content.postcode'),
                                        ], fn ($val) => ! is_null($val) && $val !== '');

                                        $customerAddress = implode(', ', $customerAddressParts);

                                        if (empty($companyAddress) || empty($customerAddress)) {
                                            return new HtmlString('<div class="p-4 bg-gray-50 rounded-lg text-center text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">Preencha o endereço do cliente para traçar a rota.</div>');
                                        }

                                        $mapUrl = 'https://www.google.com/maps/dir/'.urlencode($companyAddress).'/'.urlencode($customerAddress);

                                        return new HtmlString('
                                            <div class="flex flex-col items-center justify-center p-6 border rounded-xl shadow-sm dark:border-gray-700">
                                                <svg class="w-12 h-12 text-primary-500 mb-4" style="width: 3rem; height: 3rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                <h3 class="text-lg font-bold text-gray-900 mb-2 dark:text-white">Rota via Google Maps</h3>
                                                <p class="text-sm text-gray-500 mb-4 text-center dark:text-gray-400">Clique no botão abaixo para ver o trajeto exato até o cliente.</p>
                                                <a href="'.$mapUrl.'" target="_blank" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                                    Abrir no Google Maps
                                                </a>
                                            </div>
                                        ');
                                    }),
                            ]),
                        Tabs\Tab::make('Ações')
                            ->icon('heroicon-o-bolt')
                            ->visible(fn ($livewire) => $livewire instanceof Pages\EditBudget)
                            ->schema([
                                Placeholder::make('pricing_warning')
                                    ->label('')
                                    ->content(new HtmlString('
                                        <div class="rounded-xl border border-warning-600/20 bg-warning-500/5 p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="rounded-full bg-warning-500 p-1 text-white">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-warning-600 uppercase tracking-wider">Atenção: Precificação Pendente</h4>
                                                    <p class="text-xs text-warning-600/80">Você precisa preencher os campos de Frete, Taxas e Desconto na aba anterior e <b>SALVAR</b> o orçamento antes de emitir o documento.</p>
                                                </div>
                                            </div>
                                        </div>
                                    '))
                                    ->visible(fn (Budget $record) => ! isset($record->content['tax']) ||
                                        ! isset($record->content['discount']) ||
                                        ! isset($record->content['shipping'])
                                    ),

                                Grid::make(3)->schema([
                                    Section::make('Compartilhar Link')
                                        ->description('Gere o PDF e copie o link.')
                                        ->icon('heroicon-o-link')
                                        ->columnSpan(['default' => 3, 'md' => 1])
                                        ->schema([
                                            Actions::make([
                                                Action::make('generate_pdf_share')
                                                    ->label('Gerar Link')
                                                    ->icon('heroicon-o-share')
                                                    ->color('gray')
                                                    ->button()
                                                    ->disabled(fn (Budget $record) => ! isset($record->content['tax']) ||
                                                        ! isset($record->content['discount']) ||
                                                        ! isset($record->content['shipping'])
                                                    )
                                                    ->requiresConfirmation()
                                                    ->modalHeading('Gerar Link de Compartilhamento')
                                                    ->modalDescription('O PDF será gerado e um link de download será criado.')
                                                    ->modalSubmitActionLabel('Gerar Agora')
                                                    ->action(function (Budget $record, $livewire, Set $set) {
                                                        // Se estivermos na página de edição, salva o formulário primeiro
                                                        if (method_exists($livewire, 'save')) {
                                                            $livewire->save();
                                                        }

                                                        $pdfModel = self::generatePdfModel($record->refresh());

                                                        if ($pdfModel) {
                                                            $url = $pdfModel->getDownloadUrl();

                                                            activity()
                                                                ->performedOn($record)
                                                                ->log('Gerou link de compartilhamento do PDF');

                                                            // Salva o link no content para referência futura
                                                            $content = $record->content;
                                                            $content['share_link'] = $url;

                                                            $record->withoutEvents(function () use ($record, $content, $pdfModel) {
                                                                $record->update([
                                                                    'content' => $content,
                                                                    'pdf_document' => $pdfModel->path,
                                                                ]);
                                                            });

                                                            $set('content.share_link', $url);

                                                            Notification::make()
                                                                ->title('PDF Gerado com Sucesso!')
                                                                ->success()
                                                                ->body('O documento foi gerado e está pronto para compartilhamento.')
                                                                ->persistent()
                                                                ->actions([
                                                                    \Filament\Notifications\Actions\Action::make('download')
                                                                        ->label('Baixar PDF')
                                                                        ->button()
                                                                        ->url($url, shouldOpenInNewTab: true),
                                                                ])
                                                                ->send();
                                                        }
                                                    }),
                                            ])->fullWidth(),
                                        ]),

                                    Section::make('WhatsApp')
                                        ->description('Envie direto para o cliente.')
                                        ->icon('heroicon-o-chat-bubble-left-right')
                                        ->columnSpan(['default' => 3, 'md' => 1])
                                        ->schema([
                                            Actions::make([
                                                Action::make('pdf_and_whatsapp')
                                                    ->label(fn (Budget $record) => 'Enviar via WhatsApp'.($record->notified_via_whatsapp ? ' ✅' : ''))
                                                    ->icon('heroicon-o-chat-bubble-left-right')
                                                    ->color('success')
                                                    ->button()
                                                    ->disabled(fn (Budget $record) => ! isset($record->content['tax']) ||
                                                        ! isset($record->content['discount']) ||
                                                        ! isset($record->content['shipping'])
                                                    )
                                                    ->requiresConfirmation()
                                                    ->action(function (Budget $record, $livewire, Set $set) {
                                                        if (method_exists($livewire, 'save')) {
                                                            $livewire->save();
                                                        }

                                                        $pdfModel = self::generatePdfModel($record->refresh());

                                                        if ($pdfModel) {
                                                            $url = $pdfModel->getDownloadUrl();
                                                            $content = $record->content;
                                                            $content['share_link'] = $url;

                                                            $record->withoutEvents(function () use ($record, $content, $pdfModel) {
                                                                $record->update([
                                                                    'notified_via_whatsapp' => true,
                                                                    'content' => $content,
                                                                    'pdf_document' => $pdfModel->path,
                                                                ]);
                                                            });

                                                            $set('content.share_link', $url);

                                                            activity()
                                                                ->performedOn($record)
                                                                ->log('Enviou orçamento via WhatsApp');

                                                            $whatsappService = new WhatsappService;
                                                            $message = 'Olá! Segue o link do seu orçamento: '.$url;
                                                            $waUrl = $whatsappService->generateUrl(
                                                                $record->content['customer_phone'] ?? '',
                                                                $message
                                                            );

                                                            $livewire->js("window.open('{$waUrl}', '_blank')");
                                                        }
                                                    }),
                                            ])->fullWidth(),
                                        ]),

                                    Section::make('E-mail Oficial')
                                        ->description('Envie de forma automatizada.')
                                        ->icon('heroicon-o-envelope')
                                        ->columnSpan(['default' => 3, 'md' => 1])
                                        ->schema([
                                            Actions::make([
                                                Action::make('pdf_and_email')
                                                    ->label(fn (Budget $record) => 'Enviar por E-mail'.($record->notified_via_email ? ' ✅' : ''))
                                                    ->icon('heroicon-o-envelope')
                                                    ->color('primary')
                                                    ->button()
                                                    ->disabled(fn (Budget $record) => ! isset($record->content['tax']) ||
                                                        ! isset($record->content['discount']) ||
                                                        ! isset($record->content['shipping'])
                                                    )
                                                    ->requiresConfirmation()
                                                    ->action(function (Budget $record, $livewire, Set $set) {
                                                        if (method_exists($livewire, 'save')) {
                                                            $livewire->save();
                                                        }

                                                        try {
                                                            $mailService = new SendBudgetMailService(
                                                                $record->refresh(),
                                                                $record->content['customer_email'] ?? ''
                                                            );
                                                            $mailService->dispatch();

                                                            $record->withoutEvents(function () use ($record) {
                                                                $record->update([
                                                                    'notified_via_email' => true,
                                                                ]);
                                                            });

                                                            $record->refresh();
                                                            if (! empty($record->content['share_link'])) {
                                                                $set('content.share_link', $record->content['share_link']);
                                                            }

                                                            activity()
                                                                ->performedOn($record)
                                                                ->log('Enviou orçamento por e-mail para '.($record->content['customer_email'] ?? 'cliente'));

                                                        } catch (\Exception $e) {
                                                            Notification::make()
                                                                ->title('Erro ao enviar e-mail')
                                                                ->body($e->getMessage())
                                                                ->danger()
                                                                ->send();
                                                        }
                                                    }),
                                            ])->fullWidth(),
                                        ]),
                                ]),

                                Section::make('Link de Compartilhamento Ativo')
                                    ->icon('heroicon-o-link')
                                    ->description('Este orçamento já possui um documento gerado e pronto para compartilhamento.')
                                    ->visible(fn (Get $get, Budget $record) => ! empty($get('content.share_link')) || ! empty($record->content['share_link'] ?? null))
                                    ->schema([
                                        TextInput::make('content.share_link')
                                            ->label('URL do Documento (PDF)')
                                            ->readOnly()
                                            ->suffixAction(
                                                Action::make('copyLink')
                                                    ->icon('heroicon-m-clipboard')
                                                    ->color('success')
                                                    ->tooltip('Copiar link')
                                                    ->action(function ($state) {
                                                        Notification::make()
                                                            ->title('Link copiado!')
                                                            ->success()
                                                            ->send();
                                                    })
                                                    ->extraAttributes([
                                                        'onclick' => "navigator.clipboard.writeText(\$el.closest('.fi-fo-text-input').querySelector('input').value)",
                                                    ])
                                            ),
                                    ]),

                                Section::make('Zona de Perigo')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->description('Ações críticas e irreversíveis relacionadas a este orçamento.')
                                    ->schema([
                                        Actions::make([
                                            Action::make('delete_budget')
                                                ->label('Mover para Lixeira')
                                                ->icon('heroicon-o-trash')
                                                ->color('danger')
                                                ->requiresConfirmation()
                                                ->modalHeading('Confirmar Exclusão')
                                                ->modalDescription('Esta ação enviará o orçamento para a lixeira. Para confirmar que deseja realizar esta ação, digite "DELETAR" no campo abaixo.')
                                                ->modalSubmitActionLabel('Confirmar Exclusão')
                                                ->form([
                                                    TextInput::make('confirm_text')
                                                        ->label('Confirmação de Segurança')
                                                        ->placeholder('Digite DELETAR')
                                                        ->required()
                                                        ->rules(['in:DELETAR']),
                                                ])
                                                ->action(function (Budget $record) {
                                                    $record->delete();

                                                    Notification::make()
                                                        ->title('Orçamento enviado para a lixeira!')
                                                        ->success()
                                                        ->send();

                                                    return redirect()->to(BudgetResource::getUrl('index'));
                                                }),
                                        ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Mensagem')
                            ->icon('heroicon-o-envelope')
                            ->visible(fn ($livewire) => $livewire instanceof Pages\EditBudget)
                            ->schema([
                                Section::make('Enviar Mensagem Direta')
                                    ->description('Envie um e-mail personalizado para o cliente deste orçamento.')
                                    ->icon('heroicon-o-paper-airplane')
                                    ->schema([
                                        TextInput::make('custom_email_subject')
                                            ->label('Assunto')
                                            ->dehydrated(false)
                                            ->default(fn (?Budget $record) => $record ? 'Sobre seu Orçamento: '.$record->code : ''),
                                        RichEditor::make('custom_email_message')
                                            ->label('Mensagem')
                                            ->dehydrated(false)
                                            ->placeholder('Escreva sua mensagem aqui...'),
                                        Actions::make([
                                            Action::make('send_custom_message_inline')
                                                ->label('Enviar E-mail Agora')
                                                ->icon('heroicon-o-paper-airplane')
                                                ->color('primary')
                                                ->button()
                                                ->action(function (Budget $record, Get $get, Set $set, $livewire) {
                                                    $subject = $get('custom_email_subject');
                                                    $message = $get('custom_email_message');

                                                    if (! $subject || ! $message) {
                                                        Notification::make()->title('Preencha todos os campos')->warning()->send();

                                                        return;
                                                    }

                                                    // Salva o formulário principal se necessário
                                                    if (method_exists($livewire, 'save')) {
                                                        $livewire->save();
                                                    }

                                                    $record->refresh();

                                                    try {
                                                        $customerEmail = $record->content['customer_email'] ?? null;
                                                        $customerName = $record->content['customer_name'] ?? 'Cliente';

                                                        if (! $customerEmail) {
                                                            throw new \Exception('E-mail do cliente não encontrado.');
                                                        }

                                                        // Envia o e-mail
                                                        \Illuminate\Support\Facades\Mail::to($customerEmail)->send(
                                                            new BudgetCustomMessageMail(
                                                                customSubject: $subject,
                                                                customMessage: $message,
                                                                customerName: $customerName
                                                            )
                                                        );

                                                        // Salva no Resource de Mail
                                                        Mail::create([
                                                            'email' => $customerEmail,
                                                            'name' => $customerName,
                                                            'phone' => $record->content['customer_phone'] ?? null,
                                                            'subject' => $subject,
                                                            'message' => $message,
                                                            'is_sent' => true,
                                                        ]);

                                                        activity()
                                                            ->performedOn($record)
                                                            ->withProperties(['subject' => $subject])
                                                            ->log('Enviou mensagem personalizada por e-mail');

                                                        Notification::make()
                                                            ->title('Mensagem enviada com sucesso!')
                                                            ->success()
                                                            ->send();

                                                        $set('custom_email_message', null);
                                                        $set('custom_email_subject', 'Sobre seu Orçamento: '.$record->code);

                                                    } catch (\Exception $e) {
                                                        Notification::make()
                                                            ->title('Erro ao enviar mensagem')
                                                            ->body($e->getMessage())
                                                            ->danger()
                                                            ->send();
                                                    }
                                                }),
                                        ])->alignment(Alignment::Right)->fullWidth(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BudgetHistoryRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function calculateTotalFromRepeater(Get $get, Set $set): void
    {
        $items = $get('budgetItems') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $qty = floatval($item['quantity'] ?? 0);
            $rawPrice = $item['price'] ?? 0;
            $price = is_numeric($rawPrice) ? floatval($rawPrice) : floatval(str_replace(',', '.', str_replace('.', '', (string) $rawPrice)));
            $subtotal += ($qty * $price);
        }

        $shipping = is_numeric($get('content.shipping')) ? floatval($get('content.shipping')) : floatval(str_replace(',', '.', str_replace('.', '', (string) ($get('content.shipping') ?? 0))));
        $tax = is_numeric($get('content.tax')) ? floatval($get('content.tax')) : floatval(str_replace(',', '.', str_replace('.', '', (string) ($get('content.tax') ?? 0))));
        $discount = is_numeric($get('content.discount')) ? floatval($get('content.discount')) : floatval(str_replace(',', '.', str_replace('.', '', (string) ($get('content.discount') ?? 0))));
        $total = $subtotal + $shipping + $tax - $discount;

        $set('content.subtotal', number_format($subtotal, 2, ',', '.'));
        $set('content.total', number_format($total, 2, ',', '.'));
    }

    public static function calculateTotalInsideRepeater(Get $get, Set $set): void
    {
        $items = $get('../../budgetItems') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $qty = floatval($item['quantity'] ?? 0);
            $rawPrice = $item['price'] ?? 0;
            $price = is_numeric($rawPrice) ? floatval($rawPrice) : floatval(str_replace(',', '.', str_replace('.', '', (string) $rawPrice)));
            $subtotal += ($qty * $price);
        }

        $rawShipping = $get('../../content.shipping') ?? 0;
        $shipping = is_numeric($rawShipping) ? floatval($rawShipping) : floatval(str_replace(',', '.', str_replace('.', '', (string) $rawShipping)));

        $rawTax = $get('../../content.tax') ?? 0;
        $tax = is_numeric($rawTax) ? floatval($rawTax) : floatval(str_replace(',', '.', str_replace('.', '', (string) $rawTax)));

        $rawDiscount = $get('../../content.discount') ?? 0;
        $discount = is_numeric($rawDiscount) ? floatval($rawDiscount) : floatval(str_replace(',', '.', str_replace('.', '', (string) $rawDiscount)));

        $total = $subtotal + $shipping + $tax - $discount;

        $set('../../content.subtotal', number_format($subtotal, 2, ',', '.'));
        $set('../../content.total', number_format($total, 2, ',', '.'));
    }

    public static function calculateTotal(Get $get, Set $set): void
    {
        // deprecated by calculateTotalFromRepeater but kept for backward compatibility if needed
        self::calculateTotalFromRepeater($get, $set);
    }

    public static function generatePdfModel(Budget $record): ?BudgetPdf
    {
        try {
            $pdfService = new PdfGeneratorService;
            $filename = 'budget-'.$record->code.'-'.time().'.pdf';
            $relativePath = 'budgets/'.$filename;
            $fullPath = storage_path('app/public/'.$relativePath);

            // Garantir diretório
            if (! file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            $settings = Setting::first();
            $company = $settings?->companySetting;
            $layout = $settings?->layoutSetting;

            $pdfService->saveFromView(
                'pdf.invoice',
                [
                    'state' => $record->toArray(),
                    'budget' => $record,
                    'company' => $company,
                    'layout' => $layout,
                ],
                $fullPath
            );

            return BudgetPdf::create([
                'budget_id' => $record->id,
                'filename' => $filename,
                'path' => $relativePath,
                'download_token' => Str::random(64),
                'token_expires_at' => now()->addDays(30),
                'is_active' => true,
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao gerar PDF')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction(null)
            ->recordUrl(fn (Budget $record): ?string => $record->trashed() ? null : BudgetResource::getUrl('edit', ['record' => $record]))
            ->recordClasses(fn (Budget $record) => match (true) {
                $record->trashed() => 'opacity-40 dark:opacity-40 hover:opacity-70 dark:hover:opacity-70 [&_*]:line-through',
                $record->status === 'ignored' => 'opacity-40 dark:opacity-40 hover:opacity-90 dark:hover:opacity-90',
                $record->status === 'done' => 'opacity-70 dark:opacity-70 hover:opacity-100 dark:hover:opacity-100',
                default => null,
            })
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->label('Código')
                    ->toggleable()
                    ->icon(fn (Budget $record) => $record->trashed() ? 'heroicon-o-trash' : null)
                    ->iconColor('danger'),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendente',
                        'on going' => 'Em Andamento',
                        'done' => 'Concluído',
                        'ignored' => 'Arquivado',
                        default => $state,
                    })
                    ->icon(fn (Budget $record, string $state): string => $record->trashed() ? 'heroicon-o-trash' : match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'on going' => 'heroicon-o-arrow-path',
                        'done' => 'heroicon-o-check-circle',
                        'ignored' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (Budget $record, string $state): string => $record->trashed() ? 'gray' : match ($state) {
                        'pending' => 'primary',
                        'on going' => 'warning',
                        'done' => 'success',
                        'ignored' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('content.customer_name')
                    ->sortable()
                    ->searchable()
                    ->label('Cliente'),
                TextColumn::make('content.customer_phone')
                    ->label('WhatsApp')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->copyMessage('Telefone copiado!')
                    ->searchable(),
                TextColumn::make('content.total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'R$ '.number_format(floatval($state), 2, '.', ','))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i')
                    ->sortable()
                    ->label('Data'),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->alignCenter()
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Lixeira')
                    ->placeholder('Sem excluídos')
                    ->trueLabel('Apenas excluídos')
                    ->falseLabel('Com excluídos')
                    ->native(false),
                TernaryFilter::make('is_active')
                    ->placeholder('Padrão')
                    ->default(true)
                    ->label('Exibição')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
                SelectFilter::make('status')
                    ->placeholder('Todos os Status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'on going' => 'Em Andamento',
                        'done' => 'Concluído',
                        'ignored' => 'Arquivado',
                    ])
                    ->searchable(),
            ], FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('copy_link')
                        ->label('Copiar Link do PDF')
                        ->icon('heroicon-o-clipboard-document')
                        ->color('success')
                        ->visible(fn (Budget $record) => ! $record->trashed() && ! empty($record->content['share_link']))
                        ->action(function (Budget $record) {
                            Notification::make()
                                ->title('Link copiado para a área de transferência!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('generate_link')
                        ->label('Gerar Link do PDF')
                        ->icon('heroicon-o-link')
                        ->color('primary')
                        ->visible(fn (Budget $record) => ! $record->trashed() && empty($record->content['share_link']))
                        ->action(function (Budget $record) {
                            $pdfModel = self::generatePdfModel($record);

                            if ($pdfModel) {
                                $url = $pdfModel->getDownloadUrl();
                                $content = $record->content;
                                $content['share_link'] = $url;
                                $record->update([
                                    'content' => $content,
                                    'pdf_document' => $pdfModel->path,
                                ]);

                                Notification::make()
                                    ->title('Link do PDF gerado com sucesso')
                                    ->success()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('download_pdf')
                        ->label('Baixar PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('warning')
                        ->visible(fn (Budget $record) => ! $record->trashed())
                        ->action(function (Budget $record) {
                            $pdfModel = self::generatePdfModel($record);

                            if ($pdfModel && $pdfModel->fileExists()) {
                                return response()->download(
                                    $pdfModel->getFullPath(),
                                    'Orcamento_'.$record->code.'.pdf'
                                );
                            }
                        }),
                    Tables\Actions\Action::make('send_email')
                        ->label('Enviar por E-mail')
                        ->icon('heroicon-o-envelope')
                        ->color('primary')
                        ->visible(fn (Budget $record) => ! $record->trashed())
                        ->action(function (Budget $record) {
                            try {
                                $mailService = new SendBudgetMailService(
                                    $record,
                                    $record->content['customer_email'] ?? ''
                                );
                                $mailService->dispatch();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Erro ao enviar e-mail')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('share_whatsapp')
                        ->label('Compartilhar WhatsApp')
                        ->icon('heroicon-o-phone')
                        ->color('success')
                        ->visible(fn (Budget $record) => ! $record->trashed())
                        ->action(function (Budget $record) {
                            if (empty($record->content['share_link'] ?? null)) {
                                $pdfModel = self::generatePdfModel($record);
                                if ($pdfModel) {
                                    $url = $pdfModel->getDownloadUrl();
                                    $content = $record->content;
                                    $content['share_link'] = $url;
                                    $record->update([
                                        'content' => $content,
                                        'pdf_document' => $pdfModel->path,
                                    ]);
                                }
                            }

                            $whatsappService = new WhatsappService;
                            $message = 'Olá! Segue o link do seu orçamento: '.($record->content['share_link'] ?? '');
                            $url = $whatsappService->generateUrl(
                                $record->content['customer_phone'] ?? '',
                                $message
                            );

                            return redirect()->away($url);
                        }),
                    Tables\Actions\EditAction::make('edit')
                        ->label('Editar')
                        ->visible(fn (Budget $record) => ! $record->trashed()),
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir')
                        ->visible(fn (Budget $record) => ! $record->trashed()),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Excluir Permanente')
                        ->visible(fn (Budget $record) => $record->trashed()),
                    Tables\Actions\RestoreAction::make()
                        ->label('Restaurar')
                        ->visible(fn (Budget $record) => $record->trashed()),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Excluir Permanente Selecionados'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar Selecionados'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
            'bin' => Pages\BudgetBin::route('/bin'),
        ];
    }
}
