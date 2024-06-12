<div>
    <label class="relative -mt-5 inline-flex w-auto cursor-pointer items-center">
        <input type="checkbox" wire:model.live='active' class="peer sr-only" checked>
        <div
            class="peer h-6 w-11 rounded-full bg-primary-200 after:absolute after:start-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-primary-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-primary-300 dark:border-primary-600 dark:bg-primary-700 dark:peer-focus:ring-primary-800 rtl:peer-checked:after:-translate-x-full">
        </div>
        <span class="ms-3 text-sm font-medium">
            @if($active)
            <x-ui.icon.sun />
            @else
            <x-ui.icon.moon />
            @endif
        </span>
    </label>
</div>
