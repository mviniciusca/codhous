<?php

namespace App\Models;

use App\Services\VolumeCalculator\FormulaDefinition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

/** Versão imutável de uma fórmula: o que um cálculo salvo usou, para sempre */
class VolumeFormulaVersion extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['volume_formula_id', 'version', 'definition', 'notes', 'created_by'];

    protected $casts = [
        'definition' => 'array',
        'version' => 'integer',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Versões de fórmula são imutáveis; salve uma nova versão.'));
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(VolumeFormula::class, 'volume_formula_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calculations(): HasMany
    {
        return $this->hasMany(VolumeCalculation::class);
    }

    /** Não se chama definition(): o Filament leria a coluna JSON "definition" como relação */
    public function toDefinition(): FormulaDefinition
    {
        return FormulaDefinition::fromArray($this->definition ?? []);
    }

    public function label(): string
    {
        return 'v'.$this->version;
    }
}
