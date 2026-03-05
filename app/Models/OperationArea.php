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
        'postcode_start',
        'postcode_end',
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
     * Se postcode_start e postcode_end estiverem preenchidos, usa a faixa (ex: Rio 20000-23999).
     * Caso contrário, usa os 2 primeiros dígitos do postcode_prefix (comportamento antigo).
     */
    public static function isCepInOperationArea(string $cep): bool
    {
        $digits = preg_replace('/\D/', '', $cep);
        if (strlen($digits) < 5) {
            return false;
        }
        $prefix5 = substr($digits, 0, 5);

        return static::query()
            ->where('is_active', true)
            ->where(function ($q) use ($prefix5) {
                $q->where(function ($q2) use ($prefix5) {
                    $q2->whereNotNull('postcode_start')
                        ->whereNotNull('postcode_end')
                        ->where('postcode_start', '<=', $prefix5)
                        ->where('postcode_end', '>=', $prefix5);
                })->orWhere(function ($q2) use ($prefix5) {
                    $q2->whereNull('postcode_start')
                        ->whereNull('postcode_end')
                        ->whereRaw('LEFT(postcode_prefix, 2) = ?', [substr($prefix5, 0, 2)]);
                });
            })
            ->exists();
    }

    /**
     * Retorna a área de operação ativa para o CEP, ou null se fora da área.
     */
    public static function findOperationAreaByCep(string $cep): ?self
    {
        $digits = preg_replace('/\D/', '', $cep);
        if (strlen($digits) < 5) {
            return null;
        }
        $prefix5 = substr($digits, 0, 5);

        return static::query()
            ->where('is_active', true)
            ->where(function ($q) use ($prefix5) {
                $q->where(function ($q2) use ($prefix5) {
                    $q2->whereNotNull('postcode_start')
                        ->whereNotNull('postcode_end')
                        ->where('postcode_start', '<=', $prefix5)
                        ->where('postcode_end', '>=', $prefix5);
                })->orWhere(function ($q2) use ($prefix5) {
                    $q2->whereNull('postcode_start')
                        ->whereNull('postcode_end')
                        ->whereRaw('LEFT(postcode_prefix, 2) = ?', [substr($prefix5, 0, 2)]);
                });
            })
            ->first();
    }
}
