<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class RecentBudgetActivitiesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Atividades Recentes dos Budgets';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->where('subject_type', Budget::class)
                    ->with(['causer', 'subject'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('description')
                    ->label(__('Action'))
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'created' => 'heroicon-o-plus-circle',
                        'updated' => 'heroicon-o-pencil-square',
                        'deleted' => 'heroicon-o-trash',
                        default   => 'heroicon-o-document',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'created' => __('Created'),
                        'updated' => __('Updated'),
                        'deleted' => __('Deleted'),
                        default   => ucfirst($state),
                    }),

                TextColumn::make('subject.code')
                    ->label(__('Budget'))
                    ->default('—')
                    ->url(
                        fn(Activity $record) => $record->subject
                            ? route('filament.admin.resources.budgets.edit', ['record' => $record->subject_id])
                            : null
                    )
                    ->color('primary')
                    ->icon('heroicon-o-document-text'),

                TextColumn::make('causer.name')
                    ->label(__('User'))
                    ->default(__('System'))
                    ->icon('heroicon-o-user')
                    ->searchable(),

                TextColumn::make('changes_count')
                    ->label(__('Changes'))
                    ->getStateUsing(function (Activity $record): int {
                        $old = $record->properties?->get('old', []);

                        return count($old);
                    })
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->since()
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->description(fn(Activity $record): string => $record->created_at->format('d/m/Y H:i:s')),
            ])
            ->paginated(false);
    }

    public static function canView(): bool
    {
        // Pode adicionar permissões aqui se necessário
        return true;
    }
}
