<?php

namespace App\Filament\Resources\ContactAgendaResource\Widgets;

use App\Models\ContactAgenda;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactAgendaStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $total = ContactAgenda::count();
        $clients = ContactAgenda::where('category', ContactAgenda::CATEGORY_CLIENT)->count();
        $leads = ContactAgenda::where('category', ContactAgenda::CATEGORY_LEAD)->count();
        $suppliers = ContactAgenda::where('category', ContactAgenda::CATEGORY_SUPPLIER)->count();

        $clientPct = $total > 0 ? round(($clients / $total) * 100) : 0;
        $leadPct = $total > 0 ? round(($leads / $total) * 100) : 0;
        $supplierPct = $total > 0 ? round(($suppliers / $total) * 100) : 0;

        return [
            Stat::make('Total de Contatos', $total)
                ->description('+12% este mês')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Clientes', $clients)
                ->description("{$clientPct}% do total")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Leads', $leads)
                ->description("{$leadPct}% do total")
                ->descriptionIcon('heroicon-m-bolt')
                ->color('warning'),

            Stat::make('Fornecedores', $suppliers)
                ->description("{$supplierPct}% do total")
                ->descriptionIcon('heroicon-m-truck')
                ->color('purple'),
        ];
    }
}
