<?php

namespace App\View\Components\Core\Header;

use Closure;
use App\Models\Navigation as NavigationModel;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Navigation extends Component
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
        return view('components.core.header.navigation', [
            'nav' => NavigationModel::query()
                ->select(['navigation'])
                ->first()
        ]);
    }

}
