<?php

namespace App\Livewire;

use Livewire\Component;

class Calculator extends Component
{
    public $bgColor = '';
    public $shape = 'retangular'; // 'retangular' or 'cilindrico'
    public $width = 0;
    public $length = 0;
    public $thickness_cm = 0; // Usuário digita em CM

    public $radius_cm = 0; // Para cilindro (cm)
    public $height = 0; // Para cilindro (metros)

    public $volume = 0;
    public $volumeWithMargin = 0;

    // Constante para margem de segurança (10%)
    private const SAFETY_MARGIN = 1.10;

    public function updated($propertyName)
    {
        if ($propertyName === 'shape') {
            $this->resetFields(false); // Reset dimension fields, keep shape
        }
        $this->calculate();
    }

    public function calculate(): void
    {
        if ($this->shape === 'retangular') {
            $thicknessMeters = (float)$this->thickness_cm / 100;
            $this->volume = (float)$this->width * (float)$this->length * $thicknessMeters;
        } elseif ($this->shape === 'cilindrico') {
            $radiusMeters = (float)$this->radius_cm / 100;
            $this->volume = pi() * pow($radiusMeters, 2) * (float)$this->height;
        }

        $this->volumeWithMargin = $this->volume * self::SAFETY_MARGIN;
    }

    public function resetFields($resetShape = true): void
    {
        $fields = ['width', 'length', 'thickness_cm', 'radius_cm', 'height', 'volume', 'volumeWithMargin'];
        if ($resetShape) {
            $fields[] = 'shape';
            $this->shape = 'retangular';
        }
        $this->reset($fields);
    }

    public function render()
    {
        return view('livewire.calculator');
    }
}
