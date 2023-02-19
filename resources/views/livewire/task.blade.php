        <div class="grid w-full rounded-2xl bg-white p-8">

            <div class="flex items-center justify-between gap-3 text-sm font-bold">

                <div class="flex items-center gap-3 text-base">
                    {{-- Icon and Title --}}

                    <div class="text-gray-500" id="title">Latest Tasks</div>
                    {{-- Badge --}}
                    @if ($total > 0)
                        <span
                            class="flex h-5 w-5 items-center justify-center rounded-full bg-gray-600 p-3 text-xs text-gray-100">
                            {{ $total }}
                        </span>
                    @endif
                </div>
                {{-- Navigation --}}
                <div class="flex gap-3">
                    {{ $tasks->links() }}
                </div>
            </div>

            {{-- Listing Content  --}}
            <div class="mt-8">
                @foreach ($tasks as $task)
                    <div class="mb-4 flex w-full items-center justify-between gap-5">
                        {{-- Listing Items --}}
                        <livewire:task.listing :task="$task" :key="$task->id . '-updated'" />
                        {{--  Delete Component --}}
                        <livewire:task.destroy :task="$task" :key="$task->id . '-delete ?>'" />
                    </div>
                @endforeach

                {{-- Empty / Clear Results --}}
                @if ($total == 0)
                    <x-clear />
                @endif
                {{-- Empty / Clear Results --}}

            </div>

        </div>
