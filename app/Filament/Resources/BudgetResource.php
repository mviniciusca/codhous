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
                Group::make()
                    ->columnSpanFull()
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
                    ]),
                Section::make('Conteúdo do Pedido')
                    ->description('Detalhes dos produtos, serviços e informações do cliente.')
                    ->icon('heroicon-o-shopping-bag')
                    ->headerActions([
                        Action::make('fill_all_fake_data')
                            ->label('Preencher com Dados de Teste')
                            ->icon('heroicon-o-sparkles')
                            ->color('success')
                            ->action(function (Set $set, Get $get) {
                                $fakeService = new FakeBudgetDataService();
                                $fakeData = $fakeService->generateCompleteBudgetData();

                                foreach ($fakeData as $key => $value) {
                                    $set('content.' . $key, $value);
                                }

                                Notification::make()
                                    ->title('Dados de teste gerados!')
                                    ->body('Todos os campos foram preenchidos para validação.')
                                    ->success()
                                    ->send();
                            }),
                        Action::make('clear_all_fields')
                            ->label('Limpar Tudo')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
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
                        Fieldset::make('Informações do Cliente')
                            ->columns(3)
                            ->schema([
                                Group::make()
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('content.customer_name')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label('Nome do Cliente'),
                                        TextInput::make('content.customer_email')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label('E-mail'),
                                        TextInput::make('content.customer_phone')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label('Telefone/WhatsApp'),
                                    ]),
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
                        Fieldset::make('Componentes da Obra')
                            ->columns(4)
                            ->schema([
                                TextInput::make('content.quantity')
                                    ->required()
                                    ->label('Volume Estimado')
                                    ->suffix('m³')
                                    ->helperText('Volume solicitado para concretagem.')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('content.location')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label('Local da Obra')
                                    ->helperText('Área ou elemento a ser concretado.'),
                                TextInput::make('content.fck')
                                    ->required()
                                    ->label('FCK Solicitado')
                                    ->helperText('Resistência característica do concreto.')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('content.product')
                                    ->required()
                                    ->label('Tipo de Concreto')
                                    ->helperText('Descrição do produto/serviço.')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->collapsible(),
                Section::make('Precificação e Custos')
                    ->icon('heroicon-o-currency-dollar')
                    ->description('Definição de valores, impostos e descontos.')
                    ->collapsible()
                    ->columns(6)
                    ->schema([
                        TextInput::make('content.quantity')
                            ->label('Qtd (m³)')
                            ->live()
                            ->dehydrated()
                            ->readonly()
                            ->required()
                            ->suffix('m³')
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            })
                            ->numeric(),
                        TextInput::make('content.price')
                            ->live()
                            ->dehydrated()
                            ->prefix('R$')
                            ->label('Preço Unit. (m³)')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.subtotal')
                            ->live()
                            ->dehydrated()
                            ->readonly()
                            ->label('Subtotal')
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
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.discount')
                            ->label('Desconto')
                            ->live()
                            ->dehydrated()
                            ->numeric()
                            ->required()
                            ->prefix('- R$')
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.total')
                            ->label('Valor Total')
                            ->live()
                            ->dehydrated()
                            ->disabled()
                            ->numeric()
                            ->required()
                            ->prefix('R$')
                            ->step(0.01),
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

    public static function calculateTotal(Get $get, Set $set): void
    {
        $quantity = floatval($get('content.quantity') ?? 0);
        $price = floatval($get('content.price') ?? 0);
        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);

        $subtotal = $quantity * $price;
        $total = $subtotal + $tax - $discount;

        $set('content.subtotal', number_format($subtotal, 2, '.', ''));
        $set('content.total', number_format($total, 2, '.', ''));
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
                TextColumn::make('products_display')
                    ->label('Itens Solicitados')
                    ->state(function (Budget $record): string {
                        $products = $record->content['products'] ?? [];
                        if (empty($products)) return '-';

                        $lines = [];
                        foreach ($products as $item) {
                            $productName = \App\Models\Product::find($item['product'] ?? 0)?->name ?? 'Produto';
                            $unit = \App\Models\ProductOption::find($item['product_option'] ?? 0)?->unit?->value ?? '';
                            $lines[] = "{$productName} ({$item['quantity']} {$unit})";
                        }

                        return implode(', ', $lines);
                    })
                    ->wrap()
                    ->limit(50),
                TextColumn::make('content.customer_phone')
                    ->label('WhatsApp'),
                TextColumn::make('content.total')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->toggleable(),
                TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Anexos')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->icon(fn($state) => $state > 0 ? 'heroicon-o-paper-clip' : 'heroicon-o-document')
                    ->formatStateUsing(fn($state) => $state > 0 ? $state : 'Nenhum')
                    ->sortable()
                    ->alignCenter()
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
