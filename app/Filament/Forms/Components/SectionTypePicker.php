<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

/**
 * Galeria de tipos de seção: busca, categorias e cards com miniatura.
 * O valor é a chave do tipo. Desabilitado (edição), mostra só o tipo escolhido.
 */
class SectionTypePicker extends Field
{
    protected string $view = 'filament.forms.components.section-type-picker';

    /** @var array<string, array<string, mixed>> | Closure */
    protected array|Closure $types = [];

    /** @var array<string, array{label: string, icon: string}> | Closure */
    protected array|Closure $categories = [];

    /** @var array<string, int> | Closure */
    protected array|Closure $usage = [];

    protected string $previewsPath = 'filament.forms.components.section-type-picker.previews';

    /**
     * @param  array<string, array<string, mixed>> | Closure  $types  tipo => [label, description, icon, categories, keywords, preview]
     */
    public function types(array|Closure $types): static
    {
        $this->types = $types;

        return $this;
    }

    /**
     * @param  array<string, array{label: string, icon: string}> | Closure  $categories
     */
    public function categories(array|Closure $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Quantidade de registros existentes por tipo, exibida como "em uso".
     *
     * @param  array<string, int> | Closure  $usage
     */
    public function usage(array|Closure $usage): static
    {
        $this->usage = $usage;

        return $this;
    }

    public function previewsPath(string $path): static
    {
        $this->previewsPath = $path;

        return $this;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getTypes(): array
    {
        return $this->evaluate($this->types);
    }

    /**
     * Só as categorias que têm algum tipo.
     *
     * @return array<string, array{label: string, icon: string}>
     */
    public function getCategories(): array
    {
        $used = collect($this->getTypes())->pluck('categories')->flatten()->unique();

        return collect($this->evaluate($this->categories))
            ->filter(fn ($category, string $key): bool => $used->contains($key))
            ->all();
    }

    /**
     * @return array<string, int>
     */
    public function getUsage(): array
    {
        return $this->evaluate($this->usage);
    }

    public function getPreviewView(string $type): ?string
    {
        $view = "{$this->previewsPath}.{$type}";

        return view()->exists($view) ? $view : null;
    }
}
