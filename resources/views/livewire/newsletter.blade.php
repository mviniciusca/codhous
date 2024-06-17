<x-layout.section>
    <x-layout.content>
        <form
            class="mx-auto flex max-w-5xl items-center justify-around rounded-lg border border-primary-100 bg-white p-4 text-center dark:border-primary-800 dark:bg-primary-950"
            wire:submit="create">

            <img src="{{ asset('img/mail-sent.svg') }}" class="col-span-1 max-w-80" alt="{{ _('newsletter-image') }}">

            <div>
                <h1 class="text-4xl font-bold leading-tight tracking-tighter">
                    {{ $title ??  __('Subscribe our Newsletter') }}
                </h1>
                <p class="mb-6 text-sm">{{ $subtitle ?? __('Join our newsletter and stay ready with Zordie') }}</p>

                <div class="mx-auto max-w-xl">
                    <x-core.newsletter-form :form="$this->form" />
                    <p class="pt-4 text-sm">
                        {{ $info ?? 'Use your e-mail to subscribe our newsletter. We hate spam ;)' }}
                    </p>
                    <x-ui.button :icon="'none'" :filled="'true'">
                        <span class="inline-flex gap-2">
                            <p>{{ $btn_text ?? __('Subscribe') }}</p>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                            </svg>
                        </span>
                    </x-ui.button>
                </div>
            </div>

        </form>
    </x-layout.content>
</x-layout.section>
