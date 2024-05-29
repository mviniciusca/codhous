<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contact extends Model
{
    use HasFactory;

    /**
     *  TODO: Doc this
     * Summary of setting
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }
}
