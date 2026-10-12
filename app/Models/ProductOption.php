<?php

namespace App\Models;

use App\Enums\ProductUnit;
use Database\Factories\ProductOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ProductOption extends Model
{
    /** @use HasFactory<ProductOptionFactory> */
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
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

    protected $casts = [
        'price' => 'decimal:2',
        'unit' => ProductUnit::class,
    ];

    /**
     * Summary of product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
