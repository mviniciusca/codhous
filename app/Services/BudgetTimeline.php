<?php

namespace App\Services;

use App\Filament\Resources\BudgetResource;
use App\Livewire\Budget as BudgetWizard;
use App\Models\Budget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

/**
 * Transforma o activity log de um orçamento em uma linha do tempo legível.
 *
 * Cada entrada: ['icon', 'color', 'title', 'actor', 'at', 'details' => [...], 'changes' => [[label, old, new, delta, trend]]]
 */
class BudgetTimeline
{
    /** Campos do content exibidos no histórico, na ordem em que aparecem */
    protected const CONTENT_FIELDS = [
        'shipping' => 'Frete',
        'tax' => 'Taxas',
        'discount' => 'Desconto',
        'total' => 'Total',
        'customer_name' => 'Nome',
        'customer_phone' => 'Telefone',
        'customer_email' => 'E-mail',
        'postcode' => 'CEP',
        'street' => 'Logradouro',
        'number' => 'Número',
        'complement' => 'Complemento',
        'neighborhood' => 'Bairro',
        'city' => 'Cidade',
        'state' => 'UF',
        'delivery_date' => 'Data de entrega',
        'delivery_period' => 'Período',
        'needs_pump' => 'Bombeamento',
        'notes' => 'Observações',
    ];

    protected const MONEY_FIELDS = ['shipping', 'tax', 'discount', 'total', 'price'];

    /** Para estes valores, subir é ruim para o cliente (vermelho); desconto é o contrário */
    protected const COST_FIELDS = ['shipping', 'tax', 'total', 'price'];

    public function __construct(protected Budget $budget) {}

    public function total(): int
    {
        return $this->query()->count();
    }

    /**
     * Entradas mais recentes primeiro, agrupadas por dia.
     *
     * @return Collection<string, array<int, array>>
     */
    public function grouped(int $limit = 15): Collection
    {
        $tz = config('app.display_timezone');

        return $this->query()
            ->with('causer')
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(fn (Activity $activity) => $this->entry($activity))
            ->filter()
            ->groupBy(fn (array $entry) => $this->dayLabel($entry['at']->copy()->timezone($tz)));
    }

    protected function query()
    {
        return Activity::query()
            ->where('subject_type', Budget::class)
            ->where('subject_id', $this->budget->id)
            // Registros antigos duplicados pela troca de etapa (já aparecem como alteração de status)
            ->where('description', 'not like', 'Alterou a etapa%');
    }

    protected function entry(Activity $activity): ?array
    {
        $properties = $activity->properties?->toArray() ?? [];
        $description = (string) $activity->description;
        $actor = $activity->causer?->name;

        $base = [
            'id' => $activity->id,
            'at' => $activity->created_at,
            'actor' => $actor ?? 'Sistema',
            'details' => [],
            'changes' => [],
        ];

        return match (true) {
            $description === 'created' => array_merge($base, $this->createdEntry($properties['attributes'] ?? [], $actor)),
            $description === 'updated' || str_starts_with($description, 'price_updated') => $this->updatedEntry($base, $properties),
            $description === 'deleted' => array_merge($base, ['icon' => 'heroicon-m-trash', 'color' => 'danger', 'title' => 'Movido para a lixeira']),
            $description === 'restored' => array_merge($base, ['icon' => 'heroicon-m-arrow-uturn-left', 'color' => 'success', 'title' => 'Restaurado da lixeira']),
            in_array($description, ['item_added', 'item_updated', 'item_removed'], true) => $this->itemEntry($base, $description, $properties),
            str_starts_with($description, 'Documento') => array_merge($base, [
                'icon' => 'heroicon-m-paper-clip',
                'color' => str_contains($description, 'removido') ? 'danger' : 'info',
                'title' => $description,
                'details' => array_filter([data_get($properties, 'attributes.title')]),
            ]),
            str_starts_with($description, 'Downloaded PDF') || str_starts_with($description, 'Cliente baixou') => array_merge($base, [
                'icon' => 'heroicon-m-eye',
                'color' => 'success',
                'title' => 'Cliente abriu o PDF',
                'actor' => 'Cliente',
            ]),
            str_contains($description, 'WhatsApp') => array_merge($base, ['icon' => 'heroicon-m-chat-bubble-left-right', 'color' => 'success', 'title' => 'Proposta enviada pelo WhatsApp']),
            str_starts_with($description, 'Enviou orçamento por e-mail') => array_merge($base, [
                'icon' => 'heroicon-m-envelope',
                'color' => 'primary',
                'title' => 'Proposta enviada por e-mail',
                'details' => array_filter([str_replace('Enviou orçamento por e-mail para ', 'Para ', $description)]),
            ]),
            str_starts_with($description, 'Enviou mensagem') => array_merge($base, [
                'icon' => 'heroicon-m-chat-bubble-bottom-center-text',
                'color' => 'primary',
                'title' => 'Mensagem enviada por e-mail',
                'details' => array_filter(['Assunto: '.($properties['subject'] ?? '—')]),
            ]),
            str_starts_with($description, 'Gerou link') => array_merge($base, ['icon' => 'heroicon-m-link', 'color' => 'gray', 'title' => 'Link do PDF gerado']),
            default => array_merge($base, ['icon' => 'heroicon-m-information-circle', 'color' => 'gray', 'title' => $description]),
        };
    }

