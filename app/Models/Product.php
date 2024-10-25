<?php

namespace App\Models;
use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    /**
     * Summary of productOptions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productOption(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

}
