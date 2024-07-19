<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
