<?php

namespace App\Models;

use App\Services\VolumeCalculator\FormulaDefinition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Fórmula da calculadora de volume. A regra de cálculo mora nas versões: cada
 * alteração cria uma versão nova e as antigas ficam intactas, para que cálculos
 * do histórico continuem auditáveis. A instalação é de uma empresa só, então as
 * fórmulas daqui são as regras dessa empresa.
 */
class VolumeFormula extends Model
{
    public const SHAPES = [
        'slab' => 'Laje / piso',
        'cylinder' => 'Cilíndrica',
        'beam' => 'Retangular',
        'custom' => 'Personalizada',
    ];

    protected $fillable = ['slug', 'name', 'description', 'shape', 'is_system', 'is_active', 'sort'];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $formula) {
            if (blank($formula->slug)) {
                $base = Str::slug($formula->name) ?: 'formula';
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $formula->slug = $slug;
            }
        });

        static::deleting(function (self $formula) {
            if (! $formula->canBeDeleted()) {
                throw new InvalidArgumentException('Fórmulas do sistema ou já usadas em cálculos não podem ser excluídas; desative-as.');
            }
        });
    }

    public function versions(): HasMany
    {
        return $this->hasMany(VolumeFormulaVersion::class)->orderByDesc('version');
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(VolumeFormulaVersion::class)->latestOfMany('version');
    }

    public function calculations(): HasManyThrough
    {
        return $this->hasManyThrough(VolumeCalculation::class, VolumeFormulaVersion::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('name');
    }

    public function definition(): ?FormulaDefinition
    {
        return $this->currentVersion?->toDefinition();
    }

    /**
     * Grava a regra como nova versão. Não cria versão se nada mudou.
     *
     * @throws InvalidArgumentException quando a regra é inválida
     */
    public function saveVersion(FormulaDefinition $definition, ?User $author = null, ?string $notes = null): ?VolumeFormulaVersion
    {
        if ($errors = $definition->errors()) {
            throw new InvalidArgumentException($errors[0]);
        }

        $current = $this->currentVersion()->first();

        // Compara serializado: o JSON do banco volta 10.0 como 10
        if ($current && json_encode($current->definition) === json_encode($definition->toArray())) {
            return null;
        }

        $version = $this->versions()->create([
            'version' => ($current?->version ?? 0) + 1,
            'definition' => $definition->toArray(),
            'notes' => filled($notes) ? Str::limit(trim($notes), 250) : null,
            'created_by' => $author?->getKey(),
        ]);

        $this->unsetRelation('currentVersion');

        return $version;
    }

    public function canBeDeleted(): bool
    {
        return ! $this->is_system && ! $this->calculations()->exists();
    }
}
