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

    // Define the number of columns as an array with specific configurations for each screen size
    protected int|string|array $columns = [
        'default' => 1, // On very small screens, display 1 card per row
        'sm'      => 1, // On small screens, display 1 card per row
        'md'      => 2, // On medium screens, display 2 cards per row (4 per row in total)
        'lg'      => 2, // On large screens, display 2 cards per row
        'xl'      => 2, // On extra large screens, display 2 cards per row
        '2xl'     => 2, // On 2xl screens, display 2 cards per row
    ];

    protected function getStats(): array
    {
        return [

            // Maintenance status
            $this->makeMaintenanceModeStat(),
            // Budget statistics
            $this->makeBudgetStat(),
            // Email list
            $this->makeNewsletterStat(),

            // Customer statistics
            $this->makeCustomerStat(),

            // Pending budgets
            $this->makePendingBudgetsStat(),

            // Ongoing budgets
            $this->makeOngoingBudgetsStat(),

            // Total budgets
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

        return Stat::make(__('Budgets'), $totalBudgets)
            ->icon('heroicon-o-currency-dollar')
            ->chart($monthlyTrend['data']->toArray())
            ->description($this->getComparisonText($monthlyTrend['difference'], __('budgets')))
            ->descriptionIcon($this->getTrendIcon($monthlyTrend['difference']))
            ->color($this->getTrendColor($monthlyTrend['difference']));
    }

    /**
     * Creates the customer statistics widget
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
     * Creates the pending budgets statistics widget
     * @return Stat
     */
    protected function makePendingBudgetsStat(): Stat
    {
        $pendingBudgets = Budget::withoutTrashed()
            ->where('status', '=', 'pending')
            ->where('is_active', '=', true)
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
     * Creates the ongoing budgets statistics widget
     * @return Stat
     */
    protected function makeOngoingBudgetsStat(): Stat
    {
        $ongoingBudgets = Budget::withoutTrashed()
            ->where('status', '=', 'on going')
            ->where('is_active', '=', true)
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
     * Creates the total budget value statistics widget
     * @return Stat
     */
    protected function makeTotalValueStat(): Stat
    {
        // Get budgets that have the content.total field defined
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

        $currencySuffix = env('CURRENCY_SUFFIX', '$');

        return Stat::make(__('Total Budget Value'), $currencySuffix.' '.number_format($totalValue, 2, '.', ','))
            ->icon('heroicon-o-banknotes')
            ->description(__('Sum of all budgets completed'))
            ->descriptionIcon('heroicon-m-calculator')
            ->color('success');
    }

    /**
     * Creates the messages/emails statistics widget
     * @return Stat
     */
    protected function makeMailStat(): Stat
    {
        // Count unread emails (received and not marked as spam)
        $unreadMails = Mail::withoutTrashed()
            ->where('is_read', false)
            ->where('is_sent', false)
            ->where('is_spam', false)
            ->count();

        // Total emails in the system
        $totalMails = Mail::withoutTrashed()->count();

        // Calculate the percentage of unread
        $percentage = $totalMails > 0 ? round(($unreadMails / $totalMails) * 100) : 0;

        return Stat::make(__('Unread Messages'), $unreadMails)
            ->icon('heroicon-o-envelope')
            ->description($percentage.'% '.__('of all messages'))
            ->descriptionIcon('heroicon-m-inbox-stack')
            ->color($unreadMails > 0 ? 'warning' : 'success');
    }

    /**
     * Creates the maintenance mode statistics widget
     * @return Stat
     */
    protected function makeMaintenanceModeStat(): Stat
    {
        // Get the current maintenance mode status
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
     * Gets the monthly trend for a model
     * @param string $model
     * @return array
     */
    protected function getMonthlyTrend(string $model): array
    {
        // Trend data for the last 6 months
        $data = Trend::model($model)
            ->between(
                start: now()->subMonths(6)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();

        // Calculate the percentage difference relative to the previous month
        $values = $data->map(fn (TrendValue $value) => $value->aggregate);
        $latestMonth = $values->last();
        $previousMonth = $values->count() > 1 ? $values[$values->count() - 2] : 0;

        // Avoid division by zero
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
     * Returns the comparison text based on the percentage difference
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
