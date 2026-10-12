<?php

namespace App\Services\VolumeCalculator\Expression;

use App\Enums\MeasureUnit;

/**
 * Fórmula já analisada: avalia com valores em unidade base, confere a unidade
 * do resultado (análise dimensional em potências de metro) e se escreve de volta
 * para a memória de cálculo, com nomes ou com os números substituídos.
 */
class Expression
{
    private const EPSILON = 1e-9;

    /** @param  array<int, string>  $variables */
    private function __construct(
        public readonly string $source,
        private readonly array $tree,
        private readonly array $variables,
    ) {}

    /** @param  array<int, string>  $variables  nomes autorizados (as chaves dos campos) */
    public static function parse(string $source, array $variables): self
    {
        return new self(trim($source), (new Parser($variables))->parse($source), $variables);
    }

    /**
     * Valores já convertidos para a unidade base.
     *
     * @param  array<string, float>  $values
     */
    public function evaluate(array $values): float
    {
        $result = $this->evaluateNode($this->tree, $values);

        if (! is_finite($result)) {
            throw ExpressionError::at('O resultado da fórmula não é um número válido.');
        }

        return $result;
    }

    /**
     * Potência de metro do resultado (m³ = 3). Falha em somas de grandezas
     * diferentes, como “largura + area”.
     *
     * @param  array<string, int|float>  $dimensions  variável => potência de metro
     */
    public function dimension(array $dimensions): float
    {
        return $this->dimensionOf($this->tree, $dimensions);
    }

    /**
     * Confere que a fórmula resulta em volume.
     *
     * @param  array<string, int|float>  $dimensions
     */
    public function assertVolume(array $dimensions): void
    {
        $dimension = $this->dimension($dimensions);

        if (abs($dimension - 3) > self::EPSILON) {
            throw ExpressionError::at('A fórmula precisa resultar em volume (m³), mas resulta em '.MeasureUnit::dimensionLabel($dimension).'. Confira as unidades dos campos.');
        }
    }

    /** @return array<int, string> variáveis usadas, na ordem em que aparecem */
    public function usedVariables(): array
    {
        $found = [];
        $walk = function (array $node) use (&$walk, &$found) {
            match ($node[0]) {
                'var' => $found[$node[1]] = true,
                'neg' => $walk($node[1]),
                'bin' => [$walk($node[2]), $walk($node[3])],
                'call' => array_map($walk, $node[2]),
                default => null,
            };
        };
        $walk($this->tree);

        return array_keys($found);
    }

    /**
     * Texto da fórmula para a memória de cálculo. $variable decide como cada
     * variável aparece (nome, rótulo ou valor).
     *
     * @param  callable(string): string  $variable
     */
    public function render(callable $variable): string
    {
        return $this->renderNode($this->tree, $variable, 0);
    }

    /** @param  array<string, float>  $values */
    public function renderWithValues(array $values): string
    {
        return $this->render(fn (string $name) => self::formatNumber($values[$name] ?? 0));
    }

    public static function formatNumber(float $value, int $maxDecimals = 4): string
    {
        if (abs($value - M_PI) < self::EPSILON) {
            return 'π';
        }

        $formatted = number_format($value, $maxDecimals, ',', '.');

        return str_contains($formatted, ',') ? rtrim(rtrim($formatted, '0'), ',') : $formatted;
    }

    private function evaluateNode(array $node, array $values): float
    {
        switch ($node[0]) {
            case 'num':
                return $node[1];

            case 'var':
                if (! array_key_exists($node[1], $values)) {
                    throw ExpressionError::at('Falta o valor de “'.$node[1].'”.');
                }

                return (float) $values[$node[1]];

            case 'neg':
                return -$this->evaluateNode($node[1], $values);

            case 'bin':
                $left = $this->evaluateNode($node[2], $values);
                $right = $this->evaluateNode($node[3], $values);

                return match ($node[1]) {
                    '+' => $left + $right,
                    '-' => $left - $right,
                    '*' => $left * $right,
                    '/' => abs($right) < 1e-12 ? throw ExpressionError::at('Divisão por zero na fórmula.') : $left / $right,
                    '^' => $this->power($left, $right),
                };

            case 'call':
                $args = array_map(fn (array $arg) => $this->evaluateNode($arg, $values), $node[2]);

                return match ($node[1]) {
                    'sqrt' => $args[0] < 0 ? throw ExpressionError::at('Raiz quadrada de número negativo.') : sqrt($args[0]),
                    'abs' => abs($args[0]),
                    'pow' => $this->power($args[0], $args[1]),
                    'min' => min($args),
                    'max' => max($args),
                };
        }

        throw ExpressionError::at('Fórmula inválida.');
    }

