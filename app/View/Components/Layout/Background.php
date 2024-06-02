<?php

namespace App\View\Components\Layout;

use App\Models\Layout;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Background extends Component
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
        $content = $this->getContent();
        $background = $this->getBackground();

        return view('components.layout.background', [
            'background' => $background['background_image'],
            'content' => $content['content'],
        ]);
    }

    public function getContent()
    {
        $data = Layout::query()
            ->select(['content'])
            ->first();
        return $data;
    }

    public function getBackground()
    {
        $data = Layout::query()
            ->select(['background_image'])
            ->first();
        return $data;
    }
}
