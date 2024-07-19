<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
}
