<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Partner extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }
    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

}
