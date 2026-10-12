<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use App\Models\ProductOption;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Spatie\Activitylog\Models\Activity;

class ProductOptionRelationManager extends RelationManager
{
    protected static string $relationship = 'productOption';

    protected static ?string $title = 'Variações e preços';

    protected static ?string $modelLabel = 'variação';

    protected static ?string $pluralModelLabel = 'variações';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        $unit = $this->getOwnerRecord()->unit?->short();

        return $form
            ->columns(3)
            ->schema([
                TextInput::make('name')
                    ->label('Variação')
                    ->placeholder('Ex: FCK 25 ou Polimento espelhado')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('product_id', $this->getOwnerRecord()->id),
                    )
                    ->validationMessages(['unique' => 'Já existe uma variação com esse nome (veja também as arquivadas).'])
                    ->columnSpanFull(),
                ProductResource::moneyInput('price', 'Preço de venda'.($unit ? " por {$unit}" : ''))
                    ->required(),
                ProductResource::moneyInput('cost', 'Custo'.($unit ? " por {$unit}" : ''))
                    ->helperText('Opcional. Usado só no painel.'),
                Placeholder::make('margin')
                    ->label('Margem bruta')
                    ->content(fn (Get $get) => ProductResource::marginBadge(
                        ProductResource::parseMoney($get('price')),
                        filled($get('cost')) ? ProductResource::parseMoney($get('cost')) : null,
                    )),
                Toggle::make('is_active')
                    ->label('Disponível no site e em novos orçamentos')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        $unit = $this->getOwnerRecord()->unit?->short();

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([SoftDeletingScope::class])
                ->withCount(['budgetItems as uses']))
            ->heading('Variações e preços')
            ->description('Preços por '.($unit ?? 'unidade').'. Alterações valem para novos orçamentos; os já feitos mantêm o preço da época.')
            ->recordTitleAttribute('name')
            ->defaultSort('sort')
            ->reorderable('sort')
            ->paginated(false)
            ->recordClasses(fn (ProductOption $record) => $record->trashed() ? 'opacity-50' : null)
            ->emptyStateHeading('Nenhuma variação')
            ->emptyStateDescription('Sem variações ativas, o produto não aparece no site.')
            ->columns([
                TextColumn::make('name')
                    ->label('Variação')
                    ->weight(FontWeight::Medium)
                    ->searchable()
                    ->description(fn (ProductOption $record) => $record->trashed() ? 'Arquivada' : null),
                TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL', locale: 'pt_BR')
                    ->weight(FontWeight::SemiBold)
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('cost')
                    ->label('Custo')
                    ->money('BRL', locale: 'pt_BR')
                    ->placeholder('—')
                    ->alignEnd()
                    ->color('gray'),
                TextColumn::make('margin')
                    ->label('Margem')
                    ->state(fn (ProductOption $record) => $record->marginPercent())
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 1, ',', '.').'%')
                    ->placeholder('—')
                    ->badge()
                    ->color(fn ($state) => ProductResource::marginColor($state))
                    ->alignCenter(),
                TextColumn::make('uses')
                    ->label('Em orçamentos')
                    ->formatStateUsing(fn ($state) => (int) $state === 0 ? 'Nunca' : $state.'×')
                    ->color(fn ($state) => (int) $state === 0 ? 'gray' : null)
                    ->alignCenter(),
                ToggleColumn::make('is_active')
                    ->label('Ativa')
                    ->alignCenter()
                    ->disabled(fn (ProductOption $record) => $record->trashed()),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Arquivadas')
                    ->placeholder('Sem arquivadas')
                    ->trueLabel('Com arquivadas')
                    ->falseLabel('Somente arquivadas'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nova variação')
                    ->icon('heroicon-m-plus')
                    ->modalHeading('Nova variação')
                    ->modalWidth('2xl')
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->iconButton()
                    ->icon('heroicon-m-pencil-square')
                    ->modalHeading(fn (ProductOption $record) => 'Editar '.$record->name)
                    ->modalWidth('2xl')
                    ->hidden(fn (ProductOption $record) => $record->trashed()),
                ActionGroup::make([
                    Tables\Actions\Action::make('price_history')
                        ->label('Histórico de preço')
                        ->icon('heroicon-m-clock')
                        ->modalHeading(fn (ProductOption $record) => 'Histórico de '.$record->name)
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Fechar')
                        ->modalWidth('lg')
                        ->modalContent(fn (ProductOption $record) => view('filament.products.price-history', [
                            'changes' => $this->priceHistory($record),
                        ])),
                    Tables\Actions\DeleteAction::make()
                        ->label('Arquivar')
                        ->icon('heroicon-m-archive-box')
                        ->modalHeading(fn (ProductOption $record) => 'Arquivar '.$record->name.'?')
                        ->modalDescription('Some do site e de novos orçamentos. Orçamentos antigos continuam mostrando esta variação.')
                        ->modalSubmitActionLabel('Arquivar')
                        ->successNotificationTitle('Variação arquivada'),
                    Tables\Actions\RestoreAction::make()
                        ->label('Restaurar'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Excluir definitivamente')
                        ->visible(fn (ProductOption $record) => $record->trashed() && ! $record->isUsedInBudgets()),
                ])->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('adjust_prices')
                    ->label('Reajustar preços')
                    ->icon('heroicon-m-arrow-trending-up')
                    ->form(ProductResource::priceAdjustmentForm())
                    ->action(fn (Collection $records, array $data) => ProductResource::adjustPrices($records, $data))
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('activate')
                    ->label('Ativar')
                    ->icon('heroicon-m-eye')
                    ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('deactivate')
                    ->label('Desativar')
                    ->icon('heroicon-m-eye-slash')
                    ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Arquivar'),
            ]);
    }

    /**
     * Mudanças de preço, custo, nome e status registradas pelo activity log.
     *
     * @return array<int, array{at: Carbon, actor: string, lines: array<int, string>}>
     */
    protected function priceHistory(ProductOption $record): array
    {
        $labels = ['price' => 'Preço', 'cost' => 'Custo', 'name' => 'Nome', 'is_active' => 'Ativa'];
        $format = fn (string $field, $value) => match ($field) {
            'price', 'cost' => $value === null ? '—' : 'R$ '.number_format((float) $value, 2, ',', '.'),
            'is_active' => $value ? 'Sim' : 'Não',
            default => (string) $value,
        };

        return Activity::query()
            ->where('subject_type', ProductOption::class)
            ->where('subject_id', $record->id)
            ->with('causer')
            ->latest('id')
            ->limit(50)
            ->get()
            ->map(function (Activity $activity) use ($labels, $format) {
                $old = $activity->properties['old'] ?? [];
                $new = $activity->properties['attributes'] ?? [];

                $lines = collect($labels)
                    ->filter(fn ($label, $field) => array_key_exists($field, $new))
                    ->map(fn ($label, $field) => $activity->event === 'created'
                        ? $label.': '.$format($field, $new[$field])
                        : $label.': '.$format($field, $old[$field] ?? null).' → '.$format($field, $new[$field]))
                    ->values()
                    ->all();

                return [
                    'at' => $activity->created_at,
                    'actor' => $activity->causer?->name ?? 'Sistema',
                    'title' => match ($activity->event) {
                        'created' => 'Variação criada',
                        'deleted' => 'Arquivada',
                        'restored' => 'Restaurada',
                        default => 'Alterada',
                    },
                    'lines' => $activity->event === 'deleted' ? [] : $lines,
                ];
            })
            ->all();
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) $ownerRecord->productOption()->count();
    }
}
