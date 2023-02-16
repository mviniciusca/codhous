    <div class="grid w-full rounded-2xl bg-white p-8">
        <div class="flex items-center justify-between gap-3 text-sm font-bold">

            <div class="flex items-center gap-3 text-base">
                {{-- Icon and Title --}}
                <ion-icon class="text-lg" name="bookmarks-outline"></ion-icon>
                <div id="title">Latest Tasks</div>
                {{-- Badge --}}
                <span class="flex items-center rounded-lg bg-gray-800 p-1 text-xs font-bold text-gray-100">
                    {{ $total }}
                </span>
            </div>
            {{-- Navigation --}}
            <div class="flex gap-3">

                <button>
                    <ion-icon class="text-lg" name="chevron-back-outline"></ion-icon>
                </button>

                <button>
                    <ion-icon class="text-lg" name="chevron-forward-outline"></ion-icon>
                </button>

            </div>
        </div>
        {{-- Listing Content  --}}
        <div class="mt-8">
            @foreach ($tasks as $task)
                <div class="mb-5 flex items-center justify-between gap-14">
                    <label for="{{ $task->id }}">
                        <input name="status" id="{{ $task->id }}" type="checkbox">
                        {{ $task->title }}
                    </label>
                    {{--  Delete Component --}}
                    <livewire:task.destroy :task="$task" :key="$task->id" />
                </div>
            @endforeach
        </div>
