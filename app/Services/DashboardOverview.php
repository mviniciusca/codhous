<?php

namespace App\Services;

use App\Filament\Resources\BudgetResource;
use App\Filament\Resources\MailResource;
use App\Livewire\Budget as BudgetWizard;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Mail;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Dados da visão geral do painel: indicadores comerciais, agenda de entregas,
 * pendências e produtos mais vendidos. Tudo sai dos orçamentos ativos.
 */
class DashboardOverview
{
    /** Dias exibidos na agenda de entregas, hoje incluso */
    public const AGENDA_DAYS = 7;

    /** Entrega em até N dias com orçamento ainda em aberto vira pendência */
    public const DELIVERY_ALERT_DAYS = 3;

    /** Janela da taxa de conversão e do ranking de produtos */
    public const WINDOW_DAYS = 90;

    protected string $tz;

    protected CarbonImmutable $now;

    protected CarbonImmutable $today;

    public function __construct()
    {
        $this->tz = config('app.display_timezone');
        $this->now = CarbonImmutable::now($this->tz)->locale('pt_BR');
        $this->today = $this->now->startOfDay();
    }

    public function now(): CarbonImmutable
    {
        return $this->now;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function kpis(): array
    {
        return [
            'new' => $this->newRequestsKpi(),
            'negotiation' => $this->negotiationKpi(),
            'closed' => $this->closedThisMonthKpi(),
            'conversion' => $this->conversionKpi(),
        ];
    }

    protected function newRequestsKpi(): array
    {
        $pending = $this->active()->where('status', 'pending');
        $count = (clone $pending)->count();
        $oldest = (clone $pending)->oldest()->value('created_at');

        $start = $this->today->subDays(13);
        $perDay = $this->active()
            ->where('created_at', '>=', $start->utc())
            ->pluck('created_at')
            ->countBy(fn ($date) => $date->timezone($this->tz)->toDateString());

        return [
            'value' => (string) $count,
            'note' => $count === 0
                ? 'Nenhum pedido aguardando'
                : 'Mais antigo aguarda '.$this->waitingFor(CarbonImmutable::parse($oldest)),
            'tone' => $count > 0 ? 'warning' : 'success',
            'trend' => collect(range(13, 0))
                ->map(fn (int $daysAgo) => (int) ($perDay[$this->today->subDays($daysAgo)->toDateString()] ?? 0))
                ->all(),
            'url' => BudgetResource::getUrl('index', ['activeTab' => 'novos']),
        ];
    }

    protected function negotiationKpi(): array
    {
        $budgets = $this->active()->where('status', 'on going')->get(['id', 'status', 'content', 'status_changed_at', 'updated_at']);
        $stale = $budgets->filter->isStale()->count();
        $count = $budgets->count();

        return [
            'value' => $this->money($this->sumTotal($budgets)),
            'note' => $count.' '.($count === 1 ? 'orçamento' : 'orçamentos')
                .($stale > 0 ? " · {$stale} ".($stale === 1 ? 'parado' : 'parados') : ''),
            'tone' => $stale > 0 ? 'danger' : 'primary',
            'url' => BudgetResource::getUrl('index', ['activeTab' => 'negociacao']),
        ];
    }

    protected function closedThisMonthKpi(): array
    {
        $monthStart = $this->now->startOfMonth();
        $budgets = $this->closedBetween($monthStart, $this->now)->with('budgetItems.productOption')->get(['id', 'content']);
        $total = $this->sumTotal($budgets);

        // Compara com o mesmo intervalo de dias do mês anterior, não com o mês inteiro
        $previousStart = $monthStart->subMonthNoOverflow();
        $previousEnd = $previousStart->addDays($this->now->day - 1)->setTimeFrom($this->now)->min($previousStart->endOfMonth());
        $previousTotal = $this->sumTotal($this->closedBetween($previousStart, $previousEnd)->get(['id', 'content']));

        $volume = $budgets->flatMap->budgetItems
            ->filter(fn (BudgetItem $item) => $item->unitLabel() === 'm³')
            ->sum(fn (BudgetItem $item) => (float) $item->quantity);

        return [
            'value' => $this->money($total),
            'label' => 'Fechado em '.$this->now->translatedFormat('F'),
            'note' => $budgets->isEmpty()
                ? 'Nenhum pedido fechado no mês'
                : $budgets->count().' '.($budgets->count() === 1 ? 'pedido' : 'pedidos')
                    .($volume > 0 ? ' · '.BudgetCalculatorService::formatQuantityWithUnit($volume, 'm³') : ''),
            'delta' => $previousTotal > 0 ? (int) round(($total - $previousTotal) / $previousTotal * 100) : null,
            'deltaLabel' => 'vs. mesmo período de '.$previousStart->translatedFormat('M'),
            'tone' => 'success',
            'url' => BudgetResource::getUrl('index', ['activeTab' => 'fechados']),
        ];
    }

    protected function conversionKpi(): array
    {
        $decided = $this->active()
            ->whereIn('status', ['done', 'ignored'])
            ->where('status_changed_at', '>=', $this->now->subDays(self::WINDOW_DAYS)->utc());

        $total = (clone $decided)->count();
        $done = (clone $decided)->where('status', 'done')->count();
        $rate = $total > 0 ? (int) round($done / $total * 100) : null;

        return [
            'value' => $rate === null ? '—' : $rate.'%',
            'rate' => $rate,
            'note' => $total === 0
                ? 'Sem orçamentos decididos'
                : "{$done} de {$total} ".($total === 1 ? 'decidido' : 'decididos'),
            'tone' => match (true) {
                $rate === null => 'gray',
                $rate >= 50 => 'success',
                $rate >= 25 => 'warning',
                default => 'danger',
            },
            'url' => BudgetResource::getUrl('index', ['activeTab' => 'todos']),
        ];
    }

    /**
     * Entregas desejadas nos próximos dias, agrupadas por data. As atrasadas
     * entram nas pendências (attention), não aqui.
     *
     * @return array{days: array<int, array<string, mixed>>, today: array<string, mixed>, listUrl: string}
     */
    public function agenda(): array
    {
        $first = $this->today->toDateString();
        $last = $this->today->addDays(self::AGENDA_DAYS - 1)->toDateString();

        $scheduled = $this->active()
            ->whereIn('status', ['pending', 'on going', 'done'])
            ->whereNotNull('content->delivery_date')
            ->where('content->delivery_date', '>=', $first)
            ->where('content->delivery_date', '<=', $last)
            ->with('budgetItems.productOption')
            ->get()
            ->map(fn (Budget $budget) => $this->deliveryEntry($budget))
            ->sortBy(fn (array $entry) => $entry['sort'])
            ->groupBy('date');

        $days = collect(range(0, self::AGENDA_DAYS - 1))->map(function (int $offset) use ($scheduled) {
            $date = $this->today->addDays($offset);
            $entries = $scheduled->get($date->toDateString(), collect())->values();

            return [
                'key' => $date->toDateString(),
                'weekday' => match ($offset) {
                    0 => 'Hoje',
                    1 => 'Amanhã',
                    default => ucfirst(rtrim($date->translatedFormat('D'), '.')),
                },
                'day' => $date->format('d'),
                'long' => ucfirst($date->translatedFormat('l, j \d\e F')),
                'count' => $entries->count(),
                'volume' => round($entries->sum('volume'), 2),
                'volumeLabel' => $this->volumeLabel($entries->sum('volume')),
                'confirmed' => $entries->where('status', 'done')->count(),
                'entries' => $entries->all(),
            ];
        });

        $maxVolume = max(1, $days->max('volume'));
        $days = $days->map(fn (array $day) => $day + ['load' => (int) round($day['volume'] / $maxVolume * 100)]);

        return [
            'days' => $days->all(),
            'today' => $days->first(),
            'listUrl' => BudgetResource::getUrl('index', ['activeTab' => 'todos', 'tableFilters' => ['delivery' => ['value' => 'week']]]),
        ];
    }

    /**
     * Pendências que pedem ação de alguém. Só entram itens com contagem maior que zero.
     *
     * @return array<int, array<string, mixed>>
     */
    public function attention(): array
    {
        $soonLimit = $this->today->addDays(self::DELIVERY_ALERT_DAYS)->toDateString();

        $overdue = $this->overdueQuery()->count();

        $deliverySoon = $this->active()
            ->whereIn('status', ['pending', 'on going'])
            ->whereNotNull('content->delivery_date')
            ->where('content->delivery_date', '>=', $this->today->toDateString())
            ->where('content->delivery_date', '<=', $soonLimit)
            ->count();

        $unanswered = $this->active()
            ->where('status', 'pending')
            ->where('created_at', '<=', $this->now->subDay()->utc())
            ->count();

        $stale = $this->active()
            ->where('status', 'on going')
            ->where('status_changed_at', '<=', $this->now->subDays(Budget::STALE_AFTER_DAYS)->utc())
            ->count();

        $unread = Mail::query()
            ->where('is_read', false)
            ->where('is_sent', false)
            ->where('is_spam', false)
            ->count();

        $openWithFilter = fn (string $filter) => BudgetResource::getUrl('index', [
            'activeTab' => 'abertos',
            'tableFilters' => ['delivery' => ['value' => $filter]],
        ]);

        return collect([
            [
                'count' => $overdue,
                'title' => $overdue === 1 ? 'entrega com data vencida' : 'entregas com data vencida',
                'note' => 'Data de entrega passou e o orçamento segue em aberto',
                'tone' => 'danger',
                'icon' => 'heroicon-o-exclamation-triangle',
                'url' => $openWithFilter('late'),
            ],
            [
                'count' => $deliverySoon,
                'title' => $deliverySoon === 1 ? 'entrega próxima sem fechamento' : 'entregas próximas sem fechamento',
                'note' => 'Entrega em até '.self::DELIVERY_ALERT_DAYS.' dias, orçamento ainda aberto',
                'tone' => 'warning',
                'icon' => 'heroicon-o-truck',
                'url' => $openWithFilter('week'),
            ],
            [
                'count' => $unanswered,
                'title' => $unanswered === 1 ? 'pedido sem resposta' : 'pedidos sem resposta',
                'note' => 'Novos pedidos aguardando há mais de 24 h',
                'tone' => 'warning',
                'icon' => 'heroicon-o-inbox-arrow-down',
                'url' => BudgetResource::getUrl('index', ['activeTab' => 'novos']),
            ],
            [
                'count' => $stale,
                'title' => $stale === 1 ? 'negociação parada' : 'negociações paradas',
                'note' => 'Sem mudança de etapa há '.Budget::STALE_AFTER_DAYS.'+ dias',
                'tone' => 'danger',
                'icon' => 'heroicon-o-chat-bubble-left-ellipsis',
                'url' => BudgetResource::getUrl('index', ['activeTab' => 'negociacao']),
            ],
            [
                'count' => $unread,
                'title' => $unread === 1 ? 'mensagem não lida' : 'mensagens não lidas',
                'note' => 'Contatos recebidos pelo site',
                'tone' => 'primary',
                'icon' => 'heroicon-o-envelope',
                'url' => MailResource::getUrl('index'),
            ],
        ])->filter(fn (array $item) => $item['count'] > 0)->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recentBudgets(int $limit = 6): array
    {
        return $this->active()
            ->with('budgetItems.productOption')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Budget $budget) {
                $content = $budget->content ?? [];

                return [
                    'id' => $budget->id,
                    'code' => $budget->code,
                    'url' => BudgetResource::getUrl('edit', ['record' => $budget]),
                    'customer' => $content['customer_name'] ?? 'Cliente não informado',
                    'place' => $this->place($content),
                    'items' => $this->itemsSummary($budget->budgetItems),
                    'total' => $this->money(BudgetResource::parseMoney($content['total'] ?? 0)),
                    'status' => $budget->status,
                    'fromSite' => $budget->user_id === null,
                    'date' => $budget->created_at?->timezone($this->tz)->format('d/m/Y'),
                    'ago' => $budget->created_at?->timezone($this->tz)->locale('pt_BR')->diffForHumans(),
                ];
            })
            ->all();
    }

