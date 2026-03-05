<?php

namespace App\Rules;

use App\Services\OperationAreaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CepInOperationAreaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }
        $clean = preg_replace('/\D/', '', $value);
        if (strlen($clean) < 8) {
            return;
        }
        if (! OperationAreaService::isCepInOperationArea($value)) {
            $fail(__('Este CEP está fora da nossa área de atendimento. No momento atendemos apenas a região do Rio de Janeiro e Grande Rio.'));
        }
    }
}
