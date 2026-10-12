<?php

namespace App\Models;

use App\Services\VolumeCalculator\CalculationResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Cálculo salvo no histórico. Guarda as entradas, a memória de cálculo e a
 * versão exata da fórmula, então o resultado pode ser conferido mesmo depois
 * que a fórmula mudar.
 */
class VolumeCalculation extends Model
{
    protected $fillable = [
        'volume_formula_version_id', 'user_id', 'budget_id', 'reference',
        'inputs', 'result', 'margin_percent', 'base_volume', 'final_volume',
    ];

    protected $casts = [
        'inputs' => 'array',
        'result' => 'array',
        'margin_percent' => 'float',
        'base_volume' => 'float',
        'final_volume' => 'float',
    ];

    public function formulaVersion(): BelongsTo
    {
        return $this->belongsTo(VolumeFormulaVersion::class, 'volume_formula_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function calculationResult(): CalculationResult
    {
        return CalculationResult::fromArray($this->result);
    }

    public static function record(VolumeFormulaVersion $version, array $inputs, CalculationResult $result, ?User $user, ?string $reference = null): self
    {
        return self::create([
            'volume_formula_version_id' => $version->getKey(),
            'user_id' => $user?->getKey(),
            'reference' => filled($reference) ? mb_substr(trim($reference), 0, 255) : null,
            'inputs' => $inputs,
            'result' => $result->toArray(),
            'margin_percent' => $result->marginPercent,
            'base_volume' => $result->baseVolume,
            'final_volume' => $result->finalVolume,
        ]);
    }
}