    protected function createdEntry(array $attributes, ?string $actor): array
    {
        $content = $attributes['content'] ?? [];
        $content = is_string($content) ? (json_decode($content, true) ?? []) : $content;

        $products = collect($content['products'] ?? []);
        $details = array_filter([
            $content['customer_name'] ?? null,
            $products->isNotEmpty() ? $products->count().($products->count() === 1 ? ' item' : ' itens') : null,
            isset($content['total']) ? 'Total '.$this->money($content['total']) : null,
        ]);

        // Pedido do site não tem user_id; o causer pode ser um admin logado no mesmo navegador
        $fromSite = empty($attributes['user_id']);

        return [
            'icon' => $fromSite ? 'heroicon-m-globe-alt' : 'heroicon-m-plus-circle',
            'color' => 'success',
            'title' => $fromSite ? 'Pedido recebido pelo site' : 'Orçamento criado no painel',
            'actor' => $fromSite ? 'Cliente' : ($actor ?? 'Sistema'),
            'details' => [implode(' · ', $details)],
        ];
    }

    protected function updatedEntry(array $base, array $properties): ?array
    {
        $old = $properties['old'] ?? [];
        $new = $properties['attributes'] ?? [];
        $changes = [];

        if (array_key_exists('status', $old) && ($old['status'] ?? null) !== ($new['status'] ?? null)) {
            $changes[] = $this->change(
                'Etapa',
                BudgetResource::STATUSES[$old['status']] ?? $old['status'],
                BudgetResource::STATUSES[$new['status']] ?? $new['status'],
            );
        }

        if (array_key_exists('is_active', $old) && (bool) $old['is_active'] !== (bool) ($new['is_active'] ?? false)) {
            $changes[] = $this->change('Ativo', $old['is_active'] ? 'Sim' : 'Não', ($new['is_active'] ?? false) ? 'Sim' : 'Não');
        }

        $oldContent = $this->decode($old['content'] ?? null);
        $newContent = $this->decode($new['content'] ?? null);

        if ($oldContent !== null || $newContent !== null) {
            foreach (self::CONTENT_FIELDS as $field => $label) {
                $before = $oldContent[$field] ?? null;
                $after = $newContent[$field] ?? null;

                if ($this->same($field, $before, $after)) {
                    continue;
                }

                $changes[] = in_array($field, self::MONEY_FIELDS, true)
                    ? $this->moneyChange($field, $label, $before, $after)
                    : $this->change($label, $this->text($field, $before), $this->text($field, $after));
            }
        }

        if (empty($changes)) {
            return null;
        }

        $labels = collect($changes)->pluck('label');
        $onlyStatus = $labels->all() === ['Etapa'];
        $hasMoney = $labels->intersect(['Frete', 'Taxas', 'Desconto', 'Total'])->isNotEmpty();

        return array_merge($base, [
            'icon' => match (true) {
                $onlyStatus => 'heroicon-m-flag',
                $hasMoney => 'heroicon-m-currency-dollar',
                default => 'heroicon-m-pencil-square',
            },
            'color' => match (true) {
                $onlyStatus => 'primary',
                $hasMoney => 'warning',
                default => 'gray',
            },
            'title' => match (true) {
                $onlyStatus => 'Etapa alterada',
                $hasMoney => 'Valores atualizados',
                default => 'Dados atualizados',
            },
            'changes' => $changes,
        ]);
    }

