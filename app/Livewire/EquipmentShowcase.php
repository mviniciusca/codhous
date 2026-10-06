<?php

namespace App\Livewire;

use App\Models\Equipment;
use Livewire\Component;
use Livewire\WithPagination;

class EquipmentShowcase extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $availability = 'all';
    public $sort = 'recent';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'availability' => ['except' => 'all'],
        'sort' => ['except' => 'recent'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->dispatch('scroll-to-equipment');
    }

    public function updatingCategory()
    {
        $this->resetPage();
        $this->dispatch('scroll-to-equipment');
    }

    public function updatingAvailability()
    {
        $this->resetPage();
        $this->dispatch('scroll-to-equipment');
    }

    public function updatingSort()
    {
        $this->resetPage();
        $this->dispatch('scroll-to-equipment');
    }

    public function updatingPage()
    {
        $this->dispatch('scroll-to-equipment');
    }

    public function render()
    {
        $query = Equipment::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->availability === 'available') {
            $query->where('is_available', true);
        } elseif ($this->availability === 'unavailable') {
            $query->where('is_available', false);
        }

        if ($this->sort === 'recent') {
            $query->latest();
        } elseif ($this->sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($this->sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        }

        $categories = Equipment::select('category')->distinct()->whereNotNull('category')->pluck('category');

        return view('livewire.equipment-showcase', [
            'equipments' => $query->paginate(12)->fragment('locacao-equipamentos'),
            'categories' => $categories,
        ]);
    }
}
