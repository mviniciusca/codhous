<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Filament\Resources\BudgetResource\RelationManagers\BudgetHistoryRelationManager;
use App\Filament\Resources\BudgetResource\RelationManagers\DocumentsRelationManager;
use App\Models\Budget;
use App\Services\FakeBudgetDataService;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions;
use Filament\Support\Enums\Alignment;
use App\Services\PdfGeneratorService;
use App\Services\SendBudgetMailService;
use App\Services\WhatsappService;
use App\Models\BudgetPdf;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

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
            'Código'  => $record->code,
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
                Section::make('Visão Geral do Orçamento')
                    ->columns(4)
                    ->description('Informações básicas e status atual do pedido.')
                    ->icon('heroicon-o-document')
                    ->schema([
                        Toggle::make('is_active')
                            ->helperText('Define se este orçamento deve ser exibido nos relatórios ativos.')
                            ->label('Ativo')
                            ->default(true)
                            ->inline(),
                        Select::make('status')
                            ->label('Status')
                            ->helperText('Estado atual do atendimento.')
                            ->default('pending')
                            ->options([
                                'pending'  => 'Pendente',
                                'on going' => 'Em Andamento',
                                'done'     => 'Concluído',
                                'ignored'  => 'Arquivado/Ignorado',
                            ]),
                        TextInput::make('code')
                            ->label('Código do Orçamento')
                            ->placeholder('Gerado automaticamente ao salvar')
                            ->helperText('Ex: OR-2026-00001')
                            ->disabled(),
                        DateTimePicker::make('created_at')
                            ->displayFormat('d/m/Y H:i')
                            ->label('Data de Criação')
                            ->default(now())
                            ->required()
                            ->helperText('Data e hora do registro do orçamento.'),
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
                                                $fakeService = new FakeBudgetDataService();
                                                $fakeData = $fakeService->generateCompleteBudgetData();

                                                foreach ($fakeData as $key => $value) {
                                                    $set('content.' . $key, $value);
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
                                            ->visible(fn ($livewire, Get $get) => 
                                                $livewire instanceof Pages\CreateBudget && 
                                                \App\Enums\FakeDataEnum::matches($get('content') ?? [])
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
                                            ->live()
                                            ->label('E-mail')
                                            ->helperText('Endereço de e-mail para contato.'),
                                        TextInput::make('content.customer_phone')
                                            ->required()
                                            ->live()
                                            ->mask('(99)99999-9999')
                                            ->label('Telefone/WhatsApp')
                                            ->helperText('Número para comunicação direta.'),
                                        TextInput::make('content.postcode')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->mask('99999-999')
                                            ->label('CEP')
                                            ->helperText('Formato: 00000-000')
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                if (strlen(preg_replace('/[^0-9]/', '', $state ?? '')) === 8) {
                                                    try {
                                                        $finder = new \App\Services\AddressFinderService($state, $set, [
                                                            'logradouro' => 'content.street',
                                                            'bairro'     => 'content.neighborhood',
                                                            'localidade' => 'content.city',
                                                            'uf'         => 'content.state',
                                                        ], 'content.postcode');
                                                        $finder->find();
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
                                            ->live()
                                            ->label('Número')
                                            ->helperText('Número da residência ou lote.'),
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
                            ]),
                        Tabs\Tab::make('Itens do Pedido')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([

                                Repeater::make('budgetItems')
                                    ->relationship()
                                    ->label('Itens do Orçamento')
                                    ->itemLabel(fn (array $state): ?string => 
                                        ($state['product_id'] ? \App\Models\Product::find($state['product_id'])?->name : 'Novo Item') . 
                                        ($state['quantity'] ? " - Qtd: {$state['quantity']}" : "")
                                    )
                                    ->collapsible()
                                    ->helperText('Adicione aqui os itens que farão parte do orçamento final.')
                                    ->afterStateHydrated(function (Repeater $component, $state, ?Budget $record, Set $set, Get $get) {
                                        // ... existing hydrated logic ...
                                        if (empty($state) && $record && !empty($record->content['products'] ?? [])) {
                                            $items = [];
                                            foreach ($record->content['products'] as $req) {
                                                // Prioritize the price from the request (snapshot)
                                                $snapshotPrice = $req['price'] ?? null;
                                                
                                                if ($snapshotPrice === null) {
                                                    $option = \App\Models\ProductOption::find($req['product_option'] ?? 0);
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
                                        \Filament\Forms\Components\Grid::make(12)
                                            ->schema([
                                                Select::make('product_id')
                                                    ->label('Produto')
                                                    ->options(\App\Models\Product::pluck('name', 'id'))
                                                    ->searchable()
                                                    ->required()
                                                    ->reactive()
                                                    ->columnSpan(6)
                                                    ->afterStateUpdated(fn (Set $set) => $set('product_option_id', null)),
                                                Select::make('product_option_id')
                                                    ->label('Opção/Resistência')
                                                    ->options(fn (Get $get) => \App\Models\ProductOption::where('product_id', $get('product_id'))->get()->pluck('name', 'id'))
                                                    ->required()
                                                    ->reactive()
                                                    ->columnSpan(6)
                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                        if ($state) {
                                                            $option = \App\Models\ProductOption::find($state);
                                                            $set('price', $option?->price ?? 0);
                                                        }
                                                    })
                                                    ->searchable(),
                                                TextInput::make('quantity')
                                                    ->label('Quantidade')
                                                    ->numeric()
                                                    ->required()
                                                    ->default(1)
                                                    ->reactive()
                                                    ->columnSpan(4)
                                                    ->step(fn (Get $get) => \App\Models\ProductOption::find($get('product_option_id'))?->unit?->isDecimal() ? 0.01 : 1)
                                                    ->suffix(fn (Get $get) => \App\Models\ProductOption::find($get('product_option_id'))?->unit?->value ?? ''),
                                                TextInput::make('price')
                                                    ->label('Preço Unit.')
                                                    ->numeric()
                                                    ->prefix('R$')
                                                    ->required()
                                                    ->reactive()
                                                    ->columnSpan(4)
                                                    ->step(0.01),
                                                Placeholder::make('total_item')
                                                    ->label('Total Item')
                                                    ->columnSpan(4)
                                                    ->content(function (Get $get) {
                                                        $qty = floatval($get('quantity') ?? 0);
                                                        $price = floatval($get('price') ?? 0);
                                                        return 'R$ ' . number_format($qty * $price, 2, '.', ',');
                                                    }),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateTotalFromRepeater($get, $set);
                                    }),

                                Section::make('Precificação e Custos')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->description('Definição de valores, impostos e descontos.')
                                    ->columns(4)
                                    ->schema([
                                        TextInput::make('content.subtotal')
                                            ->live()
                                            ->dehydrated()
                                            ->readonly()
                                            ->label('Subtotal de Itens')
                                            ->helperText('Soma dos itens acima.')
                                            ->prefix('R$')
                                            ->numeric()
                                            ->step(0.01),
                                        TextInput::make('content.tax')
                                            ->label('Taxas/Frete')
                                            ->live()
                                            ->dehydrated()
                                            ->prefix('+ R$')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->step(0.01)
                                            ->helperText('Custos adicionais.')
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calculateTotalFromRepeater($get, $set);
                                            }),
                                        TextInput::make('content.discount')
                                            ->label('Desconto')
                                            ->live()
                                            ->dehydrated()
                                            ->numeric()
                                            ->required()
                                            ->prefix('- R$')
                                            ->step(0.01)
                                            ->helperText('Valor a subtrair.')
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calculateTotalFromRepeater($get, $set);
                                            }),
                                        TextInput::make('content.total')
                                            ->label('Valor Total Final')
                                            ->live()
                                            ->dehydrated()
                                            ->readonly()
                                            ->numeric()
                                            ->required()
                                            ->prefix('R$')
                                            ->step(0.01)
                                            ->helperText('Valor final do orçamento.'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Ações')
                            ->icon('heroicon-o-bolt')
                            ->visible(fn ($livewire) => $livewire instanceof Pages\EditBudget)
                            ->schema([
                                Section::make('Comunicação com o Cliente')
                                    ->description('Gere o documento oficial e notifique o cliente pelos canais disponíveis.')
                                    ->schema([
                                        Actions::make([
                                            Actions\Action::make('pdf_and_email')
                                                ->label('Gerar PDF e Enviar por E-mail')
                                                ->icon('heroicon-o-envelope')
                                                ->color('primary')
                                                ->requiresConfirmation()
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

                                            Actions\Action::make('generate_pdf_share')
                                                ->label('Gerar PDF e Link de Compartilhamento')
                                                ->icon('heroicon-o-share')
                                                ->color('info')
                                                ->requiresConfirmation()
                                                ->modalHeading('Gerar Link de Compartilhamento')
                                                ->modalDescription('O PDF será gerado e um link de download será criado.')
                                                ->modalSubmitActionLabel('Gerar Agora')
                                                ->action(function (Budget $record) {
                                                    $pdfModel = self::generatePdfModel($record);
                                                    
                                                    if ($pdfModel) {
                                                        $url = $pdfModel->getDownloadUrl();
                                                        
                                                        // Salva o link no content para referência futura
                                                        $content = $record->content;
                                                        $content['share_link'] = $url;
                                                        $record->update([
                                                            'content' => $content,
                                                            'pdf_document' => $pdfModel->path
                                                        ]);

                                                        Notification::make()
                                                            ->title('PDF Gerado com Sucesso!')
                                                            ->success()
                                                            ->body("O documento foi gerado e está pronto para compartilhamento.")
                                                            ->persistent()
                                                            ->actions([
                                                                \Filament\Notifications\Actions\Action::make('download')
                                                                    ->label('Baixar PDF')
                                                                    ->button()
                                                                    ->url($url, shouldOpenInNewTab: true),
                                                                \Filament\Notifications\Actions\Action::make('copy')
                                                                    ->label('Copiar Link')
                                                                    ->color('gray')
                                                                    ->extraAttributes([
                                                                        'onclick' => "navigator.clipboard.writeText('{$url}'); window.Filament.notifications.notify({ title: 'Copiado!', status: 'success' })"
                                                                    ]),
                                                            ])
                                                            ->send();
                                                    }
                                                }),

                                            Actions\Action::make('pdf_and_whatsapp')
                                                ->label('Gerar PDF e Enviar via WhatsApp')
                                                ->icon('heroicon-o-chat-bubble-left-right')
                                                ->color('success')
                                                ->requiresConfirmation()
                                                ->action(function (Budget $record) {
                                                    $pdfModel = self::generatePdfModel($record);

                                                    if ($pdfModel) {
                                                        $url = $pdfModel->getDownloadUrl();
                                                        $content = $record->content;
                                                        $content['share_link'] = $url;
                                                        $record->update([
                                                            'content' => $content,
                                                            'pdf_document' => $pdfModel->path
                                                        ]);

                                                        $whatsappService = new WhatsappService();
                                                        $message = "Olá! Segue o link do seu orçamento: " . $url;
                                                        $waUrl = $whatsappService->generateUrl(
                                                            $record->content['customer_phone'] ?? '',
                                                            $message
                                                        );

                                                        return redirect()->away($waUrl);
                                                    }
                                                }),
                                        ])
                                        ->alignment(Alignment::Center)
                                        ->fullWidth(),
                                ]),

                                Section::make('Link de Compartilhamento Ativo')
                                    ->icon('heroicon-o-link')
                                    ->description('Este orçamento já possui um documento gerado e pronto para compartilhamento.')
                                    ->visible(fn(Budget $record) => !empty($record->content['share_link'] ?? null))
                                    ->schema([
                                        TextInput::make('content.share_link')
                                            ->label('URL do Documento (PDF)')
                                            ->readOnly()
                                            ->suffixAction(
                                                \Filament\Forms\Components\Actions\Action::make('copyLink')
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
                                                        'onclick' => "navigator.clipboard.writeText(\$el.closest('.fi-fo-text-input').querySelector('input').value)"
                                                    ])
                                            ),
                                    ]),

                                Section::make('Zona de Perigo')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->description('Ações críticas e irreversíveis relacionadas a este orçamento.')
                                    ->schema([
                                        Actions::make([
                                            Actions\Action::make('delete_budget')
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
            $price = floatval($item['price'] ?? 0);
            $subtotal += ($qty * $price);
        }

        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);
        $total = $subtotal + $tax - $discount;

        $set('content.subtotal', number_format($subtotal, 2, '.', ''));
        $set('content.total', number_format($total, 2, '.', ''));
    }

    public static function calculateTotal(Get $get, Set $set): void
    {
        // deprecated by calculateTotalFromRepeater but kept for backward compatibility if needed
        self::calculateTotalFromRepeater($get, $set);
    }

    public static function generatePdfModel(Budget $record): ?BudgetPdf
    {
        try {
            $pdfService = new PdfGeneratorService();
            $filename = 'budget-' . $record->code . '-' . time() . '.pdf';
            $relativePath = 'budgets/' . $filename;
            $fullPath = storage_path('app/public/' . $relativePath);

            // Garantir diretório
            if (!file_exists(dirname($fullPath))) {
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
            ->recordUrl(fn(Budget $record): ?string => $record->trashed() ? null : BudgetResource::getUrl('edit', ['record' => $record]))
            ->recordClasses(fn(Budget $record) => match (true) {
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
                    ->icon(fn(Budget $record) => $record->trashed() ? 'heroicon-o-trash' : null)
                    ->iconColor('danger'),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'  => 'Pendente',
                        'on going' => 'Em Andamento',
                        'done'     => 'Concluído',
                        'ignored'  => 'Arquivado',
                        default    => $state,
                    })
                    ->icon(fn(Budget $record, string $state): string => $record->trashed() ? 'heroicon-o-trash' : match ($state) {
                        'pending'  => 'heroicon-o-clock',
                        'on going' => 'heroicon-o-arrow-path',
                        'done'     => 'heroicon-o-check-circle',
                        'ignored'  => 'heroicon-o-x-circle',
                        default    => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(Budget $record, string $state): string => $record->trashed() ? 'gray' : match ($state) {
                        'pending'  => 'primary',
                        'on going' => 'warning',
                        'done'     => 'success',
                        'ignored'  => 'danger',
                        default    => 'gray',
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
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format(floatval($state), 2, '.', ','))
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
                        'pending'  => 'Pendente',
                        'on going' => 'Em Andamento',
                        'done'     => 'Concluído',
                        'ignored'  => 'Arquivado',
                    ])
                    ->searchable(),
            ], FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('copy_link')
                        ->label('Copiar Link do PDF')
                        ->icon('heroicon-o-clipboard-document')
                        ->color('success')
                        ->visible(fn(Budget $record) => ! $record->trashed() && ! empty($record->content['share_link']))
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
                        ->visible(fn(Budget $record) => ! $record->trashed() && empty($record->content['share_link']))
                        ->action(function (Budget $record) {
                            $pdfModel = self::generatePdfModel($record);

                            if ($pdfModel) {
                                $url = $pdfModel->getDownloadUrl();
                                $content = $record->content;
                                $content['share_link'] = $url;
                                $record->update([
                                    'content' => $content,
                                    'pdf_document' => $pdfModel->path
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
                        ->visible(fn(Budget $record) => ! $record->trashed())
                        ->action(function (Budget $record) {
                            $pdfModel = self::generatePdfModel($record);

                            if ($pdfModel && $pdfModel->fileExists()) {
                                return response()->download(
                                    $pdfModel->getFullPath(),
                                    'Orcamento_' . $record->code . '.pdf'
                                );
                            }
                        }),
                    Tables\Actions\Action::make('send_email')
                        ->label('Enviar por E-mail')
                        ->icon('heroicon-o-envelope')
                        ->color('primary')
                        ->visible(fn(Budget $record) => ! $record->trashed())
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
                        ->visible(fn(Budget $record) => ! $record->trashed())
                        ->action(function (Budget $record) {
                            if (empty($record->content['share_link'] ?? null)) {
                                $pdfModel = self::generatePdfModel($record);
                                if ($pdfModel) {
                                    $url = $pdfModel->getDownloadUrl();
                                    $content = $record->content;
                                    $content['share_link'] = $url;
                                    $record->update([
                                        'content' => $content,
                                        'pdf_document' => $pdfModel->path
                                    ]);
                                }
                            }

                            $whatsappService = new WhatsappService();
                            $message = "Olá! Segue o link do seu orçamento: " . ($record->content['share_link'] ?? '');
                            $url = $whatsappService->generateUrl(
                                $record->content['customer_phone'] ?? '',
                                $message
                            );

                            return redirect()->away($url);
                        }),
                    Tables\Actions\EditAction::make('edit')
                        ->label('Editar')
                        ->visible(fn(Budget $record) => ! $record->trashed()),
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir')
                        ->visible(fn(Budget $record) => ! $record->trashed()),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Excluir Permanente')
                        ->visible(fn(Budget $record) => $record->trashed()),
                    Tables\Actions\RestoreAction::make()
                        ->label('Restaurar')
                        ->visible(fn(Budget $record) => $record->trashed()),
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit'   => Pages\EditBudget::route('/{record}/edit'),
            'bin'    => Pages\BudgetBin::route('/bin'),
        ];
    }
}
