<?php

namespace App\View\Components\Layout;

use App\Models\Layout;
use App\Models\Setting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Meta extends Component
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
            'components.layout.meta',
            [
                'meta' => Setting::query()
                    ->select(['meta_title', 'meta_author', 'meta_keywords', 'meta_description', 'google_analytics', 'header_scripts', 'google_tag'])
                    ->first(),
                'favicon' => Layout::query()
                    ->select(['favicon'])
                    ->first(),
            ]
        );
    }
}
