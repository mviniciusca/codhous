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

    /**
     * Verifica se um CEP (8 dígitos) está na área de operação.
     * Uma cidade tem um único postcode_prefix (ex: 25000 = Duque de Caxias).
     * A comparação usa os 2 primeiros dígitos do CEP: 25xxx cobre Duque de Caxias.
     */
    public static function isCepInOperationArea(string $cep): bool
    {
        $digits = preg_replace('/\D/', '', $cep);
        if (strlen($digits) < 2) {
            return false;
        }
        $prefix2 = substr($digits, 0, 2);

        return static::query()
            ->where('is_active', true)
            ->whereRaw('LEFT(postcode_prefix, 2) = ?', [$prefix2])
            ->exists();
    }

    /**
     * Retorna a área de operação ativa para o CEP, ou null se fora da área.
     */
    public static function findOperationAreaByCep(string $cep): ?self
    {
        $digits = preg_replace('/\D/', '', $cep);
        if (strlen($digits) < 2) {
            return null;
        }
        $prefix2 = substr($digits, 0, 2);

        return static::query()
            ->where('is_active', true)
            ->whereRaw('LEFT(postcode_prefix, 2) = ?', [$prefix2])
            ->first();
    }
}
