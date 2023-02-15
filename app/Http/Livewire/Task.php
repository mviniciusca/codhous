<?php

namespace App\Http\Livewire;

use App\Models\Task as ModelTask;
use Livewire\Component;

class Task extends Component
{
    public ModelTask $task;

    public function mount(ModelTask $task)
    {
        $this->task = $task;
    }
    public function render()
    {
        return view('livewire.task', [
            'total' => ModelTask::all()->count(),
            'items' => ModelTask::all()->sortByDesc('id'),
        ]);
    }
}
