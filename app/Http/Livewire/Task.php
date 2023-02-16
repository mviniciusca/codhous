<?php

namespace App\Http\Livewire;

use App\Models\Task as ModelTask;
use Livewire\Component;

class Task extends Component
{
    public ModelTask $task;

    protected $listeners = [
        'task::destroyed' => '$refresh',
    ];

    public function mount(ModelTask $task)
    {
        $this->task = $task;
    }

    public function render()
    {
        return view('livewire.task', [
            'total' => $this->task->all()->count(),
            'tasks' => $this->task->all()->sortByDesc('id'),
        ]);
    }
}
