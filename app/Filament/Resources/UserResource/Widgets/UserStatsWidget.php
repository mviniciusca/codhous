<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Budget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class UserStatsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        $userId = $this->record->id;

        // Total de orçamentos criados
        $totalBudgets = Budget::withoutGlobalScopes()
            ->where('user_id', $userId)
            ->count();

        // Orçamentos finalizados (done)
        $doneBudgets = Budget::withoutGlobalScopes()
            ->where('user_id', $userId)
            ->where('status', 'done')
            ->count();

        // Valor potencial total (apenas status 'done')
        $totalValue = Budget::withoutGlobalScopes()
            ->where('user_id', $userId)
            ->where('status', 'done')
            ->get()
            ->sum(function ($budget) {
                return (float) ($budget->content['total'] ?? 0);
            });

        // Orçamentos pendentes
        $pendingBudgets = Budget::withoutGlobalScopes()
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make('Total Budgets', $totalBudgets)
                ->description('All budgets created by this user')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('primary'),

            Stat::make('Completed Budgets', $doneBudgets)
                ->description('Budgets with status "Done"')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Potential Revenue', 'R$ '.number_format($totalValue, 2, ',', '.'))
                ->description('Total value of completed budgets')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success')
                ->chart([7, 12, 5, 18, 15, 23, $doneBudgets]),

            Stat::make('Pending Budgets', $pendingBudgets)
                ->description('Budgets awaiting approval')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
