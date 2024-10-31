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
}
