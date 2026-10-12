<?php

namespace App\Models;

use App\Enums\ProductUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'min_quantity' => 'decimal:2',
        'unit' => ProductUnit::class,
    ];

    /** Lista de produtos exibida no formulário de orçamento do site */
    public const CATALOG_CACHE_KEY = 'all_products_plucked';

    protected static function booted(): void
    {
        // Limpa o cache do site ao alterar um produto, para preço e disponibilidade refletirem na hora
        $flush = fn (Product $product) => $product->flushCatalogCache();

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);

        // A unidade é do produto: todas as variações vendem na mesma unidade
        static::saved(function (Product $product) {
            if ($product->wasChanged('unit') || $product->wasRecentlyCreated) {
                ProductOption::withTrashed()
                    ->where('product_id', $product->id)
                    ->update(['unit' => $product->unit?->value]);

                $product->productOption()->withTrashed()->pluck('id')
                    ->each(fn ($id) => Cache::forget(ProductOption::cacheKey($id)));
            }
        });

        // Orçamentos antigos dependem do produto; arquive em vez de apagar
        static::forceDeleting(function (Product $product) {
            if ($product->isUsedInBudgets()) {
                throw ValidationException::withMessages([
                    'product' => 'Este produto aparece em orçamentos e não pode ser excluído definitivamente. Desative-o ou mantenha na lixeira.',
                ]);
            }
        });
    }

    public static function cacheKey(int|string $id): string
    {
        return 'product_'.$id;
    }

    public function flushCatalogCache(): void
    {
        Cache::forget(self::CATALOG_CACHE_KEY);
        Cache::forget(self::cacheKey($this->id));
        Cache::forget(ProductOption::catalogCacheKey($this->id));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'unit', 'min_quantity', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('catalog');
    }

    /**
     * Variações (traços, acabamentos, modelos) com preço próprio.
     */
    public function productOption(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort')->orderBy('name');
    }

    public function activeOptions(): HasMany
    {
        return $this->productOption()->where('is_active', true);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function budgets(): BelongsToMany
    {
        return $this->belongsToMany(Budget::class, 'budget_product')
            ->withPivot(['quantity', 'price', 'subtotal', 'product_option_id', 'location_id'])
            ->withTimestamps();
    }

    public function isUsedInBudgets(): bool
    {
        return $this->budgetItems()->exists();
    }

    /**
     * Aparece no formulário do site: ativo e com ao menos uma variação ativa.
     */
    public function isSellable(): bool
    {
        return $this->is_active && ! $this->trashed() && $this->activeOptions()->exists();
    }

    /**
     * Menor e maior preço entre as variações ativas.
     *
     * @return array{0: float, 1: float}|null
     */
    public function priceRange(): ?array
    {
        $prices = $this->relationLoaded('productOption')
            ? $this->productOption->where('is_active', true)->pluck('price')
            : $this->activeOptions()->pluck('price');

        if ($prices->isEmpty()) {
            return null;
        }

        return [(float) $prices->min(), (float) $prices->max()];
    }

    public function allowsFractions(): bool
    {
        return $this->unit?->isDecimal() ?? true;
    }
}
