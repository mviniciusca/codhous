<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanySetting extends Model
{
    /** @use HasFactory<\Database\Factories\CompanySettingFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'address' => 'array',
    ];

    /**
     * Summary of setting
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }
}
