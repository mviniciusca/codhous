<?php

namespace App\Livewire;

use App\Models\Partner;
use Livewire\Component;

class CommercialPartners extends Component
{
    public $data = [];

    public function mount($data = [])
    {
        $this->data = $data;
    }

    public function render()
    {
        $partners = Partner::where('is_active', true)->orderBy('name')->get();

        return view('livewire.commercial-partners', [
            'partners' => $partners,
            'features' => $this->data['content']['features'] ?? [],
            'mainImage' => $this->data['content']['main_image'] ?? null,
            'headerTitle' => $this->data['header']['title'] ?? $this->data['title'] ?? null,
            'headerSubtitle' => $this->data['header']['subtitle'] ?? $this->data['badge'] ?? null,
            'headerDesc' => $this->data['header']['description'] ?? $this->data['description'] ?? null,
        ]);
    }
}
