<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class CartBag extends Component
{
    #[Reactive]
    public $count = 0;

    public function render()
    {
        return view('livewire.cart-bag');
    }
}
