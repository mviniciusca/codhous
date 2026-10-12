<?php

namespace App\Services\VolumeCalculator\Expression;

/**
 * Analisador das fórmulas de volume. A sintaxe é fechada: números, variáveis
 * autorizadas, + - * / ^, parênteses, a constante pi e as funções de FUNCTIONS.
 * Nada é executado como PHP, JavaScript ou SQL: o texto vira uma árvore de nós
 * (arrays) que só Expression sabe avaliar.
 *
 * Nós: ['num', float] | ['var', nome] | ['neg', nó] | ['bin', op, esq, dir] | ['call', função, [args]]
 */
class Parser
{
    public const MAX_LENGTH = 500;

    public const MAX_DEPTH = 32;

    public const MAX_NODES = 200;

    /** Função => [mínimo, máximo] de argumentos (null = sem limite) */
    public const FUNCTIONS = [
        'sqrt' => [1, 1],
        'abs' => [1, 1],
        'pow' => [2, 2],
        'min' => [2, null],
        'max' => [2, null],
    ];

    public const CONSTANTS = ['pi' => M_PI];

    /** @var array<int, array{type: string, value: string, pos: int}> */
    private array $tokens = [];

    private int $index = 0;

    private int $depth = 0;

    private int $nodes = 0;

    /** @param  array<int, string>  $variables  nomes autorizados */
    public function __construct(private readonly array $variables) {}

    public static function isReserved(string $name): bool
    {
        return array_key_exists(strtolower($name), self::FUNCTIONS) || array_key_exists(strtolower($name), self::CONSTANTS);
    }

    public function parse(string $source): array
    {
        $source = trim($source);

        if ($source === '') {
            throw ExpressionError::at('Informe a fórmula.');
        }

        if (mb_strlen($source) > self::MAX_LENGTH) {
            throw ExpressionError::at('A fórmula passa do limite de '.self::MAX_LENGTH.' caracteres.');
        }

        $this->tokens = $this->tokenize($source);
        $this->index = 0;
        $this->depth = 0;
        $this->nodes = 0;

        $node = $this->expression();

        if ($token = $this->peek()) {
            throw ExpressionError::at($token['value'] === ','
                ? 'Vírgula fora de uma função. Use ponto como separador decimal (ex.: 0.5)'
                : 'Trecho inesperado “'.$token['value'].'”', $token['pos']);
        }

        return $node;
    }

    /** @return array<int, array{type: string, value: string, pos: int}> */
    private function tokenize(string $source): array
    {
        // Símbolos tipográficos aceitos como apelidos dos operadores
        $source = strtr($source, ['×' => '*', '·' => '*', '÷' => '/', 'π' => 'pi', '−' => '-']);
        $tokens = [];
        $length = strlen($source);
        $i = 0;

        while ($i < $length) {
            $char = $source[$i];

            if (ctype_space($char)) {
                $i++;

                continue;
            }

            if (ctype_digit($char) || ($char === '.' && $i + 1 < $length && ctype_digit($source[$i + 1]))) {
                preg_match('/\G(?:\d+\.?\d*|\.\d+)(?:[eE][+-]?\d+)?/', $source, $match, 0, $i);
                $tokens[] = ['type' => 'num', 'value' => $match[0], 'pos' => $i];
                $i += strlen($match[0]);

                continue;
            }

            if (ctype_alpha($char) || $char === '_') {
                preg_match('/\G[A-Za-z_][A-Za-z0-9_]*/', $source, $match, 0, $i);
                $tokens[] = ['type' => 'ident', 'value' => $match[0], 'pos' => $i];
                $i += strlen($match[0]);

                continue;
            }

            if (str_contains('+-*/^(),;', $char)) {
                $tokens[] = ['type' => $char === ';' ? ',' : $char, 'value' => $char, 'pos' => $i];
                $i++;

                continue;
            }

            throw ExpressionError::at('Caractere não permitido “'.mb_substr(substr($source, $i), 0, 1).'”', $i);
        }

        return $tokens;
    }

    private function expression(): array
    {
        $this->enter();
        $node = $this->term();

        while (($token = $this->peek()) && in_array($token['type'], ['+', '-'], true)) {
            $this->index++;
            $node = $this->node(['bin', $token['type'], $node, $this->term()]);
        }

        $this->depth--;

        return $node;
    }

