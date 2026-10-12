<?php

namespace App\Livewire\Concerns;

use App\Models\VolumeFormula;
use App\Services\VolumeCalculator\CalculationResult;
use App\Services\VolumeCalculator\Expression\ExpressionError;
use App\Services\VolumeCalculator\InvalidInputs;
use App\Services\VolumeCalculator\TruckMinimum;
use App\Services\VolumeCalculator\VolumeCalculator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

/**
 * Estado e cálculo comuns às calculadoras de volume do painel (VolumeCalculatorTool)
 * e do site (Calculator): as mesmas fórmulas ativas, a mesma margem padrão e o
 * mesmo motor (VolumeCalculator). Mudou a fórmula no painel, mudou nos dois.
 */
trait CalculatesVolume
{
    public const MARGIN_OPTIONS = [0, 5, 10, 15, 20];

    public ?int $formulaId = null;

    /** @var array<string, string> variável => valor digitado */
    public array $values = [];

    /** Margem em %, como texto do select */
    public string $margin = '';

    /** Sufixo da margem padrão no select (“padrão” no painel, “recomendado” no site) */
    protected function defaultMarginSuffix(): string
    {
        return ' (padrão)';
    }

    /** @return Collection<int, VolumeFormula> */
    #[Computed]
    public function formulas(): Collection
    {
        return VolumeFormula::active()->ordered()->has('currentVersion')->with('currentVersion')->get();
    }

    #[Computed]
    public function formula(): ?VolumeFormula
    {
        return $this->formulas->firstWhere('id', $this->formulaId);
    }

    public function selectFormula(?int $id): void
    {
        $formula = $this->formulas->firstWhere('id', $id) ?? $this->formulas->first();

        $this->formulaId = $formula?->getKey();
        $this->values = collect($formula?->definition()?->variables ?? [])->mapWithKeys(fn ($variable) => [$variable->key => ''])->all();
        $this->margin = $formula ? $this->formatMargin($formula->definition()->marginPercent) : '';
        $this->resetErrorBag();
        unset($this->formula, $this->calculation);
    }

    public function fillExample(): void
    {
        foreach ($this->formula?->definition()?->variables ?? [] as $variable) {
            if ($variable->example !== null) {
                $this->values[$variable->key] = $this->formatMargin($variable->example);
            }
        }
    }

    /** @return array<int, array{value: string, label: string}> */
    public function marginOptions(): array
    {
        $default = $this->formula?->definition()?->marginPercent ?? 10;

        return collect(self::MARGIN_OPTIONS)
            ->push($default)
            ->unique(fn ($value) => (string) (float) $value)
            ->sort()
            ->map(fn ($value) => [
                'value' => $this->formatMargin($value),
                'label' => $this->formatMargin($value).'%'.((float) $value === (float) $default ? $this->defaultMarginSuffix() : ''),
            ])
            ->values()
            ->all();
    }

    /**
     * Resultado do cálculo com o estado atual. Campos vazios não geram erro
     * enquanto o usuário digita; só os preenchidos com valor inválido.
     *
     * @return array{result: ?CalculationResult, errors: array<string, string>, error: ?string, complete: bool}
     */
    #[Computed]
    public function calculation(): array
    {
        $definition = $this->formula?->definition();
        $state = ['result' => null, 'errors' => [], 'error' => null, 'complete' => false];

        if (! $definition) {
            return $state;
        }

        $state['complete'] = collect($definition->variables)->every(fn ($variable) => trim((string) ($this->values[$variable->key] ?? '')) !== '');

        try {
            $state['result'] = app(VolumeCalculator::class)->calculate($definition, $this->values, $this->marginValue());
        } catch (InvalidInputs $e) {
            $state['errors'] = collect($e->errors)
                ->filter(fn ($message, $key) => ! array_key_exists($key, $this->values) || trim((string) $this->values[$key]) !== '')
                ->all();
        } catch (ExpressionError $e) {
            $state['error'] = $e->getMessage();
        }

        return $state;
    }

    /**
     * Recomendação quando o volume final não completa o caminhão betoneira
     * (regra de entrega da empresa, separada da fórmula).
     */
    #[Computed]
    public function truckAdvice(): ?array
    {
        $result = $this->calculation['result'];

        return $result ? TruckMinimum::advise($result->finalVolume) : null;
    }

    protected function marginValue(): float
    {
        $value = VolumeCalculator::parseNumber($this->margin);

        return $value ?? ($this->formula?->definition()?->marginPercent ?? 0);
    }

    protected function formatMargin(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
