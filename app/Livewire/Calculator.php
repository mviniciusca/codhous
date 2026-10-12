<?php

namespace App\Livewire;

use App\Livewire\Concerns\CalculatesVolume;
use Livewire\Component;

/**
 * Calculadora de volume do site (bloco “calculator” do Page Builder). Usa as
 * mesmas fórmulas, margens e regras de arredondamento configuradas em
 * Ferramentas › Fórmulas de volume, e o mesmo motor da calculadora do painel.
 */
class Calculator extends Component
{
    use CalculatesVolume;

    public $bgColor = '';

    public $title = null;

    public $subtitle = null;

    public $description = null;

    public $headerVisible = true;

    public $headerAlignment = 'left';

    public function mount(): void
    {
        $this->selectFormula($this->formulas->first()?->getKey());
    }

    protected function defaultMarginSuffix(): string
    {
        return ' (recomendado)';
    }

    public function resetFields(): void
    {
        $this->selectFormula($this->formulaId);
    }

    public function render()
    {
        return view('livewire.calculator');
    }
}
