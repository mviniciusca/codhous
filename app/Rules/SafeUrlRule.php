<?php

namespace App\Rules;

use App\Models\Alert;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Link exibido no site: aceita https://..., http://..., /pagina, #secao, mailto: e tel:.
 * Recusa javascript:, data: e outros esquemas que executariam código no navegador.
 */
class SafeUrlRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        if (! is_string($value) || Alert::sanitizeUrl($value) === null) {
            $fail('Informe um link válido: https://..., /pagina, #secao, mailto: ou tel:.');
        }
    }
}