    private function power(float $base, float $exponent): float
    {
        if ($base < 0 && floor($exponent) != $exponent) {
            throw ExpressionError::at('Potência fracionária de número negativo.');
        }

        if (abs($base) < 1e-12 && $exponent < 0) {
            throw ExpressionError::at('Divisão por zero na fórmula.');
        }

        return $base ** $exponent;
    }

    private function dimensionOf(array $node, array $dimensions): float
    {
        switch ($node[0]) {
            case 'num':
                return 0;

            case 'var':
                return (float) ($dimensions[$node[1]] ?? 0);

            case 'neg':
                return $this->dimensionOf($node[1], $dimensions);

            case 'bin':
                $left = $this->dimensionOf($node[2], $dimensions);

                if ($node[1] === '^') {
                    return $left * $this->constantExponent($node[3], $dimensions);
                }

                $right = $this->dimensionOf($node[3], $dimensions);

                return match ($node[1]) {
                    '+', '-' => $this->same($left, $right, $node[1] === '+' ? 'somar' : 'subtrair'),
                    '*' => $left + $right,
                    '/' => $left - $right,
                };

            case 'call':
                $args = $node[2];

                return match ($node[1]) {
                    'sqrt' => $this->dimensionOf($args[0], $dimensions) / 2,
                    'abs' => $this->dimensionOf($args[0], $dimensions),
                    'pow' => $this->dimensionOf($args[0], $dimensions) * $this->constantExponent($args[1], $dimensions),
                    'min', 'max' => array_reduce(
                        array_slice($args, 1),
                        fn (float $carry, array $arg) => $this->same($carry, $this->dimensionOf($arg, $dimensions), 'comparar em '.$node[1]),
                        $this->dimensionOf($args[0], $dimensions),
                    ),
                };
        }

        throw ExpressionError::at('Fórmula inválida.');
    }

    /** O expoente precisa ser um número fixo para a unidade do resultado ser conhecida */
    private function constantExponent(array $node, array $dimensions): float
    {
        if ((new self('', $node, $this->variables))->usedVariables() !== []) {
            throw ExpressionError::at('O expoente de uma potência precisa ser um número fixo, sem variáveis.');
        }

        if (abs($this->dimensionOf($node, $dimensions)) > self::EPSILON) {
            throw ExpressionError::at('O expoente de uma potência não pode ter unidade.');
        }

        return $this->evaluateNode($node, []);
    }

    private function same(float $left, float $right, string $verb): float
    {
        if (abs($left - $right) > self::EPSILON) {
            throw ExpressionError::at('Não é possível '.$verb.' grandezas diferentes ('.MeasureUnit::dimensionLabel($left).' e '.MeasureUnit::dimensionLabel($right).').');
        }

        return $left;
    }

    private const PRECEDENCE = ['+' => 1, '-' => 1, '*' => 2, '/' => 2, 'neg' => 3, '^' => 4];

    private function renderNode(array $node, callable $variable, int $parent): string
    {
        switch ($node[0]) {
            case 'num':
                return self::formatNumber($node[1]);

            case 'var':
                return $variable($node[1]);

            case 'neg':
                $text = '−'.$this->renderNode($node[1], $variable, self::PRECEDENCE['neg']);

                return $parent > self::PRECEDENCE['neg'] ? "({$text})" : $text;

            case 'bin':
                $own = self::PRECEDENCE[$node[1]];
                // Lado direito de - e / (e esquerdo de ^) precisa de parênteses no mesmo nível
                $left = $this->renderNode($node[2], $variable, $node[1] === '^' ? $own + 1 : $own);
                $right = $this->renderNode($node[3], $variable, in_array($node[1], ['-', '/'], true) ? $own + 1 : $own);
                $symbol = ['+' => ' + ', '-' => ' − ', '*' => ' × ', '/' => ' ÷ ', '^' => '^'][$node[1]];
                $text = $left.$symbol.$right;

                return $own < $parent ? "({$text})" : $text;

            case 'call':
                $args = array_map(fn (array $arg) => $this->renderNode($arg, $variable, 0), $node[2]);

                return ($node[1] === 'sqrt' ? '√' : $node[1]).'('.implode('; ', $args).')';
        }

        return '';
    }
}
