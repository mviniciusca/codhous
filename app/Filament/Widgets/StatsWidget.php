<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Customer;
use App\Models\Mail;
use App\Models\Newsletter;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class StatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = '30s';

    protected static bool $isLazy = false;

    // Define o número de colunas como um array com configurações específicas para cada tamanho de tela
    protected int|string|array $columns = [
        'default' => 1, // Em telas muito pequenas, exibe 1 card por linha
        'sm'      => 1,      // Em telas pequenas, exibe 1 card por linha
        'md'      => 2,      // Em telas médias, exibe 2 cards por linha (4 por linha no total)
        'lg'      => 2,      // Em telas grandes, exibe 2 cards por linha
        'xl'      => 2,      // Em telas extra grandes, exibe 2 cards por linha
        '2xl'     => 2,     // Em telas 2xl, exibe 2 cards por linha
    ];

    protected function getStats(): array
    {
        return [

            // Status de manutenção
            $this->makeMaintenanceModeStat(),
            // Estatísticas de orçamentos
            $this->makeBudgetStat(),
            // Lista de E-mails
            $this->makeNewsletterStat(),

            // Estatísticas de clientes
            $this->makeCustomerStat(),

            // Orçamentos pendentes
            $this->makePendingBudgetsStat(),

            // Orçamentos em andamento
            $this->makeOngoingBudgetsStat(),

            // Total em orçamentos
            $this->makeTotalValueStat(),

        ];
    }

    /**
     * Cria o widget de estatística de newsletter
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
     * Cria o widget de estatística de orçamentos
     * @return Stat
     */
    protected function makeBudgetStat(): Stat
    {
        $totalBudgets = Budget::withoutTrashed()->count();
        $monthlyTrend = $this->getMonthlyTrend(Budget::class);

        return Stat::make(__('Budgets'), $totalBudgets)
            ->icon('heroicon-o-currency-dollar')
            ->chart($monthlyTrend['data']->toArray())
            ->description($this->getComparisonText($monthlyTrend['difference'], __('budgets')))
            ->descriptionIcon($this->getTrendIcon($monthlyTrend['difference']))
            ->color($this->getTrendColor($monthlyTrend['difference']));
    }

    /**
     * Cria o widget de estatística de clientes
     * @return Stat
     */
    protected function makeCustomerStat(): Stat
    {
        $totalCustomers = Customer::withoutTrashed()->count();
        $monthlyTrend = $this->getMonthlyTrend(Customer::class);

        return Stat::make(__('Customers'), $totalCustomers)
            ->icon('heroicon-o-user')
            ->chart($monthlyTrend['data']->toArray())
            ->description($this->getComparisonText($monthlyTrend['difference'], __('customers')))
            ->descriptionIcon($this->getTrendIcon($monthlyTrend['difference']))
            ->color($this->getTrendColor($monthlyTrend['difference']));
    }

    /**
     * Cria o widget de estatística de orçamentos pendentes
     * @return Stat
     */
    protected function makePendingBudgetsStat(): Stat
    {
        $pendingBudgets = Budget::withoutTrashed()
            ->where('status', '=', 'pending')
            ->count();
        $allBudgets = Budget::withoutTrashed()->count();
        $percentage = $allBudgets > 0 ? round(($pendingBudgets / $allBudgets) * 100) : 0;

        return Stat::make(__('Pending Budgets'), $pendingBudgets)
            ->icon('heroicon-o-clock')
            ->description($percentage.'% '.__('of all budgets'))
            ->descriptionIcon('heroicon-m-information-circle')
            ->color('warning');
    }

    /**
     * Cria o widget de estatística de orçamentos em andamento
     * @return Stat
     */
    protected function makeOngoingBudgetsStat(): Stat
    {
        $ongoingBudgets = Budget::withoutTrashed()
            ->where('status', '=', 'on going')
            ->count();
        $allBudgets = Budget::withoutTrashed()->count();
        $percentage = $allBudgets > 0 ? round(($ongoingBudgets / $allBudgets) * 100) : 0;

        return Stat::make(__('On Going Budgets'), $ongoingBudgets)
            ->icon('heroicon-o-arrow-trending-up')
            ->description($percentage.'% '.__('of all budgets'))
            ->descriptionIcon('heroicon-m-information-circle')
            ->color('info');
    }

    /**
     * Cria o widget de estatística de valor total de orçamentos
     * @return Stat
     */
    protected function makeTotalValueStat(): Stat
    {
        // Obter os orçamentos que têm o campo content.total definido
        $budgets = Budget::withoutTrashed()->get();
        $totalValue = 0;

        foreach ($budgets as $budget) {
            if (isset($budget->content['total'])) {
                $totalValue += floatval($budget->content['total']);
            }
        }

        $currencySuffix = env('CURRENCY_SUFFIX', '$');

        return Stat::make(__('Total Budget Value'), $currencySuffix.' '.number_format($totalValue, 2, '.', ','))
            ->icon('heroicon-o-banknotes')
            ->description(__('Sum of all budgets'))
            ->descriptionIcon('heroicon-m-calculator')
            ->color('success');
    }

    /**
     * Cria o widget de estatística para mensagens/e-mails
     * @return Stat
     */
    protected function makeMailStat(): Stat
    {
        // Contar e-mails não lidos (recebidos e não marcados como spam)
        $unreadMails = Mail::withoutTrashed()
            ->where('is_read', false)
            ->where('is_sent', false)
            ->where('is_spam', false)
            ->count();

        // Total de e-mails no sistema
        $totalMails = Mail::withoutTrashed()->count();

        // Calcular a porcentagem de não lidos
        $percentage = $totalMails > 0 ? round(($unreadMails / $totalMails) * 100) : 0;

        return Stat::make(__('Unread Messages'), $unreadMails)
            ->icon('heroicon-o-envelope')
            ->description($percentage.'% '.__('of all messages'))
            ->descriptionIcon('heroicon-m-inbox-stack')
            ->color($unreadMails > 0 ? 'warning' : 'success');
    }

    /**
     * Cria o widget de estatística do modo de manutenção
     * @return Stat
     */
    protected function makeMaintenanceModeStat(): Stat
    {
        // Obter o status atual do modo de manutenção
        $setting = Setting::select(['maintenance_mode', 'discovery_mode'])->first();
        $maintenanceMode = $setting ? $setting->maintenance_mode : false;
        $discoveryMode = $setting ? $setting->discovery_mode : false;

        $statusText = $maintenanceMode
            ? __('Maintenance Mode Active')
            : __('Site Online');

        $description = $maintenanceMode && $discoveryMode
            ? __('Discovery Mode Enabled')
            : ($maintenanceMode ? __('Site Inaccessible to Visitors') : __('Site Accessible to All'));

        return Stat::make(__('Site Status'), $statusText)
            ->icon($maintenanceMode ? 'heroicon-o-wrench' : 'heroicon-o-globe-alt')
            ->description($description)
            ->descriptionIcon($maintenanceMode ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
            ->color($maintenanceMode ? 'warning' : 'success');
    }

    /**
     * Obtém a tendência mensal para um modelo
     * @param string $model
     * @return array
     */
    protected function getMonthlyTrend(string $model): array
    {
        // Dados da tendência dos últimos 6 meses
        $data = Trend::model($model)
            ->between(
                start: now()->subMonths(6)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();

        // Calcular a diferença percentual em relação ao mês anterior
        $values = $data->map(fn (TrendValue $value) => $value->aggregate);
        $latestMonth = $values->last();
        $previousMonth = $values->count() > 1 ? $values[$values->count() - 2] : 0;

        // Evitar divisão por zero
        if ($previousMonth == 0) {
            $difference = $latestMonth > 0 ? 100 : 0;
        } else {
            $difference = round((($latestMonth - $previousMonth) / $previousMonth) * 100);
        }

        return [
            'data'       => $data->map(fn (TrendValue $value) => $value->aggregate),
            'difference' => $difference,
        ];
    }

    /**
     * Retorna o texto comparativo com base na diferença percentual
     * @param int $difference
     * @param string $itemLabel
     * @return string
     */
    protected function getComparisonText(int $difference, string $itemLabel): string
    {
        if ($difference > 0) {
            return "+{$difference}% ".__('more')." {$itemLabel}";
        } elseif ($difference < 0) {
            return "{$difference}% ".__('less')." {$itemLabel}";
        }

        return __('No change in')." {$itemLabel}";
    }

    /**
     * Retorna o ícone com base na tendência
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
     * Retorna a cor com base na tendência
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
