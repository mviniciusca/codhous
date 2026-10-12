<?php

namespace App\Services\VolumeCalculator;

use App\Enums\VolumeRounding;
use App\Services\VolumeCalculator\Expression\Expression;

/**
 * Motor da calculadora de volume. Converte as entradas para metros, avalia a
 * fórmula da versão e aplica, nesta ordem, a margem de segurança e o
 * arredondamento. O volume segue a unidade de medida do concreto dosado em
 * central (m³ de concreto fresco, ABNT NBR 7212); a margem cobre perdas de
 * lançamento, deformação de fôrmas e irregularidades do terreno.
 */
class VolumeCalculator
{
    /**
     * @param  array<string, mixed>  $inputs  variável => valor digitado (aceita vírgula decimal)
     *
     * @throws InvalidInputs
     * @throws Expression\ExpressionError
     */
    public function calculate(FormulaDefinition $definition, array $inputs, ?float $marginPercent = null): CalculationResult
    {
        $marginPercent ??= $definition->marginPercent;

        if ($marginPercent < 0 || $marginPercent > 100) {
            throw new InvalidInputs(['margin' => 'A margem deve ficar entre 0% e 100%.']);
        }

        $values = $this->validate($definition, $inputs);
        $expression = $definition->expression();

        $converted = [];
        $rows = [];
        foreach ($definition->variables as $variable) {
            $converted[$variable->key] = $variable->unit->toBase($values[$variable->key]);
            $rows[] = [
                'key' => $variable->key,
                'label' => $variable->label,
                'value' => $values[$variable->key],
                'unit' => $variable->unit->symbol(),
                'base_value' => $converted[$variable->key],
                'base_unit' => $variable->unit->baseSymbol(),
            ];
        }

        $base = $expression->evaluate($converted);

        if ($base < 0) {
            throw new InvalidInputs(['formula' => 'A fórmula resultou em volume negativo com estas medidas.']);
        }

        $base = round($base, 6);
        $margin = round($base * $marginPercent / 100, 6);
        $withMargin = round($base + $margin, 6);
        $final = $definition->rounding->apply($withMargin, $definition->roundingStep);
        $adjustment = round($final - $withMargin, 6);

        $steps = [[
            'label' => 'Volume base',
            'detail' => $expression->renderWithValues($converted),
            'value' => $base,
        ]];

        if ($marginPercent > 0) {
            $steps[] = [
                'label' => 'Margem de segurança ('.Expression::formatNumber($marginPercent, 2).'%)',
                'detail' => Expression::formatNumber($base).' × '.Expression::formatNumber($marginPercent / 100, 4),
                'value' => $margin,
            ];
        }

        if (abs($adjustment) >= 0.000001 || $definition->rounding !== VolumeRounding::None) {
            $steps[] = [
                'label' => $definition->rounding === VolumeRounding::None
                    ? 'Arredondamento (2 casas)'
                    : 'Arredondamento '.($definition->rounding === VolumeRounding::Up ? 'para cima' : 'ao mais próximo').' (múltiplos de '.Expression::formatNumber($definition->roundingStep, 3).' m³)',
                'detail' => Expression::formatNumber($withMargin).' → '.Expression::formatNumber($final),
                'value' => $adjustment,
            ];
        }

        return new CalculationResult(
            baseVolume: $base,
            marginPercent: $marginPercent,
            marginVolume: $margin,
            volumeWithMargin: $withMargin,
            roundingAdjustment: $adjustment,
            finalVolume: $final,
            inputs: $rows,
            steps: $steps,
            formula: $expression->render(fn (string $key) => $definition->variablesByKey()[$key]->label ?? $key),
        );
    }

    /** Lê número digitado no formato brasileiro ou com ponto (“1.234,5”, “0,10”, “2.5”) */
    public static function parseNumber(mixed $value): ?float
    {
        if (is_int($value) || is_float($value)) {
            return is_finite((float) $value) ? (float) $value : null;
        }

        $value = str_replace(' ', '', trim((string) $value));

        if ($value === '') {
            return null;
        }

        if (str_contains($value, ',')) {
            $value = str_replace(['.', ','], ['', '.'], $value);
        }

        return is_numeric($value) && is_finite((float) $value) ? (float) $value : null;
    }

    /**
     * @return array<string, float>
     *
     * @throws InvalidInputs
     */
    public function validate(FormulaDefinition $definition, array $inputs): array
    {
        $values = [];
        $errors = [];

        foreach ($definition->variables as $variable) {
            $value = self::parseNumber($inputs[$variable->key] ?? null);
            $unit = $variable->unit->symbol();

            if ($value === null) {
                $errors[$variable->key] = 'Informe '.mb_strtolower($variable->label).'.';
            } elseif ($variable->unit->isInteger() && floor($value) != $value) {
                $errors[$variable->key] = $variable->label.' deve ser um número inteiro.';
            } elseif ($variable->min === null && ! $variable->unit->isInteger() && $value <= 0) {
                $errors[$variable->key] = $variable->label.' deve ser maior que zero.';
            } elseif ($value < $variable->minimum()) {
                $errors[$variable->key] = $variable->label.' deve ser no mínimo '.Expression::formatNumber($variable->minimum()).' '.$unit.'.';
            } elseif ($value > $variable->maximum()) {
                $errors[$variable->key] = $variable->label.' deve ser no máximo '.Expression::formatNumber($variable->maximum()).' '.$unit.'.';
            } else {
                $values[$variable->key] = $value;
            }
        }

        if ($errors !== []) {
            throw new InvalidInputs($errors);
        }

        return $values;
    }
}
