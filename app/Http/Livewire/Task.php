<?php

namespace App\Http\Livewire;

use App\Models\Task as ModelTask;
use Livewire\Component;
use Livewire\WithPagination;

class Task extends Component
{
    use WithPagination;
    public ModelTask $task;

    protected $listeners = [
        'task::destroyed' => '$refresh',
        'task::updated' => '$refresh'
    ];

    public function mount(ModelTask $task)
    {
        $this->task = $task;
    }

    public function render()
    {
        return view('livewire.task', [
            'total' => $this->task->all()->count(),
            'tasks' => ModelTask::simplePaginate(10),
        ]);
    }

    public function updated()
    {
        $this->task->save();
    }
}
