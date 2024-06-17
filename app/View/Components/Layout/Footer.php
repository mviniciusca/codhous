<?php

namespace App\View\Components\Layout;

use App\Models\Navigation;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
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
        return view('components.layout.footer', [
            'navigation' => Navigation::query()
                ->select(['navigation'])
                ->first(),
        ]);
    }
}
