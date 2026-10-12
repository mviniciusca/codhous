<?php

use App\Enums\VolumeRounding;
use App\Services\VolumeCalculator\Expression\Expression;
use App\Services\VolumeCalculator\Expression\ExpressionError;
use App\Services\VolumeCalculator\FormulaDefinition;
use App\Services\VolumeCalculator\InvalidInputs;
use App\Services\VolumeCalculator\VolumeCalculator;

function slab(array $overrides = []): FormulaDefinition
{
    return FormulaDefinition::fromArray(array_merge([
        'expression' => 'largura * comprimento * espessura',
        'variables' => [
            ['key' => 'largura', 'label' => 'Largura', 'unit' => 'm'],
            ['key' => 'comprimento', 'label' => 'Comprimento', 'unit' => 'm'],
            ['key' => 'espessura', 'label' => 'Espessura', 'unit' => 'cm'],
        ],
        'margin_percent' => 10,
    ], $overrides));
}

test('respeita precedência, associatividade e funções permitidas', function (string $source, float $expected) {
    expect(Expression::parse($source, ['a', 'b'])->evaluate(['a' => 2, 'b' => 3]))->toEqualWithDelta($expected, 1e-9);
})->with([
    ['1 + 2 * 3', 7],
    ['(1 + 2) * 3', 9],
    ['2 ^ 3 ^ 2', 512],
    ['-2 ^ 2', -4],
    ['(-2) ^ 2', 4],
    ['a * b - b / a', 4.5],
    ['10 - 4 - 3', 3],
    ['sqrt(a * 8)', 4],
    ['pow(b, 2) + abs(-a)', 11],
    ['min(a, b; 1) + max(a, b)', 4],
    ['pi * (a / 2)^2', M_PI],
    ['π × a ÷ b', M_PI * 2 / 3],
    ['1.5e2 + .5', 150.5],
]);

test('recusa sintaxe fora da gramática e qualquer código', function (string $source, string $message) {
    expect(fn () => Expression::parse($source, ['a']))->toThrow(ExpressionError::class, $message);
})->with([
    ['', 'Informe a fórmula'],
    ['a +', 'incompleta'],
    ['(a + 1', 'Parêntese aberto'],
    ['a + 1)', 'Trecho inesperado'],
    ['b * 2', 'Variável “b” não existe'],
    ['system(a)', 'Função “system” não é permitida'],
    ['exec(a)', 'Função “exec” não é permitida'],
    ['exec("ls")', 'Caractere não permitido'],
    ['a; drop table users', 'Trecho inesperado'],
    ['$a * 2', 'Caractere não permitido “$”'],
    ['a`ls`', 'Caractere não permitido'],
    ['0,5 * a', 'Use ponto como separador decimal'],
    ['sqrt', 'precisa de parênteses'],
    ['sqrt(a, a)', 'recebe 1 argumento'],
    ['min(a)', 'pelo menos 2'],
]);

test('limita tamanho e aninhamento da expressão', function () {
    expect(fn () => Expression::parse(str_repeat('(', 40).'a'.str_repeat(')', 40), ['a']))->toThrow(ExpressionError::class, 'aninhadas demais')
        ->and(fn () => Expression::parse(implode('+', array_fill(0, 150, 'a')), ['a']))->toThrow(ExpressionError::class, 'longa demais')
        ->and(fn () => Expression::parse(str_repeat('1', 501), []))->toThrow(ExpressionError::class, 'limite de 500');
});

test('erros de avaliação viram mensagem, não exceção do PHP', function () {
    expect(fn () => Expression::parse('a / 0', ['a'])->evaluate(['a' => 1]))->toThrow(ExpressionError::class, 'Divisão por zero')
        ->and(fn () => Expression::parse('sqrt(a)', ['a'])->evaluate(['a' => -1]))->toThrow(ExpressionError::class, 'negativo')
        ->and(fn () => Expression::parse('10 ^ 400', [])->evaluate([]))->toThrow(ExpressionError::class, 'não é um número válido');
});

test('a verificação de unidades exige resultado em m³', function () {
    $dims = ['l' => 1, 'c' => 1, 'e' => 1, 'area' => 2, 'n' => 0];

    expect(Expression::parse('l * c * e * n', array_keys($dims))->dimension($dims))->toEqual(3.0)
        ->and(Expression::parse('area * e', array_keys($dims))->dimension($dims))->toEqual(3.0)
        ->and(Expression::parse('pi * (l / 2)^2 * c', array_keys($dims))->dimension($dims))->toEqual(3.0)
        ->and(Expression::parse('sqrt(area) * area', array_keys($dims))->dimension($dims))->toEqual(3.0)
        ->and(fn () => Expression::parse('l * c', array_keys($dims))->assertVolume($dims))->toThrow(ExpressionError::class, 'resulta em m²')
        ->and(fn () => Expression::parse('(l + area) * e', array_keys($dims))->dimension($dims))->toThrow(ExpressionError::class, 'somar grandezas diferentes (m e m²)')
        ->and(fn () => Expression::parse('l ^ n', array_keys($dims))->dimension($dims))->toThrow(ExpressionError::class, 'número fixo');
});

test('a memória de cálculo reescreve a fórmula com os valores', function () {
    $expression = Expression::parse('pi*(d/2)^2*h - (a - b)', ['d', 'h', 'a', 'b']);

    expect($expression->renderWithValues(['d' => 0.3, 'h' => 6, 'a' => 1, 'b' => 0.5]))->toBe('π × (0,3 ÷ 2)^2 × 6 − (1 − 0,5)')
        ->and($expression->render(fn ($name) => strtoupper($name)))->toBe('π × (D ÷ 2)^2 × H − (A − B)');
});

