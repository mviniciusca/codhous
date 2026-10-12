<?php

namespace App\Filament\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Converte o estado de um formulário em dados serializáveis para os live previews
 * (uploads temporários viram URL, enums viram valor, objetos desconhecidos são descartados).
 */
trait NormalizesPreviewState
{
    protected function normalizePreviewValue(mixed $value): mixed
    {
        if ($value instanceof TemporaryUploadedFile) {
            try {
                return $value->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->normalizePreviewValue($item), $value);
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if ($value instanceof Arrayable) {
            return $this->normalizePreviewValue($value->toArray());
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        if (is_object($value)) {
            return null;
        }

        return $value;
    }
}
