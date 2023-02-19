<div>

    <label for="{{ $task->id }}" class="flex cursor-pointer items-center space-x-2 pt-1 pb-1">
        <div class="relative">
            <input type="checkbox" {{ $task->status ? 'checked' : '' }} name="status" wire:model="task.status"
                id="{{ $task->id }}"
                class="h-4 w-4 cursor-pointer appearance-none rounded-full border border-gray-400 checked:border-transparent checked:bg-gray-500 hover:text-gray-500 focus:border-gray-300 focus:bg-gray-300 focus:text-gray-600 focus:outline-none" />
            <svg class="pointer-events-none absolute inset-0 m-1.5 hidden h-4 w-4 fill-current text-white"
                viewBox="0 0 20 20">
                <path d="M0 11l2-2 5 5L18 3l2 2L7 18z" />
            </svg>
        </div>
        <span class="{{ $task->status ? 'line-through italic' : '' }}">{{ $task->title }}</span>
    </label>

</div>
