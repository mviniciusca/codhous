<?php

namespace App\Models;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetHistory extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetHistoryFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
