<?php

namespace App\Livewire;

use Livewire\Component;

class Darkmode extends Component
{
    public bool $light;
    public function mouth()
    {
        $this->light = true;
    }
    public function update()
    {
        //
    }
    public function render()
    {
        return view('livewire.darkmode');
    }
}
