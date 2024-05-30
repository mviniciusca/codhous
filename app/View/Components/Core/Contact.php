<?php

namespace App\View\Components\Core;

use Closure;
use App\Models\Contact as ContactModel;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Contact extends Component
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
        return view(
            'components.core.contact',
            ['contact' => ContactModel::first()]
        );
    }
}
