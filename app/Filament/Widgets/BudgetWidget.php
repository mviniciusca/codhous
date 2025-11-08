<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Customer;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class BudgetWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $pendingCount = Budget::where('status', '=', 'pending')
            ->where('is_active', '=', true)
            ->count();

        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(
                fn (Budget $record): string => route('filament.admin.resources.budgets.edit', ['record' => $record]),
            )
            ->description(__('Recent budget requests waiting for approval'))
            ->headerActions([
                Action::make('new_budget')
                    ->label(__('New Budget'))
                    ->color('primary')
                    ->icon('heroicon-o-plus-circle')
                    ->url(route('filament.admin.resources.budgets.create')),
                Action::make('view_all')
                    ->label(__('View All Budgets'))
                    ->icon('heroicon-o-arrow-up-right')
                    ->url(route('filament.admin.resources.budgets.index')),
            ])
            ->heading($pendingCount === 0
                ? __('Pending Budgets')
                : __('Pending Budgets (:count)', ['count' => $pendingCount]))
            ->query(
                Budget::query()
                    ->select(['id', 'code', 'content', 'created_at', 'updated_at', 'status', 'is_active'])
                    ->where('status', '=', 'pending')
                    ->where('is_active', '=', true)
                    ->take(5)
            )
            ->emptyStateHeading(__('No pending budgets'))
            ->emptyStateDescription(__('All budgets have been processed or are in progress'))
            ->emptyStateIcon('heroicon-o-document-check')
            ->paginated(false)
            ->striped()
            ->columns([
                BadgeColumn::make('code')
                    ->label(__('Budget Code'))
                    ->searchable()
                    ->color('primary')
                    ->icon('heroicon-o-document-text'),

                TextColumn::make('content.customer_name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Budget $record): ?string => isset($record->content['customer_name']) && strlen($record->content['customer_name']) > 30
                            ? $record->content['customer_name']
                            : null
                    ),

                TextColumn::make('content.total')
                    ->label(__('Total Value'))
                    ->money(fn () => env('CURRENCY_CODE', 'USD'))
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 2, '.', ',') : '-'
                    )
                    ->color('success'),

                IconColumn::make('priority')
                    ->label(__('Priority'))
                    ->boolean()
                    ->getStateUsing(function (Budget $record): bool {
                        $createdAt = Carbon::parse($record->created_at);
                        $waitingDays = $createdAt->diffInDays(Carbon::now());

                        return $waitingDays >= 3; // Mark as high priority if waiting 3+ days
                    })
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-clock')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->label(__('Waiting Since'))
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $date = Carbon::parse($state);

                        if ($date->isToday()) {
                            return __('Today').' '.$date->format('H:i');
                        } elseif ($date->isYesterday()) {
                            return __('Yesterday').' '.$date->format('H:i');
                        } else {
                            // Calculate exact days and round to whole number
                            $exactDays = Carbon::now()->floatDiffInDays($date);
                            $roundedDays = (int) round($exactDays);

                            // Format the day text
                            $dayText = '';
                            if ($roundedDays > 0) {
                                // Use singular form for 1 day
                                if ($roundedDays == 1) {
                                    $dayText = ' ('.__('1 day').')';
                                } else {
                                    $dayText = ' ('.__(':days days', ['days' => $roundedDays]).')';
                                }
                            }

                            return $date->format('d/m/Y').$dayText;
                        }
                    })
                    ->color(function ($state) {
                        $date = Carbon::parse($state);
                        $days = Carbon::now()->floatDiffInDays($date);
                        $roundedDays = (int) round($days);

                        if ($roundedDays >= 7) {
                            return 'danger';
                        }
                        if ($roundedDays >= 3) {
                            return 'warning';
                        }

                        return 'gray';
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label(__('Approve'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Budget $record): void {
                            $record->update(['status' => 'on going']);
                        })
                        ->successNotificationTitle(__('Budget approved successfully')),

                    Action::make('view_edit')
                        ->label(__('View/Edit'))
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn (Budget $record): string => route('filament.admin.resources.budgets.edit', ['record' => $record])
                        ),
                ]),
            ]);
    }
}
