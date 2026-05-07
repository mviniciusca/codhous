<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Customer;
use App\Models\Mail;
use App\Models\Newsletter;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;


class StatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '30s';

    protected static bool $isLazy = false;

    protected int|string|array $columns = [
        'default' => 1,
        'sm'      => 2,
        'lg'      => 4,
    ];

    protected function getStats(): array
    {
        return [
            $this->makeBudgetStat(),
            $this->makePendingBudgetsStat(),
            $this->makeDoneBudgetsStat(),
            $this->makeTotalValueStat(),
        ];
    }

    /**
     * Creates the newsletter statistics widget
     * @return Stat
     */
    protected function makeNewsletterStat(): Stat
    {
        $totalNewsletters = Newsletter::withoutTrashed()->count();
        $monthlyTrend = $this->getMonthlyTrend(Newsletter::class);

        return Stat::make(__('Mailing List'), $totalNewsletters)
            ->icon('heroicon-o-envelope')
            ->chart($monthlyTrend['data']->toArray())
            ->description($this->getComparisonText($monthlyTrend['difference'], __('subscribers')))
            ->descriptionIcon($this->getTrendIcon($monthlyTrend['difference']))
            ->color($this->getTrendColor($monthlyTrend['difference']));
    }

    /**
     * Creates the budget statistics widget
     * @return Stat
     */
    protected function makeBudgetStat(): Stat
    {
        $totalBudgets = Budget::withoutTrashed()->count();
        $monthlyTrend = $this->getMonthlyTrend(Budget::class);

        return Stat::make('Total de Orçamentos', $totalBudgets)
            ->icon('heroicon-o-currency-dollar')
            ->chart($monthlyTrend['data']->toArray())
            ->description($this->getComparisonText($monthlyTrend['difference'], 'orçamentos'))
            ->descriptionIcon($this->getTrendIcon($monthlyTrend['difference']))
            ->color($this->getTrendColor($monthlyTrend['difference']));
    }

    protected function makePendingBudgetsStat(): Stat
    {
        $pendingBudgets = Budget::withoutTrashed()
            ->where('status', '=', 'pending')
            ->where('is_active', '=', true)
            ->count();
        $allBudgets = Budget::withoutTrashed()->count();
        $percentage = $allBudgets > 0 ? round(($pendingBudgets / $allBudgets) * 100) : 0;

        return Stat::make('Orçamentos Pendentes', $pendingBudgets)
            ->icon('heroicon-o-clock')
            ->description($percentage . '% ' . 'do total geral')
            ->descriptionIcon('heroicon-m-information-circle')
            ->color('warning');
    }

    protected function makeDoneBudgetsStat(): Stat
    {
        $doneBudgets = Budget::withoutTrashed()
            ->where('status', '=', 'done')
            ->where('is_active', '=', true)
            ->count();
        $allBudgets = Budget::withoutTrashed()->count();
        $percentage = $allBudgets > 0 ? round(($doneBudgets / $allBudgets) * 100) : 0;

        return Stat::make('Orçamentos Concluídos', $doneBudgets)
            ->icon('heroicon-o-check-circle')
            ->description($percentage . '% ' . 'em taxa de sucesso')
            ->descriptionIcon('heroicon-m-check-badge')
            ->color('success');
    }

    protected function makeTotalValueStat(): Stat
    {
        $budgets = Budget::where('status', '=', 'done')
            ->where('is_active', '=', true)
            ->withoutTrashed()
            ->get();
        $totalValue = 0;

        foreach ($budgets as $budget) {
            if (isset($budget->content['total'])) {
                $totalValue += floatval($budget->content['total']);
            }
        }

        return Stat::make('Receita Gerada', 'R$ ' . number_format($totalValue, 2, '.', ','))
            ->icon('heroicon-o-banknotes')
            ->description('Soma de todos os orçamentos finalizados')
            ->descriptionIcon('heroicon-m-calculator')
            ->color('success');
    }

    protected function getMonthlyTrend(string $model): array
    {
        $data = Trend::model($model)
            ->between(
                start: now()->subMonths(6)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();

        $values = $data->map(fn(TrendValue $value) => $value->aggregate);
        $latestMonth = $values->last();
        $previousMonth = $values->count() > 1 ? $values[$values->count() - 2] : 0;

        if ($previousMonth == 0) {
            $difference = $latestMonth > 0 ? 100 : 0;
        } else {
            $difference = round((($latestMonth - $previousMonth) / $previousMonth) * 100);
        }

        return [
            'data'       => $data->map(fn(TrendValue $value) => $value->aggregate),
            'difference' => $difference,
        ];
    }

    protected function getComparisonText(int $difference, string $itemLabel): string
    {
        if ($difference > 0) {
            return "+{$difference}% " . 'mais' . " {$itemLabel}";
        } elseif ($difference < 0) {
            return "{$difference}% " . 'menos' . " {$itemLabel}";
        }

        return 'Sem alteração em' . " {$itemLabel}";
    }

    /**
     * Returns the icon based on the trend
     * @param int $difference
     * @return string
     */
    protected function getTrendIcon(int $difference): string
    {
        if ($difference > 0) {
            return 'heroicon-m-arrow-trending-up';
        } elseif ($difference < 0) {
            return 'heroicon-m-arrow-trending-down';
        }

        return 'heroicon-m-arrow-right';
    }

    /**
     * Returns the color based on the trend
     * @param int $difference
     * @return string
     */
    protected function getTrendColor(int $difference): string
    {
        if ($difference > 0) {
            return 'success';
        } elseif ($difference < 0) {
            return 'danger';
        }

        return 'gray';
    }
}
