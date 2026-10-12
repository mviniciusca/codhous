<?php

namespace App\Services\VolumeCalculator;

use RuntimeException;

/** Entradas inválidas da calculadora, com a mensagem de cada campo */
class InvalidInputs extends RuntimeException
{
    /** @param  array<string, string>  $errors  variável => mensagem */
    public function __construct(public readonly array $errors)
    {
        parent::__construct(implode(' ', $errors));
    }
}
