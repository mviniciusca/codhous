<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function casts(): array
    {
        return [
            'module' => 'array'
        ];
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
