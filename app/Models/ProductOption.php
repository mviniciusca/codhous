<?php

namespace App\Models;

use App\Enums\ProductUnit;
use Database\Factories\ProductOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Variação vendável de um produto (ex: Concreto FCK 25), com preço e custo próprios.
 * Variações usadas em orçamentos são arquivadas (soft delete), nunca apagadas.
 */
class ProductOption extends Model
{
    /** @use HasFactory<ProductOptionFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
        'unit' => ProductUnit::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductOption $option) {
            // Herda a unidade do produto
            if ($option->unit === null && $option->product_id) {
                $option->unit = Product::withTrashed()->find($option->product_id)?->unit;
            }

            $option->slug ??= Str::slug($option->name);
        });

        static::saving(function (ProductOption $option) {
            if ($option->isDirty('name')) {
                $option->slug = Str::slug($option->name);
            }
        });

        // Limpa o cache do site ao alterar preço, nome ou status de uma opção
        $flush = function (ProductOption $option) {
            Cache::forget(self::cacheKey($option->id));
            Cache::forget(self::catalogCacheKey($option->product_id));
            Cache::forget(Product::CATALOG_CACHE_KEY);

            if ($option->wasChanged('product_id') && $option->getOriginal('product_id')) {
                Cache::forget(self::catalogCacheKey($option->getOriginal('product_id')));
            }
        };

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);
    }

    public static function cacheKey(int|string $id): string
    {
        return 'product_option_'.$id;
    }

    /** Opções de um produto exibidas no formulário de orçamento do site */
    public static function catalogCacheKey(int|string|null $productId): string
    {
        return 'product_options_plucked_'.$productId;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'price', 'cost', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('catalog');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function isUsedInBudgets(): bool
    {
        return $this->budgetItems()->exists();
    }

    /**
     * Margem bruta sobre o preço de venda, em %. Null quando o custo não foi informado.
     */
    public function marginPercent(): ?float
    {
        if ($this->cost === null || (float) $this->price <= 0) {
            return null;
        }

        return round(((float) $this->price - (float) $this->cost) / (float) $this->price * 100, 1);
    }
}
