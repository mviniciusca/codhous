<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

/**
 * Quadro kanban dos orçamentos por etapa (arrastar e soltar muda o status).
 */
class BudgetBoard extends Page
{
    protected static string $resource = BudgetResource::class;

    protected static string $view = 'filament.budget.board';

    /** Cartões exibidos por coluna */
    protected const PER_COLUMN = 50;

    /** Dias sem movimentação para destacar um orçamento em negociação */
    public const STALE_AFTER_DAYS = 3;

    #[Url(as: 'busca')]
    public string $search = '';

    public function getTitle(): string|Htmlable
    {
        return 'Quadro de orçamentos';
    }

    public function getSubheading(): ?string
    {
        return 'Arraste os cartões entre as colunas para mudar a etapa.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('list')
                ->label('Lista')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(BudgetResource::getUrl('index')),
            Action::make('create')
                ->label('Novo orçamento')
                ->icon('heroicon-o-plus')
                ->url(BudgetResource::getUrl('create')),
        ];
    }

    public function moveBudget(int|string $id, string $status): void
    {
        if (! array_key_exists($status, BudgetResource::STATUSES)) {
            return;
        }

        $budget = Budget::find($id);
        if (! $budget || $budget->status === $status) {
            return;
        }

        $budget->update(['status' => $status]);

        Notification::make()
            ->title($budget->code.' → '.BudgetResource::STATUSES[$status])
            ->success()
            ->send();
    }

    /**
     * @return array<string, array{label: string, count: int, total: float, cards: Collection}>
     */
    public function getColumns(): array
    {
        $columns = [];

        foreach (BudgetResource::STATUSES as $status => $label) {
            $query = $this->baseQuery()->where('status', $status);

            $columns[$status] = [
                'label' => $label,
                'count' => (clone $query)->count(),
                'total' => (clone $query)->pluck('content')
                    ->sum(fn ($content) => BudgetResource::parseMoney(data_get($content, 'total', 0))),
                'cards' => (clone $query)
                    ->with('budgetItems.product')
                    ->latest($status === 'pending' ? 'created_at' : 'updated_at')
                    ->limit(self::PER_COLUMN)
                    ->get()
                    ->map(fn (Budget $budget) => $this->card($budget)),
            ];
        }

        return $columns;
    }

    protected function baseQuery(): Builder
    {
        return Budget::query()
            ->where('is_active', true)
            ->when(filled($this->search), function (Builder $query) {
                $term = '%'.trim($this->search).'%';

                $query->where(fn (Builder $q) => $q
                    ->where('code', 'like', $term)
                    ->orWhere('content->customer_name', 'like', $term)
                    ->orWhere('content->customer_phone', 'like', $term)
                    ->orWhere('content->city', 'like', $term));
            });
    }

    protected function card(Budget $budget): array
    {
        $content = $budget->content ?? [];
        $tz = config('app.display_timezone');
        $items = $budget->budgetItems;

        $deliveryDate = ! empty($content['delivery_date']) ? Carbon::parse($content['delivery_date'], $tz)->startOfDay() : null;
        $today = now($tz)->startOfDay();
        $daysToDelivery = $deliveryDate ? (int) $today->diffInDays($deliveryDate, false) : null;

        return [
            'id' => $budget->id,
            'code' => $budget->code,
            'url' => BudgetResource::getUrl('edit', ['record' => $budget]),
            'customer' => $content['customer_name'] ?? 'Cliente não informado',
            'place' => collect([$content['neighborhood'] ?? null, $content['city'] ?? null])->filter()->implode(' · '),
            'total' => BudgetResource::parseMoney($content['total'] ?? 0),
            'items' => $items->isEmpty()
                ? null
                : $items->first()->product?->name.($items->count() > 1 ? ' +'.($items->count() - 1) : ''),
            'age' => $budget->created_at?->timezone($tz)->locale('pt_BR')->diffForHumans(short: true),
            'from_site' => $budget->user_id === null,
            'whatsapp' => (bool) $budget->notified_via_whatsapp,
            'email' => (bool) $budget->notified_via_email,
            'delivery' => $deliveryDate?->format('d/m'),
            'delivery_soon' => $daysToDelivery !== null && $daysToDelivery <= 3 && $budget->status !== 'ignored',
            'stale' => $budget->status === 'on going'
                && $budget->updated_at?->lt(now()->subDays(self::STALE_AFTER_DAYS)),
        ];
    }
}