test('calcula laje com conversão de cm, margem e memória de cálculo', function () {
    $result = (new VolumeCalculator)->calculate(slab(), ['largura' => '5', 'comprimento' => '8', 'espessura' => '10']);

    expect($result->baseVolume)->toEqualWithDelta(4.0, 1e-9)
        ->and($result->marginVolume)->toEqualWithDelta(0.4, 1e-9)
        ->and($result->finalVolume)->toBe(4.4)
        ->and($result->inputs[2])->toMatchArray(['value' => 10.0, 'unit' => 'cm', 'base_value' => 0.1, 'base_unit' => 'm'])
        ->and($result->steps[0])->toMatchArray(['label' => 'Volume base', 'detail' => '5 × 8 × 0,1'])
        ->and($result->steps[1]['label'])->toBe('Margem de segurança (10%)')
        ->and($result->formula)->toBe('Largura × Comprimento × Espessura');
});

test('aceita vírgula decimal e margem escolhida no cálculo', function () {
    $result = (new VolumeCalculator)->calculate(slab(), ['largura' => '2,5', 'comprimento' => '4', 'espessura' => '12,5'], 5);

    expect($result->baseVolume)->toEqualWithDelta(1.25, 1e-9)
        ->and($result->finalVolume)->toBe(1.31) // 1,3125 → 2 casas
        ->and($result->marginPercent)->toBe(5.0);
});

test('arredonda para cima em múltiplos do passo sem ruído de ponto flutuante', function () {
    $definition = slab(['rounding' => 'up', 'rounding_step' => 0.5]);
    $calculator = new VolumeCalculator;

    expect($calculator->calculate($definition, ['largura' => 5, 'comprimento' => 8, 'espessura' => 10])->finalVolume)->toBe(4.5)
        ->and($calculator->calculate($definition, ['largura' => 5, 'comprimento' => 8, 'espessura' => 10], 25)->finalVolume)->toBe(5.0)
        ->and(VolumeRounding::Up->apply(0.1 + 0.2, 0.1))->toBe(0.3)
        ->and(VolumeRounding::Nearest->apply(4.24, 0.5))->toBe(4.0)
        ->and(VolumeRounding::None->apply(4.4049, 0))->toBe(4.4);
});

test('pilar cilíndrico pelo diâmetro, com quantidade inteira', function () {
    $definition = FormulaDefinition::fromArray([
        'expression' => 'pi * (diametro / 2)^2 * altura * quantidade',
        'variables' => [
            ['key' => 'diametro', 'label' => 'Diâmetro', 'unit' => 'cm'],
            ['key' => 'altura', 'label' => 'Altura', 'unit' => 'm'],
            ['key' => 'quantidade', 'label' => 'Quantidade', 'unit' => 'un'],
        ],
        'margin_percent' => 0,
    ]);

    $result = (new VolumeCalculator)->calculate($definition, ['diametro' => 30, 'altura' => 6, 'quantidade' => 10]);

    expect($result->baseVolume)->toEqualWithDelta(M_PI * 0.15 ** 2 * 60, 1e-6)
        ->and($result->finalVolume)->toBe(4.24)
        ->and(fn () => (new VolumeCalculator)->calculate($definition, ['diametro' => 30, 'altura' => 6, 'quantidade' => 1.5]))
        ->toThrow(InvalidInputs::class, 'número inteiro');
});

test('valida cada entrada com mensagem por campo', function () {
    try {
        (new VolumeCalculator)->calculate(slab(), ['largura' => '', 'comprimento' => '-1', 'espessura' => 'abc']);
        $this->fail('deveria falhar');
    } catch (InvalidInputs $e) {
        expect($e->errors)->toBe([
            'largura' => 'Informe largura.',
            'comprimento' => 'Comprimento deve ser maior que zero.',
            'espessura' => 'Informe espessura.',
        ]);
    }

    expect(fn () => (new VolumeCalculator)->calculate(slab(), ['largura' => 1, 'comprimento' => 1, 'espessura' => 1], 150))
        ->toThrow(InvalidInputs::class, 'entre 0% e 100%')
        ->and(fn () => (new VolumeCalculator)->calculate(slab(), ['largura' => 200000, 'comprimento' => 1, 'espessura' => 1]))
        ->toThrow(InvalidInputs::class, 'no máximo 100.000 m');
});

test('a definição aponta campos inválidos, sem uso e unidades erradas', function () {
    expect(slab()->errors())->toBe([])
        ->and(slab(['expression' => 'largura * comprimento'])->errors())->toBe(['A fórmula precisa resultar em volume (m³), mas resulta em m². Confira as unidades dos campos.'])
        ->and(slab(['expression' => 'largura * comprimento * espessura', 'variables' => [
            ['key' => 'largura', 'unit' => 'm'], ['key' => 'comprimento', 'unit' => 'm'], ['key' => 'espessura', 'unit' => 'cm'], ['key' => 'extra', 'unit' => 'un'],
        ]])->errors())->toBe(['Campo sem uso na fórmula: extra.'])
        ->and(slab(['variables' => [['key' => 'Largura', 'unit' => 'm'], ['key' => 'pi', 'unit' => 'm']]])->errors())
        ->toContain('“pi” é um nome reservado da fórmula.')
        ->and(slab(['rounding' => 'up', 'rounding_step' => 0])->errors())->toContain('O passo de arredondamento deve ser maior que 0 e até 10 m³.');
});

test('uma definição sobrevive ida e volta pelo array salvo', function () {
    $definition = slab(['rounding' => 'up', 'rounding_step' => 0.25]);

    expect(FormulaDefinition::fromArray(json_decode(json_encode($definition->toArray()), true))->toArray())->toBe($definition->toArray());
});
