<x-app-layout>
    <x-page-title>Tasks</x-page-title>
    <div class="flex flex-row gap-14">
        {{-- Create new Task --}}
        <div class="flex basis-48">
            <button
                class="flex h-10 w-full items-center justify-center gap-3 border border-gray-400 bg-none font-bold text-gray-600 hover:bg-gray-700 hover:text-gray-100">
                <ion-icon class="h-4 w-4" name="add-outline"></ion-icon> new task
            </button>
        </div>
        {{-- Livewire app: task --}}
        <div class="w-full">
            <div class="grid grid-cols-3 gap-14">
                <livewire:task />
            </div>
        </div>
</x-app-layout>
