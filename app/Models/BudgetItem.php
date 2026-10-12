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
        'subtotal',
    ];

    /** Campos acompanhados no histórico do orçamento */
    protected const TRACKED_FIELDS = ['product_id', 'product_option_id', 'quantity', 'price'];

    protected static function booted(): void
    {
        static::created(fn (BudgetItem $item) => $item->logChange('item_added'));
        static::updated(fn (BudgetItem $item) => $item->logChange('item_updated'));
        static::deleted(fn (BudgetItem $item) => $item->logChange('item_removed'));
    }

    /**
     * Ex: "Concreto Bombeado · FCK 25"
     */
    public function describe(?int $productId = null, ?int $optionId = null): string
    {
        $product = Product::withTrashed()->find($productId ?? $this->product_id)?->name ?? 'Produto removido';
        $option = ProductOption::find($optionId ?? $this->product_option_id)?->name;

        return $option ? "{$product} · {$option}" : $product;
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
        return $this->belongsTo(Product::class);
    }

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
