<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Layout extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }
    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }
}
