<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationArea extends Model
{
    /** @use HasFactory<\Database\Factories\OperationAreaFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'city',
        'state',
        'postcode_prefix',
        'is_active',
        'shipping_fee',
        'is_base',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'shipping_fee' => 'decimal:2',
        'is_base' => 'boolean',
    ];
}
