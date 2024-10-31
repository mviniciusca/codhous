<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetHistory extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetHistoryFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
    }

    public function budget()
    {
    }
}