    /**
     * Produtos mais vendidos (orçamentos fechados na janela), por faturamento.
     *
     * @return array<int, array{name: string, revenue: float, revenueLabel: string, quantityLabel: string, share: int}>
     */
    public function topProducts(int $limit = 5): array
    {
        $items = BudgetItem::query()
            ->whereHas('budget', fn (Builder $query) => $query
                ->where('is_active', true)
                ->where('status', 'done')
                ->where('status_changed_at', '>=', $this->now->subDays(self::WINDOW_DAYS)->utc()))
            ->with('product', 'productOption')
            ->get();

        $ranking = $items
            ->groupBy(fn (BudgetItem $item) => $item->productLabel())
            ->map(function (Collection $group, string $name) {
                $units = $group->map(fn (BudgetItem $item) => $item->unitLabel())->filter()->unique();

                return [
                    'name' => $name,
                    'revenue' => (float) $group->sum('subtotal'),
                    // Só soma quantidades quando todas as linhas usam a mesma unidade
                    'quantityLabel' => $units->count() <= 1
                        ? BudgetCalculatorService::formatQuantityWithUnit((float) $group->sum('quantity'), $units->first())
                        : $group->count().' itens',
                ];
            })
            ->sortByDesc('revenue')
            ->take($limit)
            ->values();

        $max = max(1, (float) $ranking->max('revenue'));

        return $ranking
            ->map(fn (array $row) => $row + [
                'revenueLabel' => $this->money($row['revenue']),
                'share' => (int) max(2, round($row['revenue'] / $max * 100)),
            ])
            ->all();
    }

