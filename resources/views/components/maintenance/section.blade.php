@dd($layout)
@if($maintenance_mode)
<div class="absolute w-full h-full z-50 bg-primary-50 dark:bg-primary-900 justify-center grid">
    <section class="grid items-center justify-center">
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="py-4 mx-auto text-center">
                @livewire('darkmode')
            </div>
            <div class="mx-auto max-w-screen-sm text-center">
                <img class="w-96 mx-auto" src="{{ asset('/img/under-construction.svg') }}">
                <p class="mb-4 text-3xl tracking-tight font-bold md:text-4xl">
                    {{ __('Maintenance') }}
                </p>
                <p class="mb-4 text-lg font-light">{{ __('We\'re updating our website.
                    Please, come back soon!') }}</p>
                <x-ui.button>
                    {{ __('Github') }}
                </x-ui.button>
            </div>
        </div>
    </section>
</div>
@endif