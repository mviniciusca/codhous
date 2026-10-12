<?php

namespace App\Filament\Resources\BudgetResource\Widgets;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Indicadores comerciais exibidos no topo da lista de orçamentos.
 */
class BudgetStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    /** Janela usada no cálculo da taxa de conversão */
    public const CONVERSION_DAYS = 90;

    protected function getStats(): array
    {
        return [
            $this->newBudgetsStat(),
            $this->negotiationStat(),
            $this->closedThisMonthStat(),
            $this->conversionStat(),
        ];
    }

    protected function newBudgetsStat(): Stat
    {
        $pending = $this->active()->where('status', 'pending');
        $count = (clone $pending)->count();
        $oldest = (clone $pending)->oldest()->value('created_at');

        $description = match (true) {
            $count === 0 => 'Nenhum pedido aguardando',
            default => 'Mais antigo aguarda há '.$this->humanDays(Carbon::parse($oldest)),
        };

        return Stat::make('Novos pedidos', $count)
            ->description($description)
            ->descriptionIcon($count > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
            ->color($count > 0 ? 'warning' : 'success')
            ->chart($this->dailyNewBudgets())
            ->url(BudgetResource::getUrl('index', ['activeTab' => 'novos']));
    }

    protected function negotiationStat(): Stat
    {
        $budgets = $this->active()->where('status', 'on going')->get();
        $stale = $budgets->filter->isStale()->count();

        return Stat::make('Em negociação', $this->money($this->sumTotal($budgets)))
            ->description($budgets->count().' '.($budgets->count() === 1 ? 'orçamento' : 'orçamentos')
                .($stale > 0 ? " · {$stale} sem retorno há ".Budget::STALE_AFTER_DAYS.'+ dias' : ''))
            ->descriptionIcon($stale > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-chat-bubble-left-right')
            ->color($stale > 0 ? 'danger' : 'primary')
            ->url(BudgetResource::getUrl('index', ['activeTab' => 'negociacao']));
    }

    protected function closedThisMonthStat(): Stat
    {
        $tz = config('app.display_timezone');
        $monthStart = now($tz)->startOfMonth()->utc();
        $budgets = $this->active()
            ->where('status', 'done')
            ->where('status_changed_at', '>=', $monthStart)
            ->get(['id', 'content']);

        $count = $budgets->count();

        return Stat::make('Fechados em '.now($tz)->translatedFormat('F'), $this->money($this->sumTotal($budgets)))
            ->description($count === 0 ? 'Nenhum pedido fechado no mês' : $count.' '.($count === 1 ? 'pedido' : 'pedidos').' · ticket médio '.$this->money($this->sumTotal($budgets) / $count))
            ->descriptionIcon('heroicon-m-banknotes')
            ->color('success')
            ->url(BudgetResource::getUrl('index', ['activeTab' => 'fechados']));
    }

    protected function conversionStat(): Stat
    {
        $decided = $this->active()
            ->whereIn('status', ['done', 'ignored'])
            ->where('status_changed_at', '>=', now()->subDays(self::CONVERSION_DAYS));

        $total = (clone $decided)->count();
        $done = (clone $decided)->where('status', 'done')->count();
        $rate = $total > 0 ? round($done / $total * 100) : null;

        return Stat::make('Conversão ('.self::CONVERSION_DAYS.' dias)', $rate === null ? '—' : $rate.'%')
            ->description($total === 0
                ? 'Sem orçamentos fechados ou arquivados'
                : $done.' '.($done === 1 ? 'fechado' : 'fechados').' de '.$total.' '.($total === 1 ? 'decidido' : 'decididos'))
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color(match (true) {
                $rate === null => 'gray',
                $rate >= 50 => 'success',
                $rate >= 25 => 'warning',
                default => 'danger',
            });
    }

    protected function active(): Builder
    {
        return Budget::query()->where('is_active', true);
    }

    /**
     * Pedidos recebidos por dia nos últimos 14 dias (mini gráfico).
     */
    protected function dailyNewBudgets(): array
    {
        $tz = config('app.display_timezone');
        $start = now($tz)->subDays(13)->startOfDay();

        $perDay = $this->active()
            ->where('created_at', '>=', $start->copy()->utc())
            ->pluck('created_at')
            ->countBy(fn ($date) => $date->timezone($tz)->toDateString());

        return collect(range(13, 0))
            ->map(fn (int $daysAgo) => $perDay[now($tz)->subDays($daysAgo)->toDateString()] ?? 0)
            ->all();
    }

    protected function sumTotal(Collection $budgets): float
    {
        return $budgets->sum(fn (Budget $budget) => BudgetResource::parseMoney(data_get($budget->content, 'total', 0)));
    }

    protected function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }

    protected function humanDays(Carbon $date): string
    {
        $days = (int) $date->diffInDays(now());

        return match (true) {
            $days === 0 => 'menos de 1 dia',
            $days === 1 => '1 dia',
            default => "{$days} dias",
        };
    }
}