    protected function itemEntry(array $base, string $event, array $properties): array
    {
        $item = $properties['item'] ?? 'Item';
        $unit = $properties['unit'] ?? '';
        $summary = BudgetCalculatorService::formatQuantityWithUnit((float) ($properties['quantity'] ?? 0), $unit)
            .' × '.$this->money($properties['price'] ?? 0);

        if ($event === 'item_added') {
            return array_merge($base, ['icon' => 'heroicon-m-plus', 'color' => 'success', 'title' => 'Item adicionado', 'details' => ["{$item} — {$summary}"]]);
        }

        if ($event === 'item_removed') {
            return array_merge($base, ['icon' => 'heroicon-m-minus', 'color' => 'danger', 'title' => 'Item removido', 'details' => ["{$item} — {$summary}"]]);
        }

        $changes = [];
        foreach ($properties['changes'] ?? [] as $field => $values) {
            $changes[] = match ($field) {
                'product' => $this->change('Produto', $values['old'], $values['new']),
                'quantity' => $this->numberChange('Quantidade', (float) $values['old'], (float) $values['new'], $unit),
                'price' => $this->moneyChange('price', 'Preço unitário', $values['old'], $values['new']),
                default => $this->change($field, (string) $values['old'], (string) $values['new']),
            };
        }

        return array_merge($base, [
            'icon' => 'heroicon-m-shopping-bag',
            'color' => 'warning',
            'title' => 'Item alterado',
            'details' => [$item],
            'changes' => $changes,
        ]);
    }

    protected function change(string $label, ?string $old, ?string $new, ?string $delta = null, ?string $trend = null): array
    {
        return [
            'label' => $label,
            'old' => filled($old) ? $old : '—',
            'new' => filled($new) ? $new : '—',
            'delta' => $delta,
            'trend' => $trend,
        ];
    }

    protected function moneyChange(string $field, string $label, mixed $old, mixed $new): array
    {
        $before = $this->number($old);
        $after = $this->number($new);
        $diff = $after - $before;

        $trend = null;
        if (abs($diff) > 0.009) {
            $isCost = in_array($field, self::COST_FIELDS, true);
            $trend = ($diff > 0) === $isCost ? 'up-bad' : 'down-good';
            // Para o total, um aumento não é "ruim" para a empresa: mostramos neutro
            if ($field === 'total') {
                $trend = 'neutral';
            }
        }

        return $this->change(
            $label,
            $old === null ? null : $this->money($before),
            $new === null ? null : $this->money($after),
            abs($diff) > 0.009 ? ($diff > 0 ? '+' : '−').$this->money(abs($diff)) : null,
            $trend,
        );
    }

    protected function numberChange(string $label, float $old, float $new, string $unit): array
    {
        $diff = $new - $old;
        $suffix = $unit ? ' '.$unit : '';

        return $this->change(
            $label,
            BudgetCalculatorService::formatQuantity($old).$suffix,
            BudgetCalculatorService::formatQuantity($new).$suffix,
            ($diff > 0 ? '+' : '−').BudgetCalculatorService::formatQuantity(abs($diff)).$suffix,
            'neutral',
        );
    }

    protected function same(string $field, mixed $before, mixed $after): bool
    {
        if (in_array($field, self::MONEY_FIELDS, true)) {
            return abs($this->number($before) - $this->number($after)) < 0.009;
        }

        return trim((string) $before) === trim((string) $after);
    }

    protected function text(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match ($field) {
            'delivery_date' => Carbon::parse($value)->format('d/m/Y'),
            'delivery_period' => BudgetWizard::DELIVERY_PERIODS[$value] ?? $value,
            'needs_pump' => BudgetWizard::PUMP_OPTIONS[$value] ?? $value,
            'notes' => Str::limit((string) $value, 120),
            default => (string) $value,
        };
    }

    protected function money(mixed $value): string
    {
        return 'R$ '.number_format($this->number($value), 2, ',', '.');
    }

    /**
     * Valores antigos podem estar como "1.234,56", "1234.56" ou número.
     */
    protected function number(mixed $value): float
    {
        return BudgetResource::parseMoney($value ?? 0);
    }

    protected function decode(mixed $content): ?array
    {
        if ($content === null) {
            return null;
        }

        return is_array($content) ? $content : (json_decode((string) $content, true) ?: []);
    }

    protected function dayLabel(Carbon $date): string
    {
        $today = now(config('app.display_timezone'))->startOfDay();

        return match (true) {
            $date->isSameDay($today) => 'Hoje',
            $date->isSameDay($today->copy()->subDay()) => 'Ontem',
            default => $date->translatedFormat('d \d\e F \d\e Y'),
        };
    }
}
