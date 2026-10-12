<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

/**
 * Seletor de cor por amostras. O valor de cada opção é a cor em "R G B"
 * (o mesmo formato que o site usa em --primary-color); o rótulo vira tooltip.
 */
class ColorSwatches extends Field
{
    protected string $view = 'filament.forms.components.color-swatches';

    /** @var array<string, string> | Closure */
    protected array|Closure $options = [];

    /**
     * @param  array<string, string> | Closure  $options  valor "R G B" => rótulo
     */
    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getOptions(): array
    {
        return $this->evaluate($this->options);
    }
}
