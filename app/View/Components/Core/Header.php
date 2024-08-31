<?php

namespace App\View\Components\Core;

use App\Models\Module;
use App\Models\Navigation;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.core.header', [
            'header' => Module::first()->module['header'],
            'navbar' => Navigation::select(['status', 'fixed'])->first(),
        ]);
    }
}
