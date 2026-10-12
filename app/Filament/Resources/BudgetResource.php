<?php

namespace App\Filament\Resources;

use App\Enums\FakeDataEnum;
use App\Filament\Resources\BudgetResource\Pages;
use App\Filament\Resources\BudgetResource\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\BudgetResource\Widgets;
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
use App\Services\BudgetTimeline;
use App\Services\FakeBudgetDataService;
use App\Services\PdfGeneratorService;
use App\Services\SendBudgetMailService;
use App\Services\WhatsappService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column as ExcelColumn;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class BudgetResource extends Resource
{
    use \App\Filament\Concerns\BelongsToArea;

    public static function getAccessArea(): \App\Enums\AccessArea
    {
        return \App\Enums\AccessArea::Comercial;
    }

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

    /** O badge do menu mostra os pedidos que ainda esperam resposta */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()
            ->where('is_active', true)
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Novos pedidos aguardando resposta';
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\BudgetStatsOverview::class,
        ];
    }

    public const STATUSES = [
        'pending' => 'Novo pedido',
        'on going' => 'Em negociação',
        'done' => 'Fechado',
        'ignored' => 'Arquivado',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                // Etapas do orçamento + próximo passo (somente na edição)
                Placeholder::make('status_pipeline')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->visible(fn ($livewire) => $livewire instanceof Pages\EditBudget)
                    ->content(fn (Budget $record) => view('filament.budget.status-pipeline', ['record' => $record])),

                // Coluna principal: dados do pedido
                Group::make()
                    ->columnSpan(['default' => 3, 'lg' => 2])
                    ->schema([
                        self::customerSection(),
                        self::siteSection(),
                        self::deliverySection(),
                        self::itemsSection(),
                        self::activitySection(),
                    ]),

                // Coluna lateral: valores e ações, sempre visível
                Group::make()
                    ->columnSpan(['default' => 3, 'lg' => 1])
                    ->extraAttributes(['class' => 'bq-sticky'])
                    ->schema([
                        self::summarySection(),
                        self::proposalSection(),
                        self::detailsSection(),
                    ]),
            ]);
    }

    protected static function customerSection(): Section
    {
        return Section::make('Cliente')
            ->icon('heroicon-o-user')
            ->compact()
            ->headerActions([
                Action::make('ai_smart_fill')
                    ->label('Preenchimento IA')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateBudget)
                    ->action(function (Set $set) {
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
            ->columns(['default' => 1, 'md' => 3])
            ->schema([
                TextInput::make('content.customer_name')
                    ->required()
                    ->label('Nome')
                    ->placeholder('Nome completo'),
                TextInput::make('content.customer_phone')
                    ->required()
                    ->live(onBlur: true)
                    ->tel()
                    ->mask(RawJs::make(<<<'JS'
                        $input.replace(/\D/g, '').length === 0 ? '' : ($input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-99999')
                    JS))
                    ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                        $digits = strlen(preg_replace('/\D/', '', (string) $value));
                        if ($digits < 10 || $digits > 11) {
                            $fail('Informe um telefone válido com DDD.');
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => \App\Livewire\Budget::formatPhone((string) $state))
                    ->label('Telefone / WhatsApp')
                    ->placeholder('(00) 00000-0000')
                    ->suffixAction(
                        Action::make('open_whatsapp')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->tooltip('Abrir conversa no WhatsApp')
                            ->url(fn ($state) => filled($state)
                                ? (new WhatsappService)->generateUrl($state, 'Olá! Aqui é da '.(Setting::get('company.trade_name') ?: config('app.name')).', sobre o seu pedido de orçamento.')
                                : null, shouldOpenInNewTab: true)
                            ->visible(fn ($state) => strlen(preg_replace('/\D/', '', (string) $state)) >= 10)
                    ),
                TextInput::make('content.customer_email')
                    ->required()
                    ->email()
                    ->live(onBlur: true)
                    ->label('E-mail')
                    ->placeholder('cliente@email.com')
                    ->suffixAction(
                        Action::make('send_mailto')
                            ->icon('heroicon-o-envelope')
                            ->tooltip('Escrever e-mail')
                            ->url(fn ($state) => filled($state) ? 'mailto:'.$state : null)
                            ->visible(fn ($state) => filled($state))
                    ),
            ]);
    }

    protected static function siteSection(): Section
    {
        return Section::make('Local da obra')
            ->icon('heroicon-o-map-pin')
            ->compact()
            ->headerActions([
                Action::make('open_route')
                    ->label('Ver rota')
                    ->icon('heroicon-o-map')
                    ->color('gray')
                    ->link()
                    ->url(fn (Get $get) => self::routeUrl($get), shouldOpenInNewTab: true)
                    ->visible(fn (Get $get) => self::routeUrl($get) !== null),
            ])
            ->columns(['default' => 1, 'md' => 12])
            ->schema([
                TextInput::make('content.postcode')
                    ->required()
                    ->live(onBlur: true)
                    ->mask('99999-999')
                    ->label('CEP')
                    ->placeholder('00000-000')
                    ->columnSpan(['md' => 3])
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
                                } else {
                                    Notification::make()
                                        ->title('CEP fora da área de atendimento')
                                        ->body('Defina o frete manualmente no resumo.')
                                        ->warning()
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                // Fallback silencioso para não travar a UI
                            }
                        }
                    }),
                TextInput::make('content.street')
                    ->required()
                    ->label('Logradouro')
                    ->columnSpan(['md' => 7]),
                TextInput::make('content.number')
                    ->required()
                    ->maxLength(20)
                    ->label('Número')
                    ->placeholder('Nº ou S/N')
                    ->columnSpan(['md' => 2]),
                TextInput::make('content.complement')
                    ->maxLength(255)
                    ->label('Complemento / Referência')
                    ->placeholder('Lote, quadra, ponto de referência')
                    ->columnSpan(['md' => 4]),
                TextInput::make('content.neighborhood')
                    ->required()
                    ->label('Bairro')
                    ->columnSpan(['md' => 3]),
                TextInput::make('content.city')
                    ->required()
                    ->label('Cidade')
                    ->columnSpan(['md' => 3]),
                TextInput::make('content.state')
                    ->required()
                    ->maxLength(2)
                    ->label('UF')
                    ->columnSpan(['md' => 2]),
            ]);
    }

    protected static function deliverySection(): Section
    {
        return Section::make('Entrega')
            ->icon('heroicon-o-truck')
            ->compact()
            ->columns(['default' => 1, 'md' => 3])
            ->schema([
                DatePicker::make('content.delivery_date')
                    ->label('Data desejada')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->prefixIcon('heroicon-m-calendar-days')
                    ->live(),
                Select::make('content.delivery_period')
                    ->label('Período')
                    ->placeholder('A combinar')
                    ->options(\App\Livewire\Budget::DELIVERY_PERIODS),
                Select::make('content.needs_pump')
                    ->label('Bombeamento')
                    ->placeholder('Não informado')
                    ->options(\App\Livewire\Budget::PUMP_OPTIONS),
                Textarea::make('content.notes')
                    ->label('Observações do cliente')
                    ->placeholder('Tipo de obra, acesso do caminhão, restrições de horário…')
                    ->rows(2)
                    ->autosize()
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }

    protected static function itemsSection(): Section
    {
        return Section::make('Itens do pedido')
            ->icon('heroicon-o-shopping-bag')
            ->compact()
            ->description(function (Get $get) {
                $items = collect($get('budgetItems') ?? [])->filter(fn ($item) => filled($item['product_id'] ?? null));

                return $items->isEmpty()
                    ? 'Nenhum item adicionado.'
                    : $items->count().($items->count() === 1 ? ' item' : ' itens').' no pedido';
            })
            ->schema([
                Repeater::make('budgetItems')
                    ->relationship()
                    ->hiddenLabel()
                    ->addActionLabel('Adicionar item')
                    ->reorderable(false)
                    ->defaultItems(1)
                    ->itemLabel(function (array $state): string {
                        $product = Product::find($state['product_id'] ?? null)?->name;
                        $option = ProductOption::find($state['product_option_id'] ?? null)?->name;

                        return $product ? $product.($option ? ' · '.$option : '') : 'Novo item';
                    })
                    ->minItems(1)
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

                        // Pedidos antigos guardavam os itens só no content
                        if (empty($state) && $record && ! empty($record->content['products'] ?? [])) {
                            $items = [];
                            foreach ($record->content['products'] as $req) {
                                // Prioriza o preço do pedido (snapshot)
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

                            self::calculateTotalFromRepeater($get, $set);
                        }
                    })
                    // Mantém a coluna subtotal do item coerente com quantidade x preço
                    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data) => self::withItemSubtotal($data))
                    ->mutateRelationshipDataBeforeSaveUsing(fn (array $data) => self::withItemSubtotal($data))
                    ->schema([
                        Grid::make(['default' => 2, 'md' => 6])
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produto')
                                    ->options(fn (mixed $state) => self::productOptions($state))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->columnSpan(['default' => 2, 'md' => 3])
                                    ->afterStateUpdated(fn (Set $set) => $set('product_option_id', null)),
                                Select::make('product_option_id')
                                    ->label('Opção / Traço')
                                    ->options(fn (Get $get, mixed $state) => self::variationOptions($get('product_id'), $state))
                                    ->required()
                                    ->live()
                                    ->searchable()
                                    ->columnSpan(['default' => 2, 'md' => 3])
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if ($state) {
                                            $option = ProductOption::withTrashed()->find($state);
                                            $set('price', number_format((float) ($option?->price ?? 0), 2, ',', '.'));
                                            $set('unit_cost', $option?->cost);
                                        }
                                        self::calculateTotalInsideRepeater($get, $set);
                                    }),
                                TextInput::make('quantity')
                                    ->label('Qtd.')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->live(debounce: 500)
                                    ->columnSpan(['default' => 1, 'md' => 2])
                                    ->step(fn (Get $get) => Product::withTrashed()->find($get('product_id'))?->allowsFractions() === false ? 1 : 0.5)
                                    ->minValue(0.5)
                                    ->suffix(fn (Get $get) => ProductOption::find($get('product_option_id'))?->unit?->value ?? '')
                                    ->formatStateUsing(fn ($state) => $state === null ? null : round(floatval($state), 2))
                                    ->dehydrateStateUsing(fn ($state) => round(floatval($state), 2))
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateTotalInsideRepeater($get, $set)),
                                TextInput::make('price')
                                    ->label('Preço unit.')
                                    ->prefix('R$')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
                                    ->stripCharacters('.')
                                    ->dehydrateStateUsing(fn ($state) => self::parseMoney($state))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->hint(function (Get $get) {
                                        $tablePrice = (float) (ProductOption::find($get('product_option_id'))?->price ?? 0);

                                        return $tablePrice > 0 && abs($tablePrice - self::parseMoney($get('price'))) > 0.009
                                            ? 'Tabela: R$ '.number_format($tablePrice, 2, ',', '.')
                                            : null;
                                    })
                                    ->hintColor('warning')
                                    ->columnSpan(['default' => 1, 'md' => 2])
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateTotalInsideRepeater($get, $set)),
                                // Custo do momento da venda (o model grava ao salvar); usado na margem do resumo
                                Hidden::make('unit_cost')->dehydrated(false),
                                Placeholder::make('total_item')
                                    ->label('Total')
                                    ->columnSpan(['default' => 2, 'md' => 2])
                                    ->content(fn (Get $get) => new HtmlString(
                                        '<span class="bq-money">R$ '.number_format(floatval($get('quantity') ?? 0) * self::parseMoney($get('price')), 2, ',', '.').'</span>'
                                    )),
                            ]),
                    ])
                    ->columns(1)
                    ->live(debounce: 1000)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateTotalFromRepeater($get, $set)),
            ]);
    }

    protected static function activitySection(): Section
    {
        return Section::make('Histórico')
            ->icon('heroicon-o-clock')
            ->compact()
            ->collapsible()
            ->description(function (Budget $record) {
                $total = (new BudgetTimeline($record))->total();

                return $total === 1 ? '1 registro' : "{$total} registros";
            })
            ->visible(fn ($livewire) => $livewire instanceof Pages\EditBudget)
            ->schema([
                Placeholder::make('activity_timeline')
                    ->hiddenLabel()
                    ->content(function (Budget $record, $livewire) {
                        $timeline = new BudgetTimeline($record);
                        $limit = $livewire->timelineLimit ?? 15;

                        return view('filament.budget.activity-timeline', [
                            'groups' => $timeline->grouped($limit),
                            'hasMore' => $timeline->total() > $limit,
                        ]);
                    }),
            ]);
    }

    protected static function summarySection(): Section
    {
        $money = fn (TextInput $input) => $input
            ->inlineLabel()
            ->live(debounce: 1000)
            ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
            ->stripCharacters('.')
            ->dehydrateStateUsing(fn ($state) => self::parseMoney($state))
            ->required()
            ->default('0,00')
            ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateTotalFromRepeater($get, $set));

        return Section::make('Resumo')
            ->icon('heroicon-o-calculator')
            ->compact()
            ->schema([
                Hidden::make('content.subtotal')
                    ->dehydrateStateUsing(fn ($state) => self::parseMoney($state)),
                Hidden::make('content.total')
                    ->dehydrateStateUsing(fn ($state) => self::parseMoney($state)),

                Placeholder::make('summary_subtotal')
                    ->label('Itens')
                    ->inlineLabel()
                    ->content(fn (Get $get) => new HtmlString('<span class="bq-money">R$ '.e($get('content.subtotal') ?: '0,00').'</span>')),
                $money(TextInput::make('content.shipping'))
                    ->label('Frete')
                    ->prefix('+ R$'),
                $money(TextInput::make('content.tax'))
                    ->label('Taxas')
                    ->prefix('+ R$'),
                $money(TextInput::make('content.discount'))
                    ->label('Desconto')
                    ->prefix('− R$'),
                Placeholder::make('summary_margin')
                    ->label('Margem')
                    ->inlineLabel()
                    ->visible(fn (Get $get) => self::estimatedMargin($get) !== null)
                    ->content(function (Get $get) {
                        [$profit, $percent] = self::estimatedMargin($get);

                        return new HtmlString('<span class="bq-money">R$ '.number_format($profit, 2, ',', '.').'</span> '
                            .'<span class="bq-hint">('.number_format($percent, 1, ',', '.').'% sobre os itens)</span>');
                    })
                    ->hintIcon('heroicon-m-lock-closed', tooltip: 'Visível só no painel. Itens sem custo cadastrado ficam fora do cálculo.'),
                Placeholder::make('summary_total')
                    ->hiddenLabel()
                    ->content(fn (Get $get) => new HtmlString(
                        '<div class="bq-total"><span>Total</span><strong>R$ '.e($get('content.total') ?: '0,00').'</strong></div>'
                    )),
            ]);
    }

    protected static function proposalSection(): Section
    {
        return Section::make('Enviar proposta')
            ->icon('heroicon-o-paper-airplane')
            ->compact()
            ->description('Salva as alterações, gera o PDF e envia.')
            ->visible(fn ($livewire) => $livewire instanceof Pages\EditBudget)
            ->schema([
                Actions::make([
                    Action::make('pdf_and_whatsapp')
                        ->label(fn (Budget $record) => $record->notified_via_whatsapp ? 'Reenviar pelo WhatsApp' : 'Enviar pelo WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Enviar proposta pelo WhatsApp')
                        ->modalDescription(fn (Budget $record) => 'O orçamento será salvo, o PDF gerado e a conversa com '.($record->content['customer_phone'] ?? 'o cliente').' aberta numa nova aba.')
                        ->modalSubmitActionLabel('Gerar e abrir WhatsApp')
                        ->action(function (Budget $record, $livewire, Set $set) {
                            $url = self::saveAndShare($record, $livewire, $set);
                            if (! $url) {
                                return;
                            }

                            $record->withoutEvents(fn () => $record->update(['notified_via_whatsapp' => true]));
                            self::markProposalSent($record);

                            activity()
                                ->performedOn($record)
                                ->log('Enviou orçamento via WhatsApp');

                            $waUrl = (new WhatsappService)->generateUrl(
                                $record->content['customer_phone'] ?? '',
                                'Olá! Segue o link do seu orçamento '.$record->code.': '.$url
                            );

                            $livewire->js('window.open('.json_encode($waUrl).", '_blank')");
                        }),
                ])->fullWidth(),

                Actions::make([
                    Action::make('pdf_and_email')
                        ->label(fn (Budget $record) => $record->notified_via_email ? 'Reenviar por e-mail' : 'Enviar por e-mail')
                        ->icon('heroicon-o-envelope')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Enviar proposta por e-mail')
                        ->modalDescription(fn (Budget $record) => 'O PDF será enviado para '.($record->content['customer_email'] ?? 'o cliente').'.')
                        ->modalSubmitActionLabel('Enviar agora')
                        ->action(function (Budget $record, $livewire, Set $set) {
                            if (! self::saveForm($livewire)) {
                                return;
                            }

                            self::sendProposalEmail($record->refresh());
                        }),
                ])->fullWidth(),

                Actions::make([
                    Action::make('generate_pdf_share')
                        ->label('Link do PDF')
                        ->icon('heroicon-o-link')
                        ->color('gray')
                        ->action(function (Budget $record, $livewire, Set $set) {
                            $url = self::saveAndShare($record, $livewire, $set);
                            if (! $url) {
                                return;
                            }

                            activity()
                                ->performedOn($record)
                                ->log('Gerou link de compartilhamento do PDF');

                            Notification::make()
                                ->title('Link do PDF gerado')
                                ->body('Copie o link abaixo e envie para o cliente.')
                                ->success()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('open')
                                        ->label('Abrir PDF')
                                        ->button()
                                        ->url($url, shouldOpenInNewTab: true),
                                ])
                                ->send();
                        }),
                    Action::make('send_custom_message')
                        ->label('Mensagem')
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->color('gray')
                        ->modalHeading('Enviar mensagem por e-mail')
                        ->modalDescription(fn (Budget $record) => 'Para '.($record->content['customer_email'] ?? 'o cliente'))
                        ->modalSubmitActionLabel('Enviar mensagem')
                        ->fillForm(fn (Budget $record) => ['subject' => 'Sobre seu orçamento '.$record->code])
                        ->form([
                            TextInput::make('subject')
                                ->label('Assunto')
                                ->required(),
                            RichEditor::make('message')
                                ->label('Mensagem')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'undo', 'redo']),
                        ])
                        ->action(fn (Budget $record, array $data) => self::sendCustomMessage($record, $data['subject'], $data['message'])),
                ])->fullWidth(),

                TextInput::make('content.share_link')
                    ->label('Link do PDF')
                    ->readOnly()
                    ->visible(fn (Get $get) => filled($get('content.share_link')))
                    ->helperText(function (Budget $record) {
                        $latest = $record->pdfs()->latest()->first();

                        return $latest?->token_expires_at
                            ? 'Válido até '.$latest->token_expires_at->timezone(config('app.display_timezone'))->format('d/m/Y')
                            : null;
                    })
                    ->suffixAction(
                        Action::make('copyLink')
                            ->icon('heroicon-m-clipboard')
                            ->tooltip('Copiar link')
                            ->action(fn () => Notification::make()->title('Link copiado!')->success()->send())
                            ->extraAttributes([
                                'onclick' => "navigator.clipboard.writeText(\$el.closest('.fi-fo-field-wrp').querySelector('input').value)",
                            ])
                    ),
            ]);
    }

    protected static function detailsSection(): Section
    {
        return Section::make('Detalhes')
            ->icon('heroicon-o-information-circle')
            ->compact()
            ->collapsible()
            ->schema([
                TextInput::make('code')
                    ->label('Código')
                    ->inlineLabel()
                    ->default(fn () => Budget::generateUniqueCode())
                    ->disabled()
                    ->dehydrated(),
                Placeholder::make('created_at_display')
                    ->label('Recebido em')
                    ->inlineLabel()
                    ->visible(fn (?Budget $record) => $record?->exists)
                    ->content(fn (?Budget $record) => $record?->created_at?->timezone(config('app.display_timezone'))->format('d/m/Y H:i')),
                Placeholder::make('updated_at_display')
                    ->label('Atualizado em')
                    ->inlineLabel()
                    ->visible(fn (?Budget $record) => $record?->exists)
                    ->content(fn (?Budget $record) => $record?->updated_at?->timezone(config('app.display_timezone'))->format('d/m/Y H:i')),
                Toggle::make('is_active')
                    ->label('Ativo')
                    ->inlineLabel()
                    ->default(true)
                    ->helperText('Inativos ficam ocultos nos relatórios.')
                    ->live()
                    ->afterStateUpdated(function ($state, ?Budget $record) {
                        if ($record && $record->exists) {
                            $record->update(['is_active' => $state]);
                            Notification::make()
                                ->title($state ? 'Orçamento ativado!' : 'Orçamento desativado!')
                                ->success()
                                ->send();
                        }
                    }),
            ]);
    }

    /**
     * Produtos ativos, mais o já escolhido (mesmo inativo ou na lixeira) para não sumir de orçamentos antigos.
     */
    protected static function productOptions(mixed $selected): array
    {
        return Product::withTrashed()
            ->where(fn (Builder $q) => $q->where(fn (Builder $q) => $q->where('is_active', true)->whereNull('deleted_at'))
                ->when($selected, fn (Builder $q) => $q->orWhere('id', $selected)))
            ->orderBy('sort')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Product $product) => [
                $product->id => $product->name.($product->trashed() ? ' (na lixeira)' : (! $product->is_active ? ' (inativo)' : '')),
            ])
            ->all();
    }

    /**
     * Variações ativas do produto, mais a já escolhida (mesmo inativa ou arquivada).
     */
    protected static function variationOptions(mixed $productId, mixed $selected): array
    {
        if (blank($productId)) {
            return [];
        }

        return ProductOption::withTrashed()
            ->where('product_id', $productId)
            ->where(fn (Builder $q) => $q->where(fn (Builder $q) => $q->where('is_active', true)->whereNull('deleted_at'))
                ->when($selected, fn (Builder $q) => $q->orWhere('id', $selected)))
            ->orderBy('sort')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (ProductOption $option) => [
                $option->id => $option->name.' — R$ '.number_format((float) $option->price, 2, ',', '.')
                    .($option->trashed() ? ' (arquivada)' : (! $option->is_active ? ' (inativa)' : '')),
            ])
            ->all();
    }

    /**
     * Lucro bruto estimado dos itens que têm custo: [lucro, % sobre a venda desses itens].
     */
    protected static function estimatedMargin(Get $get): ?array
    {
        $revenue = 0.0;
        $profit = 0.0;

        foreach ($get('budgetItems') ?? [] as $item) {
            if (! isset($item['unit_cost']) || $item['unit_cost'] === '' || $item['unit_cost'] === null) {
                continue;
            }

            $quantity = (float) ($item['quantity'] ?? 0);
            $price = self::parseMoney($item['price'] ?? 0);
            $revenue += $quantity * $price;
            $profit += $quantity * ($price - (float) $item['unit_cost']);
        }

        if ($revenue <= 0) {
            return null;
        }

        // Desconto sai da margem
        $profit -= self::parseMoney($get('content.discount') ?? 0);

        return [$profit, $profit / $revenue * 100];
    }

    /**
     * Converte "1.234,56", "1234.56" ou número em float.
     */
    public static function parseMoney(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) str_replace(',', '.', str_replace('.', '', (string) $value));
    }

    protected static function withItemSubtotal(array $data): array
    {
        $data['subtotal'] = round(floatval($data['quantity'] ?? 0) * self::parseMoney($data['price'] ?? 0), 2);

        return $data;
    }

    protected static function routeUrl(Get $get): ?string
    {
        $company = array_filter([
            Setting::get('company.address.street'),
            Setting::get('company.address.number'),
            Setting::get('company.address.city'),
            Setting::get('company.address.state'),
        ], fn ($value) => filled($value));

        $customer = array_filter([
            $get('content.street'),
            $get('content.number'),
            $get('content.neighborhood'),
            $get('content.city'),
            $get('content.state'),
        ], fn ($value) => filled($value));

        if (empty($company) || count($customer) < 3) {
            return null;
        }

        return 'https://www.google.com/maps/dir/'.urlencode(implode(', ', $company)).'/'.urlencode(implode(', ', $customer));
    }

    /**
     * Salva o formulário sem notificação; retorna false se a validação falhar.
     */
    protected static function saveForm($livewire): bool
    {
        if (! method_exists($livewire, 'save')) {
            return true;
        }

        $livewire->save(shouldRedirect: false, shouldSendSavedNotification: false);

        return true;
    }

    /**
     * Salva, gera um novo PDF e guarda o link de compartilhamento.
     */
    protected static function saveAndShare(Budget $record, $livewire, Set $set): ?string
    {
        if (! self::saveForm($livewire)) {
            return null;
        }

        $pdfModel = self::generatePdfModel($record->refresh());
        if (! $pdfModel) {
            return null;
        }

        $url = $pdfModel->getDownloadUrl();
        $content = $record->content;
        $content['share_link'] = $url;

        $record->withoutEvents(fn () => $record->update([
            'content' => $content,
            'pdf_document' => $pdfModel->path,
        ]));

        $set('content.share_link', $url);

        return $url;
    }

    /**
     * Enviar a proposta move um pedido novo para "Em negociação".
     */
    protected static function markProposalSent(Budget $record): void
    {
        if ($record->status === 'pending') {
            $record->withoutEvents(fn () => $record->update(['status' => 'on going', 'status_changed_at' => now()]));
        }
    }

    protected static function sendCustomMessage(Budget $record, string $subject, string $message): void
    {
        try {
            $customerEmail = $record->content['customer_email'] ?? null;
            $customerName = $record->content['customer_name'] ?? 'Cliente';

            if (! $customerEmail) {
                throw new \Exception('E-mail do cliente não encontrado.');
            }

            \Illuminate\Support\Facades\Mail::to($customerEmail)->send(
                new BudgetCustomMessageMail(
                    customSubject: $subject,
                    customMessage: $message,
                    customerName: $customerName
                )
            );

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
                ->title('Mensagem enviada!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao enviar mensagem')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function getRelations(): array
    {
        return [
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

    /** Cores das etapas, iguais às do quadro e da barra de etapas */
    public const STATUS_COLORS = [
        'pending' => 'warning',
        'on going' => 'primary',
        'done' => 'success',
        'ignored' => 'gray',
    ];

    public const STATUS_ICONS = [
        'pending' => 'heroicon-m-inbox-arrow-down',
        'on going' => 'heroicon-m-chat-bubble-left-right',
        'done' => 'heroicon-m-check-circle',
        'ignored' => 'heroicon-m-archive-box',
    ];

    public static function table(Table $table): Table
    {
        $tz = config('app.display_timezone');

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutTrashed()
                ->with(['budgetItems.product', 'budgetItems.productOption', 'user'])
                ->withSum('pdfs as pdf_views', 'download_count'))
            ->recordUrl(fn (Budget $record): string => self::getUrl('edit', ['record' => $record]))
            ->recordClasses(fn (Budget $record) => $record->status === 'ignored' ? 'opacity-60' : null)
            ->searchPlaceholder('Código, cliente, telefone, e-mail ou cidade')
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->poll('60s')
            ->striped()
            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateHeading('Nenhum orçamento por aqui')
            ->emptyStateDescription('Os pedidos feitos pelo site aparecem nesta lista automaticamente.')
            ->columns([
                TextColumn::make('code')
                    ->label('Pedido')
                    ->weight(FontWeight::SemiBold)
                    ->color('primary')
                    ->searchable()
                    ->sortable(['created_at'])
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->description(fn (Budget $record) => $record->created_at?->locale('pt_BR')->diffForHumans().' · '.($record->user_id ? 'Painel' : 'Site'))
                    ->tooltip(fn (Budget $record) => 'Recebido em '.$record->created_at?->timezone($tz)->format('d/m/Y H:i').($record->user_id ? ' · criado por '.($record->user?->name ?? 'equipe') : ' pelo site')),

                TextColumn::make('customer')
                    ->label('Cliente')
                    ->state(fn (Budget $record) => $record->content['customer_name'] ?? '—')
                    ->weight(FontWeight::Medium)
                    ->description(fn (Budget $record) => collect([
                        filled($record->content['customer_phone'] ?? null) ? \App\Livewire\Budget::formatPhone($record->content['customer_phone']) : null,
                        $record->content['city'] ?? null,
                    ])->filter()->implode(' · '))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where(fn (Builder $q) => $q
                        ->where('content->customer_name', 'like', "%{$search}%")
                        ->orWhere('content->customer_email', 'like', "%{$search}%")
                        ->orWhere('content->customer_phone', 'like', "%{$search}%")
                        ->orWhere('content->city', 'like', "%{$search}%")
                        ->orWhere('content->neighborhood', 'like', "%{$search}%")))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('content->customer_name', $direction)),

                TextColumn::make('items_summary')
                    ->label('Itens')
                    ->state(fn (Budget $record) => $record->budgetItems->first()?->productLabel() ?? '—')
                    ->limit(22)
                    ->tooltip(fn (Budget $record) => $record->budgetItems->map(fn ($item) => $item->describe())->implode(' | ') ?: null)
                    ->description(function (Budget $record) {
                        $first = $record->budgetItems->first();
                        if (! $first) {
                            return null;
                        }

                        $unit = $first->unitLabel();
                        $others = $record->budgetItems->count() - 1;

                        return BudgetCalculatorService::formatQuantityWithUnit((float) $first->quantity, $unit)
                            .($others > 0 ? ' · +'.$others.' '.($others === 1 ? 'item' : 'itens') : '');
                    })
                    ->toggleable(),

                TextColumn::make('delivery')
                    ->label('Entrega')
                    ->state(fn (Budget $record) => ! empty($record->content['delivery_date'])
                        ? Carbon::parse($record->content['delivery_date'])->format('d/m/Y')
                        : null)
                    ->placeholder('A combinar')
                    ->badge()
                    ->color(fn (Budget $record) => self::deliveryColor($record))
                    ->icon(fn (Budget $record) => self::deliveryColor($record) === 'danger' ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-truck')
                    ->description(fn (Budget $record) => \App\Livewire\Budget::DELIVERY_PERIODS[$record->content['delivery_period'] ?? ''] ?? null)
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('content->delivery_date', $direction))
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Etapa')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => self::STATUS_COLORS[$state] ?? 'gray')
                    ->icon(fn (string $state) => self::STATUS_ICONS[$state] ?? null)
                    ->description(fn (Budget $record) => self::stageNote($record))
                    ->sortable(['status', 'status_changed_at']),

                TextColumn::make('proposal')
                    ->label('Proposta')
                    ->state(fn (Budget $record) => match (true) {
                        $record->notified_via_whatsapp && $record->notified_via_email => 'WhatsApp e e-mail',
                        (bool) $record->notified_via_whatsapp => 'WhatsApp',
                        (bool) $record->notified_via_email => 'E-mail',
                        default => 'Não enviada',
                    })
                    ->badge()
                    ->color(fn (Budget $record) => $record->notified_via_whatsapp || $record->notified_via_email ? 'success' : 'gray')
                    ->icon(fn (Budget $record) => $record->notified_via_whatsapp || $record->notified_via_email ? 'heroicon-m-paper-airplane' : 'heroicon-m-minus-circle')
                    ->description(fn (Budget $record) => ($views = (int) $record->pdf_views) > 0 ? "PDF aberto {$views}×" : null)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (Budget $record) => self::parseMoney($record->content['total'] ?? 0))
                    ->money('BRL', locale: 'pt_BR')
                    ->weight(FontWeight::SemiBold)
                    ->alignEnd()
                    ->sortable(query: fn (Builder $query, string $direction) => $query
                        ->orderByRaw("CAST(json_extract(content, '$.total') AS DECIMAL(14,2)) ".($direction === 'asc' ? 'asc' : 'desc'))),

                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('origin')
                    ->label('Origem')
                    ->placeholder('Todas')
                    ->options(['site' => 'Site', 'panel' => 'Painel'])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'site' => $query->whereNull('user_id'),
                        'panel' => $query->whereNotNull('user_id'),
                        default => $query,
                    }),

                SelectFilter::make('delivery')
                    ->label('Entrega')
                    ->placeholder('Qualquer data')
                    ->options([
                        'late' => 'Atrasada (em aberto)',
                        'week' => 'Próximos 7 dias',
                        'scheduled' => 'Com data definida',
                        'none' => 'A combinar',
                    ])
                    ->query(function (Builder $query, array $data) use ($tz) {
                        $today = now($tz)->toDateString();

                        return match ($data['value'] ?? null) {
                            'late' => $query->whereIn('status', ['pending', 'on going'])
                                ->whereNotNull('content->delivery_date')
                                ->where('content->delivery_date', '<', $today),
                            'week' => $query->whereBetween('content->delivery_date', [$today, now($tz)->addDays(7)->toDateString()]),
                            'scheduled' => $query->whereNotNull('content->delivery_date'),
                            'none' => $query->whereNull('content->delivery_date'),
                            default => $query,
                        };
                    }),

                SelectFilter::make('proposal')
                    ->label('Proposta')
                    ->placeholder('Todas')
                    ->options(['sent' => 'Enviada', 'not_sent' => 'Não enviada'])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'sent' => $query->where(fn (Builder $q) => $q->where('notified_via_whatsapp', true)->orWhere('notified_via_email', true)),
                        'not_sent' => $query->where('notified_via_whatsapp', false)->where('notified_via_email', false),
                        default => $query,
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->label('Período')
                    ->form([
                        DatePicker::make('from')->label('Recebido de')->native(false)->displayFormat('d/m/Y'),
                        DatePicker::make('until')->label('Até')->native(false)->displayFormat('d/m/Y'),
                    ])
                    ->columns(2)
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->where('created_at', '>=', Carbon::parse($date, $tz)->startOfDay()->utc()))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->where('created_at', '<=', Carbon::parse($date, $tz)->endOfDay()->utc())))
                    ->indicateUsing(fn (array $data) => array_values(array_filter([
                        ($data['from'] ?? null) ? 'Desde '.Carbon::parse($data['from'])->format('d/m/Y') : null,
                        ($data['until'] ?? null) ? 'Até '.Carbon::parse($data['until'])->format('d/m/Y') : null,
                    ]))),

                TernaryFilter::make('is_active')
                    ->label('Exibição')
                    ->placeholder('Todos')
                    ->trueLabel('Somente ativos')
                    ->falseLabel('Somente inativos')
                    ->default(true),
            ])
            ->filtersFormColumns(2)
            ->filtersTriggerAction(fn (Tables\Actions\Action $action) => $action->label('Filtros')->button()->color('gray'))
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->hiddenLabel()
                    ->tooltip('Conversar no WhatsApp')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->iconButton()
                    ->visible(fn (Budget $record) => strlen(preg_replace('/\D/', '', $record->content['customer_phone'] ?? '')) >= 10)
                    ->url(fn (Budget $record) => (new WhatsappService)->generateUrl(
                        $record->content['customer_phone'],
                        'Olá, '.Str::before(trim($record->content['customer_name'] ?? ''), ' ').'! Aqui é da '.(Setting::get('company.trade_name') ?: config('app.name')).", sobre o seu orçamento {$record->code}."
                    ), shouldOpenInNewTab: true),

                ActionGroup::make([
                    ActionGroup::make([
                        Tables\Actions\EditAction::make()
                            ->label('Abrir orçamento')
                            ->icon('heroicon-m-arrow-top-right-on-square'),
                        Tables\Actions\Action::make('download_pdf')
                            ->label('Baixar PDF')
                            ->icon('heroicon-m-document-arrow-down')
                            ->action(fn (Budget $record) => self::streamPdf($record)),
                        Tables\Actions\Action::make('copy_link')
                            ->label('Copiar link do PDF')
                            ->icon('heroicon-m-link')
                            ->visible(fn (Budget $record) => filled($record->content['share_link'] ?? null))
                            ->action(function (Budget $record, $livewire) {
                                $livewire->js('navigator.clipboard.writeText('.json_encode($record->content['share_link']).')');
                                Notification::make()->title('Link copiado')->success()->send();
                            }),
                        Tables\Actions\Action::make('send_email')
                            ->label('Enviar proposta por e-mail')
                            ->icon('heroicon-m-envelope')
                            ->requiresConfirmation()
                            ->modalHeading(fn (Budget $record) => 'Enviar '.$record->code.' por e-mail?')
                            ->modalDescription(fn (Budget $record) => 'O PDF com os valores atuais será enviado para '.($record->content['customer_email'] ?? 'o cliente').'.')
                            ->modalSubmitActionLabel('Enviar')
                            ->visible(fn (Budget $record) => filled($record->content['customer_email'] ?? null))
                            ->action(fn (Budget $record) => self::sendProposalEmail($record)),
                    ])->dropdown(false),

                    ActionGroup::make(
                        collect(self::STATUSES)->map(fn (string $label, string $status) => Tables\Actions\Action::make('move_'.Str::slug($status, '_'))
                            ->label('Mover para '.$label)
                            ->icon(self::STATUS_ICONS[$status])
                            ->color(self::STATUS_COLORS[$status])
                            ->visible(fn (Budget $record) => $record->status !== $status)
                            ->action(fn (Budget $record) => self::moveTo($record, $status)))
                            ->values()
                            ->all()
                    )->dropdown(false),

                    Tables\Actions\DeleteAction::make()
                        ->label('Mover para a lixeira')
                        ->modalHeading(fn (Budget $record) => 'Mover '.$record->code.' para a lixeira?'),
                ])
                    ->tooltip('Mais ações')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('move_status')
                    ->label('Mover para etapa')
                    ->icon('heroicon-m-arrows-right-left')
                    ->form([
                        Select::make('status')
                            ->label('Nova etapa')
                            ->options(self::STATUSES)
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each(fn (Budget $record) => self::moveTo($record, $data['status'], notify: false));

                        Notification::make()
                            ->title($records->count().' '.($records->count() === 1 ? 'orçamento movido' : 'orçamentos movidos').' para '.self::STATUSES[$data['status']])
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                ExportBulkAction::make()
                    ->label('Exportar para Excel')
                    ->exports([self::excelExport()]),
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Mover para a lixeira'),
            ]);
    }

    /**
     * Planilha com os dados comerciais do orçamento (lista e ações em massa).
     */
    public static function excelExport(): ExcelExport
    {
        $content = fn (string $key) => fn (Budget $record) => $record->content[$key] ?? null;

        return ExcelExport::make('orcamentos')
            ->withFilename(fn () => 'orcamentos-'.now(config('app.display_timezone'))->format('Y-m-d-His'))
            ->modifyQueryUsing(fn (Builder $query) => $query->with('budgetItems.product', 'budgetItems.productOption'))
            ->withColumns([
                ExcelColumn::make('code')->heading('Pedido'),
                ExcelColumn::make('status')->heading('Etapa')->formatStateUsing(fn ($state) => self::STATUSES[$state] ?? $state),
                ExcelColumn::make('created_at')->heading('Recebido em')->formatStateUsing(fn ($state) => $state?->timezone(config('app.display_timezone'))->format('d/m/Y H:i')),
                ExcelColumn::make('origin')->heading('Origem')->getStateUsing(fn (Budget $record) => $record->user_id ? 'Painel' : 'Site'),
                ExcelColumn::make('customer_name')->heading('Cliente')->getStateUsing($content('customer_name')),
                ExcelColumn::make('customer_phone')->heading('Telefone')->getStateUsing($content('customer_phone')),
                ExcelColumn::make('customer_email')->heading('E-mail')->getStateUsing($content('customer_email')),
                ExcelColumn::make('address')->heading('Endereço')->getStateUsing(fn (Budget $record) => collect([
                    $record->content['street'] ?? null, $record->content['number'] ?? null, $record->content['complement'] ?? null,
                ])->filter()->implode(', ')),
                ExcelColumn::make('neighborhood')->heading('Bairro')->getStateUsing($content('neighborhood')),
                ExcelColumn::make('city')->heading('Cidade')->getStateUsing($content('city')),
                ExcelColumn::make('state')->heading('UF')->getStateUsing($content('state')),
                ExcelColumn::make('postcode')->heading('CEP')->getStateUsing($content('postcode')),
                ExcelColumn::make('items')->heading('Itens')->getStateUsing(fn (Budget $record) => $record->budgetItems
                    ->map(fn ($item) => $item->describe().' — '.BudgetCalculatorService::formatQuantityWithUnit((float) $item->quantity, $item->unitLabel()).' × R$ '.number_format((float) $item->price, 2, ',', '.'))
                    ->implode("\n")),
                ExcelColumn::make('delivery_date')->heading('Entrega desejada')->getStateUsing(fn (Budget $record) => ! empty($record->content['delivery_date']) ? Carbon::parse($record->content['delivery_date'])->format('d/m/Y') : 'A combinar'),
                ExcelColumn::make('subtotal')->heading('Subtotal (R$)')->getStateUsing(fn (Budget $record) => self::parseMoney($record->content['subtotal'] ?? 0)),
                ExcelColumn::make('shipping')->heading('Frete (R$)')->getStateUsing(fn (Budget $record) => self::parseMoney($record->content['shipping'] ?? 0)),
                ExcelColumn::make('tax')->heading('Taxas (R$)')->getStateUsing(fn (Budget $record) => self::parseMoney($record->content['tax'] ?? 0)),
                ExcelColumn::make('discount')->heading('Desconto (R$)')->getStateUsing(fn (Budget $record) => self::parseMoney($record->content['discount'] ?? 0)),
                ExcelColumn::make('total')->heading('Total (R$)')->getStateUsing(fn (Budget $record) => self::parseMoney($record->content['total'] ?? 0)),
            ]);
    }

    /**
     * Texto curto sob a etapa: há quanto tempo o pedido está parado ali.
     */
    public static function stageNote(Budget $record): ?string
    {
        $days = $record->daysInStage();

        return match ($record->status) {
            'pending' => self::waitingNote($record),
            'on going' => $record->isStale() ? 'Sem retorno há '.$days.' dias' : self::proposalNote($record),
            'done' => 'Em '.$record->status_changed_at?->timezone(config('app.display_timezone'))->format('d/m/Y'),
            default => null,
        };
    }

    /**
     * Ex: "Enviada por WhatsApp · PDF aberto 2×"
     */
    protected static function proposalNote(Budget $record): ?string
    {
        $channels = collect([
            $record->notified_via_whatsapp ? 'WhatsApp' : null,
            $record->notified_via_email ? 'e-mail' : null,
        ])->filter();
        $views = (int) ($record->pdf_views ?? 0);

        return collect([
            $channels->isNotEmpty() ? 'Enviada por '.$channels->implode(' e ') : null,
            $views > 0 ? "PDF aberto {$views}×" : null,
        ])->filter()->implode(' · ') ?: null;
    }

    /**
     * Pedido novo: conta desde o recebimento, que é quanto o cliente está esperando.
     */
    protected static function waitingNote(Budget $record): string
    {
        $days = (int) $record->created_at?->diffInDays(now());

        return match (true) {
            $days === 0 => 'Recebido hoje',
            $days === 1 => 'Aguarda há 1 dia',
            default => "Aguarda há {$days} dias",
        };
    }

    protected static function deliveryColor(Budget $record): string
    {
        if (empty($record->content['delivery_date'])) {
            return 'gray';
        }

        if (! $record->isOpen()) {
            return 'gray';
        }

        $days = (int) now(config('app.display_timezone'))->startOfDay()
            ->diffInDays(Carbon::parse($record->content['delivery_date'], config('app.display_timezone'))->startOfDay(), false);

        return match (true) {
            $days < 0 => 'danger',
            $days <= 3 => 'warning',
            default => 'info',
        };
    }

    public static function moveTo(Budget $record, string $status, bool $notify = true): void
    {
        if (! array_key_exists($status, self::STATUSES) || $record->status === $status) {
            return;
        }

        $record->update(['status' => $status]);

        if ($notify) {
            Notification::make()
                ->title($record->code.' → '.self::STATUSES[$status])
                ->success()
                ->send();
        }
    }

    /**
     * Gera o PDF com os valores atuais e devolve como download, sem criar link público.
     */
    public static function streamPdf(Budget $record)
    {
        $settings = Setting::first();
        $output = (new PdfGeneratorService)->outputFromView('pdf.invoice', [
            'state' => $record->toArray(),
            'budget' => $record,
            'company' => $settings?->companySetting,
            'layout' => $settings?->layoutSetting,
        ]);

        return response()->streamDownload(fn () => print ($output), 'Orcamento_'.$record->code.'.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public static function sendProposalEmail(Budget $record): void
    {
        try {
            (new SendBudgetMailService($record, $record->content['customer_email'] ?? ''))->dispatch();

            $record->withoutEvents(fn () => $record->update(['notified_via_email' => true]));
            self::markProposalSent($record);

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
            'board' => Pages\BudgetBoard::route('/quadro'),
        ];
    }
}
