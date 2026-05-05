<?php

namespace App\Livewire;

use App\Models\Showcase;
use Livewire\Component;
use Livewire\WithPagination;

class ShowcaseFeed extends Component
{
    use WithPagination;

    public $limit = 6;
    public $showPagination = true;

    public function mount($limit = null, $showPagination = true)
    {
        if ($limit) {
            $this->limit = $limit;
        }
        $this->showPagination = $showPagination;
    }

    public function render()
    {
        return view('livewire.showcase-feed', [
            'showcases' => Showcase::active()
                ->orderBy('sort_order')
                ->latest()
                ->paginate($this->limit),
        ]);
    }
}
