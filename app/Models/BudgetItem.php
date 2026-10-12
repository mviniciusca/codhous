<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    protected $table = 'budget_product';

    protected $fillable = [
        'budget_id',
        'product_id',
        'product_option_id',
        'location_id',
        'quantity',
        'price',
        'unit_cost',
        'subtotal',
        'product_name',
        'option_name',
        'unit',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /** Campos acompanhados no histórico do orçamento */
    protected const TRACKED_FIELDS = ['product_id', 'product_option_id', 'quantity', 'price'];

    protected static function booted(): void
    {
        // Guarda nome, unidade e custo do momento da venda: renomear ou arquivar
        // uma variação depois não altera orçamentos já feitos.
        static::saving(function (BudgetItem $item) {
            if ($item->isDirty('product_id') || blank($item->product_name)) {
                $item->product_name = Product::withTrashed()->find($item->product_id)?->name;
            }

            if ($item->isDirty('product_option_id') || blank($item->option_name)) {
                $option = ProductOption::withTrashed()->find($item->product_option_id);
                $item->option_name = $option?->name;
                $item->unit = $option?->unit?->value;

                if ($item->isDirty('product_option_id') || $item->unit_cost === null) {
                    $item->unit_cost = $option?->cost;
                }
            }

            $item->subtotal = round((float) $item->quantity * (float) $item->price, 2);
        });

        static::created(fn (BudgetItem $item) => $item->logChange('item_added'));
        static::updated(fn (BudgetItem $item) => $item->logChange('item_updated'));
        static::deleted(fn (BudgetItem $item) => $item->logChange('item_removed'));
    }

    /**
     * Ex: "Concreto Bombeado · FCK 25"
     */
    public function describe(?int $productId = null, ?int $optionId = null): string
    {
        // Sem ids explícitos, usa os nomes gravados na venda
        if ($productId === null && $optionId === null && filled($this->product_name)) {
            return $this->option_name ? "{$this->product_name} · {$this->option_name}" : $this->product_name;
        }

        $product = Product::withTrashed()->find($productId ?? $this->product_id)?->name ?? 'Produto removido';
        $option = ProductOption::withTrashed()->find($optionId ?? $this->product_option_id)?->name;

        return $option ? "{$product} · {$option}" : $product;
    }

    public function productLabel(): string
    {
        return $this->product_name ?: ($this->product?->name ?? 'Produto removido');
    }

    public function optionLabel(): ?string
    {
        return $this->option_name ?: $this->productOption?->name;
    }

    public function unitLabel(): ?string
    {
        return $this->unit ?: $this->productOption?->unit?->value;
    }

    /**
     * Lucro bruto do item (preço − custo) × quantidade. Null quando não há custo.
     */
    public function grossProfit(): ?float
    {
        if ($this->unit_cost === null) {
            return null;
        }

        return round(((float) $this->price - (float) $this->unit_cost) * (float) $this->quantity, 2);
    }

    /**
     * Registra a alteração do item no histórico do orçamento (os itens ficam em outra tabela).
     */
    protected function logChange(string $event): void
    {
        $budget = Budget::withTrashed()->find($this->budget_id);
        if (! $budget) {
            return;
        }

        // Itens gravados junto com o orçamento já aparecem no registro de criação
        if ($event === 'item_added' && $budget->created_at?->gt(now()->subSeconds(10))) {
            return;
        }

        $changes = [];
        if ($event === 'item_updated') {
            foreach (self::TRACKED_FIELDS as $field) {
                $old = $this->getOriginal($field);
                $new = $this->getAttribute($field);

                // Compara como número: "85.00" e 85 são o mesmo valor
                if (abs((float) $old - (float) $new) > 0.0001) {
                    $changes[$field] = ['old' => $old, 'new' => $new];
                }
            }

            if (empty($changes)) {
                return;
            }

            if (isset($changes['product_id']) || isset($changes['product_option_id'])) {
                $changes['product'] = [
                    'old' => $this->describe((int) $this->getOriginal('product_id'), (int) $this->getOriginal('product_option_id')),
                    'new' => $this->describe(),
                ];
                unset($changes['product_id'], $changes['product_option_id']);
            }
        }

        activity('budget')
            ->performedOn($budget)
            ->event($event)
            ->withProperties([
                'item' => $this->describe(),
                'unit' => ProductOption::find($this->product_option_id)?->unit?->value,
                'quantity' => (float) $this->quantity,
                'price' => (float) $this->price,
                'changes' => $changes,
            ])
            ->log($event);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class)->withTrashed();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
