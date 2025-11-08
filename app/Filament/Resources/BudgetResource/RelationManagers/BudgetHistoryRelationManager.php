<?php

namespace App\Filament\Resources\BudgetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Spatie\Activitylog\Models\Activity;

class BudgetHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Histórico de Alterações';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Version History & Changes'))
            ->description(__('Track all modifications made to this budget with detailed before/after comparison'))
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->columns([
                TextColumn::make('description')
                    ->label(__('Action'))
                    ->badge()
                    ->icon(fn (string $state): string => match (true) {
                        $state === 'created'                       => 'heroicon-o-plus-circle',
                        $state === 'updated'                       => 'heroicon-o-pencil-square',
                        $state === 'deleted'                       => 'heroicon-o-trash',
                        str_contains($state, 'price_updated')      => 'heroicon-o-currency-dollar',
                        str_contains($state, 'document_attached')  => 'heroicon-o-paper-clip',
                        str_contains($state, 'document_removed')   => 'heroicon-o-trash',
                        str_contains($state, 'Preços atualizados') => 'heroicon-o-currency-dollar',
                        str_contains($state, 'Documento')          => 'heroicon-o-document',
                        default                                    => 'heroicon-o-document',
                    })
                    ->color(fn (string $state): string => match (true) {
                        $state === 'created'                       => 'success',
                        $state === 'updated'                       => 'warning',
                        $state === 'deleted'                       => 'danger',
                        str_contains($state, 'price_updated')      => 'warning',
                        str_contains($state, 'document_attached')  => 'info',
                        str_contains($state, 'document_removed')   => 'danger',
                        str_contains($state, 'Preços atualizados') => 'warning',
                        str_contains($state, 'Documento')          => 'info',
                        default                                    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match (true) {
                        $state === 'created'                                                  => __('Created'),
                        $state === 'updated'                                                  => __('Updated'),
                        $state === 'deleted'                                                  => __('Deleted'),
                        str_contains($state, 'price_updated')                                 => __('Price Updated'),
                        str_contains($state, 'document_attached')                             => __('Document Attached'),
                        str_contains($state, 'document_removed')                              => __('Document Removed'),
                        str_contains($state, 'Documento') && str_contains($state, 'anexado')  => __('Document Attached'),
                        str_contains($state, 'Documento') && str_contains($state, 'removido') => __('Document Removed'),
                        str_contains($state, 'Preços atualizados')                            => __('Price Updated'),
                        default                                                               => ucfirst($state),
                    })
                    ->wrap(),

                TextColumn::make('causer.name')
                    ->label(__('Modified By'))
                    ->default(__('System'))
                    ->icon('heroicon-o-user')
                    ->searchable(),

                TextColumn::make('changes_summary')
                    ->label(__('What Changed'))
                    ->html()
                    ->wrap()
                    ->getStateUsing(function (Activity $record): string {
                        // Se a descrição já contém informações detalhadas, mostrá-las
                        if (str_contains($record->description, 'price_updated')) {
                            // Extrair a parte depois de ":"
                            $parts = explode(':', $record->description);
                            if (count($parts) > 1) {
                                return '<span class="text-warning-600 dark:text-warning-400">'.trim($parts[1]).'</span>';
                            }
                        }

                        if (str_contains($record->description, 'Preços atualizados') ||
                            str_contains($record->description, 'Documento')) {
                            // Extrair a parte relevante da descrição
                            $parts = explode(':', $record->description);
                            if (count($parts) > 1) {
                                return '<span class="text-primary-600 dark:text-primary-400">'.trim($parts[1]).'</span>';
                            }

                            return '<span class="text-primary-600 dark:text-primary-400">'.$record->description.'</span>';
                        }

                        $properties = $record->properties?->toArray() ?? [];
                        $attributes = $properties['attributes'] ?? [];
                        $old = $properties['old'] ?? [];

                        if (empty($old) && $record->description === 'created') {
                            return '<span class="text-success-600 dark:text-success-400">✓ Budget criado</span>';
                        }

                        if (empty($old)) {
                            return '<span class="text-gray-500">—</span>';
                        }

                        $changes = [];
                        foreach ($old as $key => $oldValue) {
                            $newValue = $attributes[$key] ?? null;
                            if ($oldValue != $newValue) {
                                $fieldName = $this->getFieldLabel($key);
                                $changes[] = "<strong>{$fieldName}</strong>";
                            }
                        }

                        if (empty($changes)) {
                            return '<span class="text-gray-500">Sem mudanças</span>';
                        }

                        $changeList = implode(', ', array_slice($changes, 0, 3));
                        $remaining = count($changes) - 3;

                        if ($remaining > 0) {
                            $changeList .= " <span class=\"text-gray-500\">e +{$remaining} mais</span>";
                        }

                        return $changeList;
                    }),

                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->since()
                    ->description(fn (Activity $record): string => $record->created_at->format('d/m/Y H:i:s'))
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                SelectFilter::make('causer_id')
                    ->label(__('User'))
                    ->options(function () {
                        return \App\Models\User::query()
                            ->whereIn('id', function ($query) {
                                $query->select('causer_id')
                                    ->from('activity_log')
                                    ->where('subject_type', \App\Models\Budget::class)
                                    ->whereNotNull('causer_id')
                                    ->distinct();
                            })
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable(),

                SelectFilter::make('description')
                    ->label(__('Action Type'))
                    ->options([
                        'created' => __('Created'),
                        'updated' => __('Updated'),
                        'deleted' => __('Deleted'),
                    ]),
            ])
            ->actions([
                Action::make('view_changes')
                    ->label(__('View Details'))
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading(fn (Activity $record): string => __('Changes Made').' - '.$record->created_at->format('d/m/Y H:i:s'))
                    ->modalWidth('5xl')
                    ->infolist(fn (Activity $record): array => $this->getChangeDetailsInfolist($record))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('Close')),
            ])
            ->bulkActions([
                // Sem bulk actions para histórico
            ]);
    }

    protected function getChangeDetailsInfolist(Activity $record): array
    {
        $properties = $record->properties?->toArray() ?? [];
        $attributes = $properties['attributes'] ?? [];
        $old = $properties['old'] ?? [];

        $infolist = [
            Section::make(__('Activity Information'))
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('action')
                                ->label(__('Action'))
                                ->state(match ($record->description) {
                                    'created' => '✓ Created',
                                    'updated' => '✎ Updated',
                                    'deleted' => '✗ Deleted',
                                    default   => ucfirst($record->description),
                                })
                                ->badge()
                                ->color(match ($record->description) {
                                    'created' => 'success',
                                    'updated' => 'warning',
                                    'deleted' => 'danger',
                                    default   => 'gray',
                                }),

                            TextEntry::make('user')
                                ->label(__('Modified By'))
                                ->state($record->causer?->name ?? __('System'))
                                ->icon('heroicon-o-user'),

                            TextEntry::make('date')
                                ->label(__('When'))
                                ->state($record->created_at->format('d/m/Y H:i:s'))
                                ->icon('heroicon-o-clock'),
                        ]),
                ]),
        ];

        // Se foi criação, mostrar dados iniciais
        if ($record->description === 'created' && ! empty($attributes)) {
            $infolist[] = Section::make(__('Initial Data'))
                ->description(__('Data when the budget was created'))
                ->schema($this->buildAttributesSchema($attributes))
                ->collapsible();
        }

        // Se foi atualização, mostrar comparação before/after
        if ($record->description === 'updated' || str_contains($record->description, 'price_updated')) {
            $infolist[] = Section::make(__('Changes Comparison'))
                ->description(__('Compare previous values with the current active snapshot'))
                ->schema($this->buildComparisonSchema($old, $attributes))
                ->collapsible();
        }

        return $infolist;
    }

    protected function buildAttributesSchema(array $attributes): array
    {
        $schema = [];

        foreach ($attributes as $key => $value) {
            if (in_array($key, ['updated_at', 'created_at', 'user_id'])) {
                continue;
            }

            $schema[] = TextEntry::make($key)
                ->label($this->getFieldLabel($key))
                ->state($this->formatValue($key, $value))
                ->copyable();
        }

        return [Grid::make(2)->schema($schema)];
    }

    protected function buildComparisonSchema(array $old, array $new): array
    {
        $schema = [];

        foreach ($old as $key => $oldValue) {
            $newValue = $new[$key] ?? null;

            // Pular timestamps e campos não modificados
            if (in_array($key, ['updated_at']) || $oldValue == $newValue) {
                continue;
            }

            // Tratamento especial para o campo content (JSON)
            if ($key === 'content' && is_array($oldValue) && is_array($newValue)) {
                $contentChanges = $this->getContentChanges($oldValue, $newValue);

                if (! empty($contentChanges)) {
                    foreach ($contentChanges as $contentKey => $values) {
                        // Calcular trend (aumento/diminuição)
                        $trend = $this->calculateTrend($contentKey, $values['old'], $values['new']);

                        $schema[] = Grid::make(2)
                            ->schema([
                                TextEntry::make('old_content_'.$contentKey)
                                    ->label('⬅️ '.$this->getContentFieldLabel($contentKey).' '.__('(Previous)'))
                                    ->state($this->formatContentValue($contentKey, $values['old']))
                                    ->color('gray')
                                    ->icon('heroicon-o-clock')
                                    ->copyable()
                                    ->badge(),

                                TextEntry::make('new_content_'.$contentKey)
                                    ->label('✅ '.$this->getContentFieldLabel($contentKey).' '.__('(Current)').' '.$trend)
                                    ->state($this->formatContentValue($contentKey, $values['new']))
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle')
                                    ->copyable()
                                    ->badge()
                                    ->columnSpan(1),
                            ]);
                    }
                }
                continue;
            }            $schema[] = Grid::make(2)
                ->schema([
                    TextEntry::make('old_'.$key)
                        ->label('⬅️ '.$this->getFieldLabel($key).' '.__('(Previous)'))
                        ->state($this->formatValue($key, $oldValue))
                        ->color('gray')
                        ->icon('heroicon-o-clock')
                        ->copyable()
                        ->badge(),

                    TextEntry::make('new_'.$key)
                        ->label('✅ '.$this->getFieldLabel($key).' '.__('(Current)'))
                        ->state($this->formatValue($key, $newValue))
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->copyable()
                        ->badge(),
                ]);
        }

        return $schema;
    }

    protected function getContentChanges(array $old, array $new): array
    {
        $changes = [];

        // Lista de campos importantes para mostrar
        $importantFields = ['price', 'tax', 'discount', 'total', 'quantity', 'shipping',
            'customer_name', 'customer_email', 'customer_phone',
            'status', 'location', 'product'];

        foreach ($importantFields as $field) {
            $oldVal = $old[$field] ?? null;
            $newVal = $new[$field] ?? null;

            if ($oldVal != $newVal && ($oldVal !== null || $newVal !== null)) {
                $changes[$field] = [
                    'old' => $oldVal,
                    'new' => $newVal,
                ];
            }
        }

        return $changes;
    }

    protected function getContentFieldLabel(string $key): string
    {
        return match ($key) {
            'price'          => __('Price'),
            'tax'            => __('Tax'),
            'discount'       => __('Discount'),
            'total'          => __('Total'),
            'quantity'       => __('Quantity'),
            'shipping'       => __('Shipping'),
            'customer_name'  => __('Customer Name'),
            'customer_email' => __('Customer Email'),
            'customer_phone' => __('Customer Phone'),
            'status'         => __('Status'),
            'location'       => __('Location'),
            'product'        => __('Product'),
            default          => ucfirst(str_replace('_', ' ', $key)),
        };
    }

    protected function formatContentValue(string $key, mixed $value): string
    {
        if (is_null($value)) {
            return '—';
        }

        // Formatar valores monetários
        if (in_array($key, ['price', 'tax', 'discount', 'total', 'shipping'])) {
            return 'R$ '.number_format((float) $value, 2, ',', '.');
        }

        // Formatar quantidade
        if ($key === 'quantity') {
            return $value.' m³';
        }

        return (string) $value;
    }

    protected function calculateTrend(string $key, mixed $oldValue, mixed $newValue): string
    {
        // Campos numéricos que podem ter trend
        $numericFields = ['price', 'tax', 'discount', 'total', 'quantity', 'shipping', 'product'];

        if (! in_array($key, $numericFields)) {
            return '';
        }

        $old = (float) ($oldValue ?? 0);
        $new = (float) ($newValue ?? 0);

        if ($old == $new) {
            return '';
        }

        // Para desconto, trend invertido (desconto maior é bom)
        if ($key === 'discount') {
            if ($new > $old) {
                $diff = $new - $old;
                $percent = $old > 0 ? round(($diff / $old) * 100, 1) : 0;

                return '📈 +R$ '.number_format($diff, 2, ',', '.')." (+{$percent}%)";
            } else {
                $diff = $old - $new;
                $percent = $old > 0 ? round(($diff / $old) * 100, 1) : 0;

                return '📉 -R$ '.number_format($diff, 2, ',', '.')." (-{$percent}%)";
            }
        }

        // Para outros valores (aumento é ruim em custos, bom em quantidade)
        $isGoodIncrease = in_array($key, ['quantity']);

        if ($new > $old) {
            $diff = $new - $old;
            $percent = $old > 0 ? round(($diff / $old) * 100, 1) : 0;

            if (in_array($key, ['price', 'tax', 'total', 'shipping'])) {
                return '⬆️ +R$ '.number_format($diff, 2, ',', '.')." (+{$percent}%)";
            } else {
                return "⬆️ +{$diff} (+{$percent}%)";
            }
        } else {
            $diff = $old - $new;
            $percent = $old > 0 ? round(($diff / $old) * 100, 1) : 0;

            if (in_array($key, ['price', 'tax', 'total', 'shipping'])) {
                return '⬇️ -R$ '.number_format($diff, 2, ',', '.')." (-{$percent}%)";
            } else {
                return "⬇️ -{$diff} (-{$percent}%)";
            }
        }
    }

    protected function getFieldLabel(string $key): string
    {
        return match ($key) {
            'code'       => __('Code'),
            'status'     => __('Status'),
            'is_active'  => __('Active'),
            'content'    => __('Content'),
            'created_at' => __('Created At'),
            'updated_at' => __('Updated At'),
            'user_id'    => __('User'),
            default      => ucfirst(str_replace('_', ' ', $key)),
        };
    }

    protected function formatValue(string $key, mixed $value): string
    {
        if (is_null($value)) {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? __('Yes') : __('No');
        }

        if (is_array($value)) {
            return $this->formatArrayValue($value);
        }

        if ($key === 'user_id') {
            $user = \App\Models\User::find($value);

            return $user?->name ?? "User #{$value}";
        }

        if (in_array($key, ['created_at', 'updated_at']) && $value instanceof \Carbon\Carbon) {
            return $value->format('d/m/Y H:i:s');
        }

        return (string) $value;
    }

    protected function formatArrayValue(array $value, int $depth = 0): string
    {
        $items = [];
        $indent = str_repeat('  ', $depth);

        foreach ($value as $k => $v) {
            if (is_array($v)) {
                $items[] = "{$indent}<strong>{$k}:</strong><br>".$this->formatArrayValue($v, $depth + 1);
            } else {
                $formattedValue = is_bool($v) ? ($v ? 'Yes' : 'No') : $v;
                $items[] = "{$indent}<strong>{$k}:</strong> {$formattedValue}";
            }
        }

        return implode('<br>', $items);
    }
}
