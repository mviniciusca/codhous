<x-layout.section>
    <x-layout.content>
        <form
            class="mx-auto max-w-4xl rounded-md border border-primary-100 bg-white p-12 text-center dark:border-primary-800 dark:bg-primary-950"
            wire:submit="create">

            <h1 class="text-4xl font-bold leading-tight tracking-tighter"> {{ __('Subscribe our Newsletter') }}
            </h1>
            <p class="mb-6 text-sm">{{ __('Join our newsletter and stay ready with Zordie') }}</p>

            <div class="mx-auto max-w-xl">
                {{ $this->form }}
                <x-ui.button :icon="'none'" :filled="'true'">
                    <span class="inline-flex gap-2">
                        <p>Subscribe</p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>
                    </span>
                </x-ui.button>
            </div>

        </form>
    </x-layout.content>
</x-layout.section>
