<?php

namespace App\Filament\Resources;

use App\Enums\FakeDataEnum;
use App\Filament\Resources\BudgetResource\Pages;
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
                                    ->options(fn () => Product::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->columnSpan(['default' => 2, 'md' => 3])
                                    ->afterStateUpdated(fn (Set $set) => $set('product_option_id', null)),
                                Select::make('product_option_id')
                                    ->label('Opção / Traço')
                                    ->options(fn (Get $get) => ProductOption::where('product_id', $get('product_id'))->orderBy('name')->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->searchable()
                                    ->columnSpan(['default' => 2, 'md' => 3])
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if ($state) {
                                            $option = ProductOption::find($state);
                                            $set('price', number_format($option?->price ?? 0, 2, ',', '.'));
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
                                    ->step(0.5)
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

                            try {
                                (new SendBudgetMailService($record->refresh(), $record->content['customer_email'] ?? ''))->dispatch();

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
            $record->withoutEvents(fn () => $record->update(['status' => 'on going']));
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
            'board' => Pages\BudgetBoard::route('/quadro'),
        ];
    }
}
