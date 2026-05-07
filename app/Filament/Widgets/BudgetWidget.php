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
    protected static ?int $sort = 3;

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
            ->description('Solicitações de orçamento recentes aguardando aprovação')
            ->headerActions([
                Action::make('new_budget')
                    ->label('Novo Orçamento')
                    ->color('primary')
                    ->icon('heroicon-o-plus-circle')
                    ->url(route('filament.admin.resources.budgets.create')),
                Action::make('view_all')
                    ->label('Ver Todos')
                    ->icon('heroicon-o-arrow-up-right')
                    ->url(route('filament.admin.resources.budgets.index')),
            ])
            ->heading($pendingCount === 0
                ? 'Orçamentos Pendentes'
                : "Orçamentos Pendentes ({$pendingCount})")
            ->query(
                Budget::query()
                    ->select(['id', 'code', 'content', 'created_at', 'updated_at', 'status', 'is_active'])
                    ->where('status', '=', 'pending')
                    ->where('is_active', '=', true)
                    ->take(5)
            )
            ->emptyStateHeading('Nenhum orçamento pendente')
            ->emptyStateDescription('Todos os orçamentos foram processados ou estão em andamento')
            ->emptyStateIcon('heroicon-o-document-check')
            ->paginated(false)
            ->striped()
            ->columns([
                BadgeColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->color('primary')
                    ->icon('heroicon-o-document-text'),

                TextColumn::make('content.customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Budget $record): ?string => isset($record->content['customer_name']) && strlen($record->content['customer_name']) > 30
                            ? $record->content['customer_name']
                            : null
                    ),

                TextColumn::make('content.total')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format((float) $state, 2, ',', '.') : '-'
                    )
                    ->color('success'),

                IconColumn::make('priority')
                    ->label('Prioridade')
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
                    ->label('Aguardando desde')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $date = Carbon::parse($state);

                        if ($date->isToday()) {
                            return 'Hoje ' . $date->format('H:i');
                        } elseif ($date->isYesterday()) {
                            return 'Ontem ' . $date->format('H:i');
                        } else {
                            $exactDays = Carbon::now()->floatDiffInDays($date);
                            $roundedDays = (int) round($exactDays);

                            $dayText = '';
                            if ($roundedDays > 0) {
                                if ($roundedDays == 1) {
                                    $dayText = ' (1 dia)';
                                } else {
                                    $dayText = " ({$roundedDays} dias)";
                                }
                            }

                            return $date->format('d/m/Y') . $dayText;
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
                        ->label('Aprovar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Budget $record): void {
                            $record->update(['status' => 'on going']);
                        })
                        ->successNotificationTitle('Orçamento aprovado com sucesso'),

                    Action::make('view_edit')
                        ->label('Ver/Editar')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn (Budget $record): string => route('filament.admin.resources.budgets.edit', ['record' => $record])
                        ),
                ]),
            ]);
    }
}
