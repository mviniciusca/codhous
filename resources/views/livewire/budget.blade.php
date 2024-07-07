@props(['code' => null, 'status' => null])
<x-layout.section>
    <x-layout.content>
        @if($status)
        <div class="inline-flex w-full gap-8">
            <div class="w-full hidden lg:block lg:w-1/3 bg-cover rounded-md bg-no-repeat bg-center"
                style="background-image: url(https://www.guru.com/blog/wp-content/uploads/2023/04/civil-engineer-duties.jpg)">
            </div>
            <div class="w-full lg:w-2/3">
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