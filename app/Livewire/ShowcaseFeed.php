<?php

namespace App\Livewire;

use App\Models\Showcase;
use Livewire\Component;
use Livewire\WithPagination;

class ShowcaseFeed extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.showcase-feed', [
            'showcases' => Showcase::active()
                ->orderBy('sort_order')
                ->latest()
                ->paginate(2),
        ]);
    }
}
