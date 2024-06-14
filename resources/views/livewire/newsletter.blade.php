<section class="bg-white dark:bg-gray-900">
    <div class="mx-auto rounded-md bg-primary-950 bg-opacity-70 px-8 py-12">
        <form class="mx-auto inline-block w-full gap-2 text-center" wire:submit="create">
            <span>
                {{ $this->form }}
            </span>
            <x-ui.button :icon="'none'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </x-ui.button>
        </form>
    </div>
</section>
