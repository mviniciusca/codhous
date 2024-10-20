<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';
    protected $guarded = [];
    protected $casts = [
        'properties' => 'array',
    ];
}
