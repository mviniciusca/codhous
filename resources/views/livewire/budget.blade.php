@props(['code' => null])
<x-layout.section>
    <x-layout.content>
        @if($status)
        <div class="inline-flex w-full gap-8">
            @if($image)
            <div class="w-full hidden lg:block lg:w-1/3 bg-cover rounded-md bg-no-repeat bg-center"
                style="background-image: url({{ asset('storage/' . $image ) }})">
            </div>
            @endif
            <div class="w-full {{ $image ? 'lg:w-2/3' : 'lg:w-full' }}">
                <form wire:submit='create'>
                    {{ $this->form }}
                    <span class="inline-flex">
                        <x-ui.button :icon="'none'">
                            {{ __('Send') }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6 inline-flex">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                    </span>
                    </x-ui.button>
                </form>
            </div>
        </div>
        @else
        @auth
        <x-section.empty-section />
        @endauth
        @endif
    </x-layout.content>
</x-layout.section>