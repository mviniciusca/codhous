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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class BudgetResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'Budget';

    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return __('Budget');
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
                            ->inline(),
                        Select::make('status')
                            ->label('Status')
                            ->helperText('Estado atual do atendimento.')
                            ->options([
                                'pending'  => 'Pendente',
                                'on going' => 'Em Andamento',
                                'done'     => 'Concluído',
                                'ignored'  => 'Arquivado/Ignorado',
                            ]),
                        TextInput::make('code')
                            ->label('Código do Orçamento')
                            ->helperText('Identificador único gerado automaticamente.')
                            ->disabled(),
                        DateTimePicker::make('created_at')
                            ->displayFormat('d/m/Y H:i')
                            ->label('Data de Criação')
                            ->disabled()
                            ->helperText('Data e hora em que o cliente enviou o pedido.'),
                    ]),

                Tabs::make('Conteúdo do Orçamento')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Informações do Cliente')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make()
                                    ->headerActions([
                                        Action::make('fill_all_fake_data')
                                            ->label('Preencher com Dados de Teste')
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
                                                    ->title('Dados de teste gerados!')
                                                    ->success()
                                                    ->send();
                                            }),
                                        Action::make('clear_all_fields')
                                            ->label('Limpar Tudo')
                                            ->icon('heroicon-o-trash')
                                            ->color('danger')
                                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateBudget)
                                            ->requiresConfirmation()
                                            ->action(function (Set $set) {
                                                $set('content', []);
                                                Notification::make()
                                                    ->title('Campos limpos!')
                                                    ->success()
                                                    ->send();
                                            }),
                                    ])
                                    ->schema([
                                        Fieldset::make('Dados de Contato')
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('content.customer_name')
                                                    ->disabled()
                                                    ->required()
                                                    ->dehydrated()
                                                    ->label('Nome do Cliente')
                                                    ->helperText('Nome completo fornecido pelo cliente.'),
                                                TextInput::make('content.customer_email')
                                                    ->disabled()
                                                    ->required()
                                                    ->dehydrated()
                                                    ->label('E-mail')
                                                    ->helperText('Endereço de e-mail para contato.'),
                                                TextInput::make('content.customer_phone')
                                                    ->disabled()
                                                    ->required()
                                                    ->dehydrated()
                                                    ->label('Telefone/WhatsApp')
                                                    ->helperText('Número para comunicação direta.'),
                                            ]),
                                        Fieldset::make('Endereço da Obra')
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('content.postcode')
                                                    ->disabled()
                                                    ->required()
                                                    ->dehydrated()
                                                    ->label('CEP'),
                                                TextInput::make('content.street')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required()
                                                    ->label('Logradouro'),
                                                TextInput::make('content.number')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->label('Número'),
                                                TextInput::make('content.city')
                                                    ->disabled()
                                                    ->required()
                                                    ->dehydrated()
                                                    ->label('Cidade'),
                                                TextInput::make('content.neighborhood')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required()
                                                    ->label('Bairro'),
                                                TextInput::make('content.state')
                                                    ->disabled()
                                                    ->required()
                                                    ->dehydrated()
                                                    ->label('UF'),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Itens do Pedido')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([

                                Repeater::make('budgetItems')
                                    ->relationship()
                                    ->label('Itens do Orçamento')
                                    ->helperText('Adicione aqui os itens que farão parte do orçamento final.')
                                    ->afterStateHydrated(function (Repeater $component, $state, ?Budget $record, Set $set, Get $get) {
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
                                        Select::make('product_id')
                                            ->label('Produto')
                                            ->options(\App\Models\Product::pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn (Set $set) => $set('product_option_id', null)),
                                        Select::make('product_option_id')
                                            ->label('Opção/Resistência')
                                            ->options(fn (Get $get) => \App\Models\ProductOption::where('product_id', $get('product_id'))->get()->pluck('name', 'id'))
                                            ->required()
                                            ->reactive()
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
                                            ->suffix(fn (Get $get) => \App\Models\ProductOption::find($get('product_option_id'))?->unit?->value ?? ''),
                                        TextInput::make('price')
                                            ->label('Preço Unit.')
                                            ->numeric()
                                            ->prefix('R$')
                                            ->required()
                                            ->reactive()
                                            ->step(0.01),
                                        Placeholder::make('total_item')
                                            ->label('Total Item')
                                            ->content(function (Get $get) {
                                                $qty = floatval($get('quantity') ?? 0);
                                                $price = floatval($get('price') ?? 0);
                                                return 'R$ ' . number_format($qty * $price, 2, ',', '.');
                                            }),
                                    ])
                                    ->columns(5)
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
                                            ->disabled()
                                            ->numeric()
                                            ->required()
                                            ->prefix('R$')
                                            ->step(0.01)
                                            ->helperText('Valor final do orçamento.'),
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
                    ->label('WhatsApp'),
                TextColumn::make('content.total')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Enviado em'),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->alignCenter()
                    ->boolean(),
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
                            $pdfService = new \App\Services\BudgetPdfService();
                            $pdfModel = $pdfService->generatePdf($record, true);

                            if ($pdfModel) {
                                $url = $pdfModel->getDownloadUrl();
                                $content = $record->content;
                                $content['share_link'] = $url;
                                $record->update(['content' => $content]);

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
                            $pdfService = new \App\Services\BudgetPdfService();
                            $pdfModel = $pdfService->generatePdf($record, true);

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
                                $mail = new \App\Services\SendBudgetMail(
                                    $record->toArray(),
                                    $record->content['customer_email'] ?? '',
                                    new \App\Mail\BudgetMail($record->toArray())
                                );
                                $mail->dispatch();

                                Notification::make()
                                    ->title('E-mail enviado com sucesso')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Erro ao enviar e-mail')
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
                                $pdfService = new \App\Services\BudgetPdfService();
                                $pdfModel = $pdfService->generatePdf($record, true);
                                if ($pdfModel) {
                                    $url = $pdfModel->getDownloadUrl();
                                    $content = $record->content;
                                    $content['share_link'] = $url;
                                    $record->update(['content' => $content]);
                                }
                            }

                            $whatsApp = new \App\Services\WhatsAppShare();
                            $message = "Olá! Segue o link do seu orçamento: " . ($record->content['share_link'] ?? '');
                            $url = $whatsApp->generateUrl(
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
