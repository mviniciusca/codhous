<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Task as TaskModel;

class Task extends Component
{

	/**
	 * @var TaskModel
	 */
	public TaskModel $task;

	/**
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
    public function render()
    {
        return view('livewire.task', [
			'tasks' => $this->task->all()->sortByDesc('id'),
        ]);
    }

	protected $rules = [
		'task.title'    => ['required'],
		'task.status'   => ['required'],
		'task.priority' => ['required'],
	];

	/**
	 * @param ModelTask $task
	 * @return void
	 */
	public function mount(TaskModel $task)
	{
	    $this->task = $task;
	}
}
