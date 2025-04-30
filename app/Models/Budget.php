<?php

namespace App\Models;

use App\Models\BudgetHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Cast
     * @return array
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    public function budgetHistory()
    {
        return $this->hasOne(BudgetHistory::class);
    }

    /**
     * Products relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'budget_product')
            ->withPivot(['quantity', 'price', 'subtotal', 'product_option_id', 'location_id'])
            ->withTimestamps();
    }

    /**
     * Get the PDF files associated with the budget
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pdfs()
    {
        return $this->hasMany(BudgetPdf::class);
    }

    /**
     * Get the latest PDF file for this budget
     *
     * @return BudgetPdf|null
     */
    public function latestPdf()
    {
        return $this->pdfs()->latest()->first();
    }
}
