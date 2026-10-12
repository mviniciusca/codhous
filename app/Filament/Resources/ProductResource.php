<?php

namespace App\Filament\Resources;

use App\Enums\ProductUnit;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\ProductOptionRelationManager;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\BudgetCalculatorService;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Orçamentos';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $breadcrumb = 'Produtos e Serviços';

    protected static ?string $recordTitleAttribute = 'name';

    /** Faixas de margem bruta para colorir os indicadores */
    public const MARGIN_GOOD = 30;

    public const MARGIN_LOW = 15;

    public static function getNavigationLabel(): string
    {
        return 'Produtos e Serviços';
    }

    public static function getModelLabel(): string
    {
        return 'Produto ou Serviço';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Produtos e Serviços';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->columnSpan(['default' => 3, 'lg' => 2])
                    ->schema([
                        Section::make('Produto')
                            ->icon('heroicon-o-shopping-bag')
                            ->compact()
                            ->columns(['default' => 1, 'md' => 2])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nome')
                                    ->placeholder('Ex: Concreto Usinado')
                                    ->helperText('Como aparece no formulário de orçamento do site.')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpanFull(),
                                Select::make('unit')
                                    ->label('Unidade de venda')
                                    ->options(ProductUnit::class)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->helperText('Todas as variações são vendidas nesta unidade.'),
                                TextInput::make('min_quantity')
                                    ->label('Pedido mínimo')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0.5)
                                    ->step(fn (Get $get) => self::unitFromState($get)?->isDecimal() === false ? 1 : 0.5)
                                    ->integer(fn (Get $get) => self::unitFromState($get)?->isDecimal() === false)
                                    ->suffix(fn (Get $get) => self::unitFromState($get)?->short())
                                    ->formatStateUsing(fn ($state) => $state === null ? null : (float) $state)
                                    ->helperText('O site não aceita pedidos abaixo disso.'),
                                Textarea::make('description')
                                    ->label('Descrição')
                                    ->placeholder('Ex: Concreto dosado em central, entregue por caminhão betoneira.')
                                    ->helperText('Opcional. Usada nos posts do Estúdio de Posts.')
                                    ->rows(2)
                                    ->autosize()
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),

                        // Na criação, as variações entram junto com o produto
                        Section::make('Variações e preços')
                            ->icon('heroicon-o-currency-dollar')
                            ->description('Traços, acabamentos ou modelos. Cada um com seu preço de venda e custo.')
                            ->compact()
                            ->visible(fn (string $operation) => $operation === 'create')
                            ->schema([
                                Repeater::make('productOption')
                                    ->relationship()
                                    ->hiddenLabel()
                                    ->addActionLabel('Adicionar variação')
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->reorderable(false)
                                    ->columns(['default' => 2, 'md' => 12])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Variação')
                                            ->placeholder('Ex: FCK 25')
                                            ->required()
                                            ->distinct()
                                            ->maxLength(255)
                                            ->columnSpan(['default' => 2, 'md' => 4]),
                                        self::moneyInput('price', 'Preço')
                                            ->required()
                                            ->columnSpan(['default' => 1, 'md' => 3]),
                                        self::moneyInput('cost', 'Custo')
                                            ->columnSpan(['default' => 1, 'md' => 3]),
                                        Placeholder::make('margin')
                                            ->label('Margem')
                                            ->content(fn (Get $get) => self::marginBadge(
                                                self::parseMoney($get('price')),
                                                filled($get('cost')) ? self::parseMoney($get('cost')) : null,
                                            ))
                                            ->columnSpan(['default' => 2, 'md' => 2]),
                                    ]),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['default' => 3, 'lg' => 1])
                    ->schema([
                        Section::make('Disponibilidade')
                            ->icon('heroicon-o-eye')
                            ->compact()
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Ativo')
                                    ->default(true)
                                    ->helperText('Desativado, some do site e de novos orçamentos. Orçamentos antigos não mudam.'),
                                Placeholder::make('site_status')
                                    ->hiddenLabel()
                                    ->visible(fn (?Product $record) => $record?->exists)
                                    ->content(fn (Product $record) => new HtmlString($record->isSellable()
                                        ? '<span style="color: rgb(var(--success-600))">● Aparece no formulário do site</span>'
                                        : '<span style="color: rgb(var(--warning-600))">● Não aparece no site'.($record->is_active ? ' (sem variações ativas)' : '').'</span>')),
                            ]),

                        Section::make('Resumo')
                            ->icon('heroicon-o-chart-bar')
                            ->compact()
                            ->visible(fn (?Product $record) => $record?->exists)
                            ->schema([
                                Placeholder::make('summary_options')
                                    ->label('Variações ativas')
                                    ->inlineLabel(false)
                                    ->content(fn (Product $record) => $record->activeOptions()->count().' de '.$record->productOption()->count()),
                                Placeholder::make('summary_prices')
                                    ->label('Preços')
                                    ->inlineLabel(false)
                                    ->content(fn (Product $record) => self::priceRangeLabel($record) ?? '—'),
                                Placeholder::make('summary_margin')
                                    ->label('Margem média')
                                    ->inlineLabel(false)
                                    ->content(fn (Product $record) => self::averageMarginBadge($record)),
                                Placeholder::make('summary_usage')
                                    ->label('Em orçamentos')
                                    ->inlineLabel(false)
                                    ->content(function (Product $record) {
                                        $total = $record->budgetItems()->count();
                                        $recent = $record->budgetItems()->where('created_at', '>=', now()->subDays(90))->count();

                                        return $total === 0 ? 'Nunca orçado' : "{$total} no total · {$recent} nos últimos 90 dias";
                                    }),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('productOption')
                ->withCount([
                    'productOption as options_count',
                    'activeOptions as active_options_count',
                    'budgetItems as recent_uses' => fn (Builder $q) => $q->where('created_at', '>=', now()->subDays(90)),
                ]))
            ->defaultSort('sort')
            ->reorderable('sort')
            ->reorderRecordsTriggerAction(fn (Tables\Actions\Action $action, bool $isReordering) => $action
                ->button()
                ->color('gray')
                ->label($isReordering ? 'Concluir ordenação' : 'Ordenar no site'))
            ->paginated(false)
            ->searchPlaceholder('Buscar produto')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->emptyStateHeading('Nenhum produto cadastrado')
            ->emptyStateDescription('Cadastre os produtos e serviços que aparecem no formulário de orçamento do site.')
            ->columns([
                TextColumn::make('name')
                    ->label('Produto')
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->description(fn (Product $record) => $record->active_options_count.' de '.$record->options_count.' '.($record->options_count === 1 ? 'variação ativa' : 'variações ativas')),
                TextColumn::make('unit')
                    ->label('Unidade')
                    ->formatStateUsing(fn (?ProductUnit $state) => $state?->short())
                    ->badge()
                    ->color('gray'),
                TextColumn::make('price_range')
                    ->label('Preço')
                    ->state(fn (Product $record) => self::priceRangeLabel($record))
                    ->placeholder('Sem preço')
                    ->weight(FontWeight::Medium),
                TextColumn::make('min_quantity')
                    ->label('Mínimo')
                    ->formatStateUsing(fn ($state, Product $record) => BudgetCalculatorService::formatQuantityWithUnit((float) $state, $record->unit?->value))
                    ->toggleable(),
                TextColumn::make('margin')
                    ->label('Margem média')
                    ->state(fn (Product $record) => self::averageMargin($record))
                    ->formatStateUsing(fn ($state) => $state === null ? null : number_format($state, 1, ',', '.').'%')
                    ->placeholder('Sem custo')
                    ->badge()
                    ->color(fn ($state) => self::marginColor($state)),
                TextColumn::make('recent_uses')
                    ->label('Orçado (90 dias)')
                    ->formatStateUsing(fn ($state) => $state.' '.((int) $state === 1 ? 'vez' : 'vezes'))
                    ->alignCenter()
                    ->color(fn ($state) => (int) $state === 0 ? 'gray' : null)
                    ->toggleable(),
                TextColumn::make('site')
                    ->label('No site')
                    ->state(fn (Product $record) => $record->is_active && $record->active_options_count > 0 && ! $record->trashed() ? 'Visível' : 'Oculto')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Visível' ? 'success' : 'gray')
                    ->icon(fn (string $state) => $state === 'Visível' ? 'heroicon-m-eye' : 'heroicon-m-eye-slash')
                    ->tooltip(fn (Product $record) => match (true) {
                        $record->trashed() => 'Na lixeira',
                        ! $record->is_active => 'Produto desativado',
                        $record->active_options_count === 0 => 'Nenhuma variação ativa',
                        default => 'Aparece no formulário de orçamento',
                    }),
                ToggleColumn::make('is_active')
                    ->label('Ativo')
                    ->alignCenter(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
                Tables\Filters\TrashedFilter::make()
                    ->label('Lixeira')
                    ->placeholder('Sem os da lixeira')
                    ->trueLabel('Com os da lixeira')
                    ->falseLabel('Somente lixeira'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Abrir')
                    ->iconButton()
                    ->icon('heroicon-m-pencil-square'),
                ActionGroup::make([
                    Tables\Actions\ReplicateAction::make()
                        ->label('Duplicar com variações')
                        ->icon('heroicon-m-document-duplicate')
                        ->excludeAttributes(['options_count', 'active_options_count', 'recent_uses'])
                        ->beforeReplicaSaved(function (Product $replica) {
                            $replica->name = $replica->name.' (cópia)';
                            $replica->is_active = false;
                            $replica->sort = (int) Product::max('sort') + 1;
                        })
                        ->after(function (Product $replica, Product $record) {
                            $record->productOption->each(fn (ProductOption $option) => $option
                                ->replicate(['slug'])
                                ->fill(['product_id' => $replica->id])
                                ->save());
                        })
                        ->successNotificationTitle('Produto duplicado (inativo até você revisar)')
                        ->successRedirectUrl(fn (Product $replica) => self::getUrl('edit', ['record' => $replica])),
                    Tables\Actions\DeleteAction::make()
                        ->label('Mover para a lixeira'),
                    Tables\Actions\RestoreAction::make()
                        ->label('Restaurar'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Excluir definitivamente')
                        ->visible(fn (Product $record) => $record->trashed() && ! $record->isUsedInBudgets()),
                ])->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('adjust_prices')
                        ->label('Reajustar preços')
                        ->icon('heroicon-m-arrow-trending-up')
                        ->form(self::priceAdjustmentForm())
                        ->action(function (Collection $records, array $data) {
                            $options = ProductOption::whereIn('product_id', $records->pluck('id'))->get();
                            self::adjustPrices($options, $data);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Mover para a lixeira'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar'),
                ]),
            ]);
    }

    /**
     * Campos do reajuste em lote (usado na lista de produtos e nas variações).
     */
    public static function priceAdjustmentForm(): array
    {
        return [
            TextInput::make('percent')
                ->label('Reajuste')
                ->helperText('Use número negativo para reduzir. Ex: 8 = +8%, -5 = -5%.')
                ->numeric()
                ->required()
                ->suffix('%')
                ->minValue(-90)
                ->maxValue(500),
            Select::make('target')
                ->label('Aplicar em')
                ->options([
                    'price' => 'Preço de venda',
                    'cost' => 'Custo',
                    'both' => 'Preço e custo',
                ])
                ->default('price')
                ->required()
                ->native(false),
            Select::make('rounding')
                ->label('Arredondar para')
                ->options([
                    '0.01' => 'Centavos (sem arredondar)',
                    '0.5' => 'R$ 0,50',
                    '1' => 'R$ 1,00',
                    '5' => 'R$ 5,00',
                ])
                ->default('0.01')
                ->required()
                ->native(false),
        ];
    }

    /**
     * Aplica o reajuste e informa quantos preços mudaram. Cada mudança fica no histórico da variação.
     */
    public static function adjustPrices(iterable $options, array $data): void
    {
        $factor = 1 + ((float) $data['percent'] / 100);
        $step = (float) $data['rounding'];
        $round = fn (float $value) => round(round($value / $step) * $step, 2);
        $fields = match ($data['target']) {
            'cost' => ['cost'],
            'both' => ['price', 'cost'],
            default => ['price'],
        };

        $changed = 0;
        foreach ($options as $option) {
            foreach ($fields as $field) {
                if ($option->{$field} !== null) {
                    $option->{$field} = max(0, $round((float) $option->{$field} * $factor));
                }
            }

            if ($option->isDirty()) {
                $option->save();
                $changed++;
            }
        }

        Notification::make()
            ->title($changed.' '.($changed === 1 ? 'variação reajustada' : 'variações reajustadas'))
            ->body(($data['percent'] > 0 ? '+' : '').str_replace('.', ',', (string) (float) $data['percent']).'% aplicado. Orçamentos já feitos mantêm o preço da época.')
            ->success()
            ->send();
    }

    public static function moneyInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->prefix('R$')
            ->live(onBlur: true)
            ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
            ->stripCharacters('.')
            ->formatStateUsing(fn ($state) => $state === null ? null : number_format((float) $state, 2, ',', '.'))
            ->dehydrateStateUsing(fn ($state) => filled($state) ? self::parseMoney($state) : null)
            ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                if (filled($value) && self::parseMoney($value) < 0) {
                    $fail('O valor não pode ser negativo.');
                }
            });
    }

    public static function parseMoney(mixed $value): float
    {
        return BudgetResource::parseMoney($value);
    }

    public static function priceRangeLabel(Product $record): ?string
    {
        $range = $record->priceRange();
        if ($range === null) {
            return null;
        }

        $unit = $record->unit?->short();
        $format = fn (float $value) => 'R$ '.number_format($value, 2, ',', '.');
        $label = $range[0] === $range[1] ? $format($range[0]) : $format($range[0]).' – '.number_format($range[1], 2, ',', '.');

        return $label.($unit ? ' /'.$unit : '');
    }

    public static function averageMargin(Product $record): ?float
    {
        $margins = $record->productOption
            ->where('is_active', true)
            ->map(fn (ProductOption $option) => $option->marginPercent())
            ->filter(fn ($margin) => $margin !== null);

        return $margins->isEmpty() ? null : round($margins->avg(), 1);
    }

    public static function averageMarginBadge(Product $record): HtmlString|string
    {
        $margin = self::averageMargin($record->loadMissing('productOption'));

        return $margin === null ? 'Informe o custo das variações' : self::marginLabel($margin);
    }

    public static function marginColor(?float $margin): string
    {
        return match (true) {
            $margin === null => 'gray',
            $margin >= self::MARGIN_GOOD => 'success',
            $margin >= self::MARGIN_LOW => 'warning',
            default => 'danger',
        };
    }

    /**
     * Ex: "32,5%" colorido conforme a faixa de margem.
     */
    public static function marginBadge(float $price, ?float $cost): HtmlString|string
    {
        if ($cost === null || $price <= 0) {
            return '—';
        }

        return self::marginLabel(round(($price - $cost) / $price * 100, 1));
    }

    public static function marginLabel(float $margin): HtmlString
    {
        $color = self::marginColor($margin);

        return new HtmlString('<span style="font-weight: 600; color: rgb(var(--'.($color === 'gray' ? 'gray' : $color).'-600))">'
            .number_format($margin, 1, ',', '.').'%</span>');
    }

    protected static function unitFromState(Get $get): ?ProductUnit
    {
        $unit = $get('unit');

        return $unit instanceof ProductUnit ? $unit : ProductUnit::tryFrom((string) $unit);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductOptionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
