<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Navigation extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * TODO: Doc this
     * @return array
     */
    protected function casts(): array
    {
        return [
            'navigation' => 'array',
            'nav_button' => 'array',
        ];
    }

    /**
     * Summary of setting
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }

}
