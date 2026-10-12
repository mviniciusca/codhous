<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /** Lista de produtos exibida no formulário de orçamento do site */
    public const CATALOG_CACHE_KEY = 'all_products_plucked';

    protected static function booted(): void
    {
        // Limpa o cache do site ao alterar um produto, para preço e disponibilidade refletirem na hora
        $flush = fn (Product $product) => $product->flushCatalogCache();

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);
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

    /**
     * Summary of productOptions
     */
    public function productOption(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    /**
     * Budgets relationship
     */
    public function budgets(): BelongsToMany
    {
        return $this->belongsToMany(Budget::class, 'budget_product')
            ->withPivot(['quantity', 'price', 'subtotal', 'product_option_id', 'location_id'])
            ->withTimestamps();
    }
}
