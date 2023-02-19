<?php

namespace App\Http\Livewire\Task;

use App\Models\Task as ModelTask;
use Livewire\Component;

class Listing extends Component
{
    public ModelTask $task;

    protected $rules = [
        'task.status' => ['boolean']
    ];

    public function mount(ModelTask $task)
    {
        $this->task = $task;
    }

    public function render()
    {
        return view('livewire.task.listing',[
            'task' => $this->task->all()
        ]);
    }

    public function updated()
    {
        $this->task->save();
        $this->emit('task::updated');
    }

}
