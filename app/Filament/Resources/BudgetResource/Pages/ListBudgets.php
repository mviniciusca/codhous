<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Filament\Resources\BudgetResource\Widgets\BudgetStatsOverview;
use App\Models\Budget;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    public function getTitle(): string
    {
        return 'Orçamentos';
    }

    public function getSubheading(): ?string
    {
        return 'Pedidos recebidos pelo site e criados pela equipe.';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BudgetStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        $trashed = Budget::onlyTrashed()->count();

        return [
            Actions\Action::make('board')
                ->label('Quadro')
                ->icon('heroicon-o-view-columns')
                ->color('gray')
                ->url(BudgetResource::getUrl('board')),
            Actions\ActionGroup::make([
                ExportAction::make('export')
                    ->label('Exportar para Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    // Exporta exatamente o que está na tela: aba, filtros e busca
                    ->exports([BudgetResource::excelExport()->useTableQuery()]),
                Actions\Action::make('bin')
                    ->label('Lixeira'.($trashed > 0 ? " ({$trashed})" : ''))
                    ->icon('heroicon-o-trash')
                    ->url(BudgetResource::getUrl('bin')),
            ])
                ->label('Mais')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->button(),
            Actions\CreateAction::make()
                ->label('Novo orçamento')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $count = fn (array $statuses) => Budget::query()
            ->where('is_active', true)
            ->whereIn('status', $statuses)
            ->count();

        $tab = fn (string $label, array $statuses, ?string $color = null) => Tab::make($label)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', $statuses))
            ->badge($count($statuses) ?: null)
            ->badgeColor($color ?? 'gray');

        return [
            'abertos' => $tab('Em aberto', ['pending', 'on going'], 'primary')
                ->icon('heroicon-m-inbox-stack'),
            'novos' => $tab('Novos', ['pending'], 'warning'),
            'negociacao' => $tab('Em negociação', ['on going'], 'primary'),
            'fechados' => $tab('Fechados', ['done'], 'success'),
            'arquivados' => $tab('Arquivados', ['ignored']),
            'todos' => Tab::make('Todos'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'abertos';
    }
}
