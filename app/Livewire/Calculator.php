<?php

namespace App\Livewire;

use Livewire\Component;

class Calculator extends Component
{
    public $width = 0;
    public $length = 0;
    public $thickness_cm = 0; // Usuário digita em CM
    public $volume = 0;
    public $volumeWithMargin = 0;

    // Constante para margem de segurança (10%)
    private const SAFETY_MARGIN = 1.1;

    public function updated($propertyName)
    {
        $this->calculate();
    }

    public function calculate(): void
    {
        // Converte CM para Metros internamente
        $thicknessMeters = (float)$this->thickness_cm / 100;

        $this->volume = (float)$this->width * (float)$this->length * $thicknessMeters;
        $this->volumeWithMargin = $this->volume * self::SAFETY_MARGIN;
    }

    public function resetFields(): void
    {
        $this->reset(['width', 'length', 'thickness_cm', 'volume', 'volumeWithMargin']);
    }

    public function render()
    {
        return view('livewire.calculator');
    }
}
