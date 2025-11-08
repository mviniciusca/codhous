<?php

namespace App\Models;

use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Summary of productOptions
     * @return HasMany
     */
    public function productOption(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    /**
     * Budgets relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function budgets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Budget::class, 'budget_product')
            ->withPivot(['quantity', 'price', 'subtotal', 'product_option_id', 'location_id'])
            ->withTimestamps();
    }
}
