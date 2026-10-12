<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Wizard;

/**
 * Layout do construtor de seções: biblioteca (SectionTypePicker) à esquerda,
 * live preview no centro e inspetor (Wizard) à direita.
 */
class SectionBuilder extends Component
{
    protected string $view = 'filament.section-builder.layout';

    /**
     * @param  array<Component>  $schema
     */
    final public function __construct(array $schema = [])
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component>  $schema
     */
    public static function make(array $schema = []): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }

    /**
     * Coluna em que cada filho é exibido.
     */
    public static function getColumnFor(Component $component): string
    {
        return match (true) {
            $component instanceof SectionTypePicker => 'library',
            $component instanceof Wizard => 'inspector',
            default => 'canvas',
        };
    }
}