    protected function deliveryEntry(Budget $budget): array
    {
        $content = $budget->content ?? [];
        $items = $budget->budgetItems;
        $volume = $items->filter(fn (BudgetItem $item) => $item->unitLabel() === 'm³')->sum(fn (BudgetItem $item) => (float) $item->quantity);
        $period = $content['delivery_period'] ?? null;
        $date = CarbonImmutable::parse($content['delivery_date'], $this->tz);

        return [
            'id' => $budget->id,
            'code' => $budget->code,
            'url' => BudgetResource::getUrl('edit', ['record' => $budget]),
            'date' => $date->toDateString(),
            'dateLabel' => $date->format('d/m'),
            'period' => BudgetWizard::DELIVERY_PERIODS[$period] ?? 'A combinar',
            'periodKey' => $period,
            // Manhã antes de tarde; flexível e sem período por último
            'sort' => $date->toDateString().'-'.match ($period) {
                'manha' => 1,
                'tarde' => 2,
                default => 3,
            }.'-'.$budget->id,
            'customer' => $content['customer_name'] ?? 'Cliente não informado',
            'place' => $this->place($content),
            'items' => $this->itemsSummary($items),
            'volume' => (float) $volume,
            'volumeLabel' => $volume > 0 ? $this->volumeLabel($volume) : null,
            'pump' => match ($content['needs_pump'] ?? null) {
                'sim' => 'Com bomba',
                'nao_sei' => 'Bomba a definir',
                default => null,
            },
            'status' => $budget->status,
        ];
    }