    private function term(): array
    {
        $node = $this->unary();

        while (($token = $this->peek()) && in_array($token['type'], ['*', '/'], true)) {
            $this->index++;
            $node = $this->node(['bin', $token['type'], $node, $this->unary()]);
        }

        return $node;
    }

    private function unary(): array
    {
        $token = $this->peek();

        if ($token && in_array($token['type'], ['+', '-'], true)) {
            $this->index++;
            $this->enter();
            $operand = $this->unary();
            $this->depth--;

            return $token['type'] === '-' ? $this->node(['neg', $operand]) : $operand;
        }

        return $this->power();
    }

    private function power(): array
    {
        $base = $this->primary();

        if (($token = $this->peek()) && $token['type'] === '^') {
            $this->index++;
            $this->enter();
            // Associativo à direita: 2^3^2 = 2^(3^2); -2^2 = -(2^2)
            $exponent = $this->unary();
            $this->depth--;

            return $this->node(['bin', '^', $base, $exponent]);
        }

        return $base;
    }

    private function primary(): array
    {
        $token = $this->peek();

        if (! $token) {
            throw ExpressionError::at('A fórmula termina de forma incompleta.');
        }

        $this->index++;

        if ($token['type'] === 'num') {
            if (! is_finite((float) $token['value'])) {
                throw ExpressionError::at('Número fora do limite', $token['pos']);
            }

            return $this->node(['num', (float) $token['value']]);
        }

        if ($token['type'] === '(') {
            $node = $this->expression();
            $this->expect(')', 'Parêntese aberto sem fechamento', $token['pos']);

            return $node;
        }

        if ($token['type'] === 'ident') {
            $name = $token['value'];
            $lower = strtolower($name);
            $next = $this->peek();

            if ($next && $next['type'] === '(') {
                if (! array_key_exists($lower, self::FUNCTIONS)) {
                    throw ExpressionError::at('Função “'.$name.'” não é permitida', $token['pos']);
                }

                return $this->call($lower, $token['pos']);
            }

            if (array_key_exists($lower, self::FUNCTIONS)) {
                throw ExpressionError::at('A função “'.$lower.'” precisa de parênteses, ex.: '.$lower.'(…)', $token['pos']);
            }

            if (array_key_exists($lower, self::CONSTANTS)) {
                return $this->node(['num', self::CONSTANTS[$lower]]);
            }

            if (! in_array($name, $this->variables, true)) {
                throw ExpressionError::at('Variável “'.$name.'” não existe nos campos desta fórmula', $token['pos']);
            }

            return $this->node(['var', $name]);
        }

        throw ExpressionError::at($token['type'] === ')' ? 'Parêntese fechado sem abertura' : 'Trecho inesperado “'.$token['value'].'”', $token['pos']);
    }

    private function call(string $function, int $position): array
    {
        $this->index++; // (
        $this->enter();
        $args = [];

        if (($this->peek()['type'] ?? null) !== ')') {
            $args[] = $this->expression();

            while (($this->peek()['type'] ?? null) === ',') {
                $this->index++;
                $args[] = $this->expression();
            }
        }

        $this->expect(')', 'Parêntese da função “'.$function.'” sem fechamento', $position);
        $this->depth--;

        [$min, $max] = self::FUNCTIONS[$function];
        $count = count($args);

        if ($count < $min || ($max !== null && $count > $max)) {
            $expected = $max === null ? "pelo menos {$min}" : ($min === $max ? (string) $min : "{$min} a {$max}");

            throw ExpressionError::at("A função “{$function}” recebe {$expected} ".($min === 1 && $max === 1 ? 'argumento' : 'argumentos').", {$count} informado(s)", $position);
        }

        return $this->node(['call', $function, $args]);
    }

    private function expect(string $type, string $message, int $position): void
    {
        if (($this->peek()['type'] ?? null) !== $type) {
            throw ExpressionError::at($message, $position);
        }

        $this->index++;
    }

    private function peek(): ?array
    {
        return $this->tokens[$this->index] ?? null;
    }

    private function enter(): void
    {
        if (++$this->depth > self::MAX_DEPTH) {
            throw ExpressionError::at('A fórmula tem parênteses ou operações aninhadas demais.');
        }
    }

    private function node(array $node): array
    {
        if (++$this->nodes > self::MAX_NODES) {
            throw ExpressionError::at('A fórmula é longa demais.');
        }

        return $node;
    }
}
