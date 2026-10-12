<?php

namespace App\Services\VolumeCalculator\Expression;

use RuntimeException;

/** Erro de sintaxe, de unidade ou de avaliação, com mensagem pronta para a tela */
class ExpressionError extends RuntimeException
{
    public static function at(string $message, ?int $position = null): self
    {
        return new self($position === null ? $message : $message.' (posição '.($position + 1).')');
    }
}
