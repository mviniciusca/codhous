<?php

namespace App\Services\VolumeCalculator;

use App\Enums\VolumeRounding;
use App\Services\VolumeCalculator\Expression\Expression;
use App\Services\VolumeCalculator\Expression\ExpressionError;
use App\Services\VolumeCalculator\Expression\Parser;

/**
 * Regra de cálculo de uma versão de fórmula: a expressão geométrica, os campos
 * de entrada e os ajustes (margem e arredondamento). É o mesmo array guardado em
 * volume_formula_versions, então uma versão antiga recalcula exatamente igual.
 * Preço, frete e demais regras comerciais ficam fora daqui de propósito.
 */
final class FormulaDefinition
{
    public const MAX_VARIABLES = 12;

    private ?Expression $compiled = null;

    /** @param  array<int, FormulaVariable>  $variables */
    public function __construct(
        public readonly string $expression,
        public readonly array $variables,
        public readonly float $marginPercent = 10,
        public readonly VolumeRounding $rounding = VolumeRounding::None,
        public readonly float $roundingStep = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            expression: trim((string) ($data['expression'] ?? '')),
            variables: array_values(array_map(
                fn ($variable) => $variable instanceof FormulaVariable ? $variable : FormulaVariable::fromArray((array) $variable),
                (array) ($data['variables'] ?? []),
            )),
            marginPercent: (float) ($data['margin_percent'] ?? 0),
            rounding: ($data['rounding'] ?? null) instanceof VolumeRounding ? $data['rounding'] : (VolumeRounding::tryFrom((string) ($data['rounding'] ?? '')) ?? VolumeRounding::None),
            roundingStep: (float) ($data['rounding_step'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'expression' => $this->expression,
            'variables' => array_map(fn (FormulaVariable $variable) => $variable->toArray(), $this->variables),
            'margin_percent' => round($this->marginPercent, 2),
            'rounding' => $this->rounding->value,
            'rounding_step' => $this->rounding === VolumeRounding::None ? 0 : round($this->roundingStep, 3),
        ];
    }

    /** @return array<string, FormulaVariable> */
    public function variablesByKey(): array
    {
        return collect($this->variables)->keyBy('key')->all();
    }

    public function expression(): Expression
    {
        return $this->compiled ??= Expression::parse($this->expression, array_column($this->variables, 'key'));
    }

    /**
     * Confere campos, sintaxe e unidades. Retorna a lista de problemas (vazia = ok).
     *
     * @return array<int, string>
     */
    public function errors(): array
    {
        $errors = [];
        $keys = [];

        if ($this->variables === []) {
            $errors[] = 'Adicione pelo menos um campo de entrada.';
        }

        if (count($this->variables) > self::MAX_VARIABLES) {
            $errors[] = 'Use no máximo '.self::MAX_VARIABLES.' campos.';
        }

        foreach ($this->variables as $variable) {
            if (! preg_match('/^[a-z][a-z0-9_]{0,31}$/', $variable->key)) {
                $errors[] = 'A variável “'.$variable->key.'” deve começar com letra minúscula e ter só letras minúsculas, números e _ (até 32 caracteres).';
            } elseif (Parser::isReserved($variable->key)) {
                $errors[] = '“'.$variable->key.'” é um nome reservado da fórmula.';
            }

            if (isset($keys[$variable->key])) {
                $errors[] = 'A variável “'.$variable->key.'” está repetida.';
            }
            $keys[$variable->key] = true;

            if ($variable->min !== null && $variable->max !== null && $variable->min > $variable->max) {
                $errors[] = 'O mínimo de “'.$variable->label.'” é maior que o máximo.';
            }
        }

        if ($this->marginPercent < 0 || $this->marginPercent > 100) {
            $errors[] = 'A margem de segurança deve ficar entre 0% e 100%.';
        }

        if ($this->rounding !== VolumeRounding::None && ($this->roundingStep <= 0 || $this->roundingStep > 10)) {
            $errors[] = 'O passo de arredondamento deve ser maior que 0 e até 10 m³.';
        }

        if ($errors !== []) {
            return $errors;
        }

        try {
            $expression = $this->expression();
            $expression->assertVolume(collect($this->variables)->mapWithKeys(fn (FormulaVariable $v) => [$v->key => $v->unit->dimension()])->all());
        } catch (ExpressionError $error) {
            return [$error->getMessage()];
        }

        $unused = array_diff(array_keys($keys), $expression->usedVariables());

        if ($unused !== []) {
            $errors[] = 'Campo sem uso na fórmula: '.implode(', ', $unused).'.';
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return $this->errors() === [];
    }
}
