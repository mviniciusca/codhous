<?php

namespace App\Http\Livewire\Task;

use App\Models\Task as ModelTask;
use Livewire\Component;

class Destroy extends Component
{
    public ModelTask $task;

    public function mount(ModelTask $task)
    {
        $this->task = $task;
    }

    public function render()
    {
        return view('livewire.task.destroy');
    }

    public function destroy()
    {
        $this->task->delete();
        $this->emit('task::destroyed');
        session()->flash('task-destroyed', 'Task destroyed!');
    }


}