    protected function overdueQuery(): Builder
    {
        return $this->active()
            ->whereIn('status', ['pending', 'on going'])
            ->whereNotNull('content->delivery_date')
            ->where('content->delivery_date', '<', $this->today->toDateString());
    }

    protected function closedBetween(CarbonImmutable $from, CarbonImmutable $to): Builder
    {
        return $this->active()
            ->where('status', 'done')
            ->whereBetween('status_changed_at', [$from->utc(), $to->utc()]);
    }

    protected function active(): Builder
    {
        return Budget::query()->where('is_active', true);
    }

    protected function itemsSummary(Collection $items): ?string
    {
        $first = $items->first();
        if (! $first) {
            return null;
        }

        $label = $first->optionLabel() ? $first->productLabel().' · '.$first->optionLabel() : $first->productLabel();

        return $label.($items->count() > 1 ? ' +'.($items->count() - 1) : '');
    }

    protected function place(array $content): ?string
    {
        return collect([$content['neighborhood'] ?? null, $content['city'] ?? null])->filter()->implode(' · ') ?: null;
    }

    protected function sumTotal(Collection $budgets): float
    {
        return $budgets->sum(fn (Budget $budget) => BudgetResource::parseMoney(data_get($budget->content, 'total', 0)));
    }

    protected function waitingFor(CarbonImmutable $since): string
    {
        $hours = (int) $since->diffInHours($this->now);

        return match (true) {
            $hours < 1 => 'há menos de 1 h',
            $hours < 24 => "há {$hours} h",
            (int) floor($hours / 24) === 1 => 'há 1 dia',
            default => 'há '.(int) floor($hours / 24).' dias',
        };
    }

    protected function volumeLabel(float $volume): string
    {
        return BudgetCalculatorService::formatQuantity($volume).' m³';
    }

    public function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
