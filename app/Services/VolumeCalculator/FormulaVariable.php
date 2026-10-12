<?php

namespace App\Services\VolumeCalculator;

use App\Enums\MeasureUnit;

/** Campo de entrada de uma fórmula: a variável, o rótulo e a unidade em que é digitado */
final class FormulaVariable
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly MeasureUnit $unit,
        public readonly ?float $example = null,
        public readonly ?float $min = null,
        public readonly ?float $max = null,
        public readonly ?string $help = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $number = fn ($value) => is_numeric($value) ? (float) $value : null;

        return new self(
            key: trim((string) ($data['key'] ?? '')),
            label: trim((string) ($data['label'] ?? '')) ?: (string) ($data['key'] ?? ''),
            unit: MeasureUnit::tryFrom((string) ($data['unit'] ?? '')) ?? MeasureUnit::M,
            example: $number($data['example'] ?? null),
            min: $number($data['min'] ?? null),
            max: $number($data['max'] ?? null),
            help: filled($data['help'] ?? null) ? trim($data['help']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'unit' => $this->unit->value,
            'example' => $this->example,
            'min' => $this->min,
            'max' => $this->max,
            'help' => $this->help,
        ];
    }

    /** Menor valor aceito: o configurado ou, sem configuração, maior que zero (1 para quantidades) */
    public function minimum(): float
    {
        return $this->min ?? ($this->unit->isInteger() ? 1 : 0);
    }

    public function maximum(): float
    {
        return $this->max ?? 100000;
    }

    public function step(): string
    {
        return match ($this->unit) {
            MeasureUnit::UNIT, MeasureUnit::MM => '1',
            MeasureUnit::CM => '0.5',
            default => '0.01',
        };
    }
}
