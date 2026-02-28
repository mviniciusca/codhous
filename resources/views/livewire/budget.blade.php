@props(['code' => null])
<x-layout.section>
    @if($module)
    <livewire:cart-bag :count="count($data['content']['products'] ?? [])" wire:key="bag-count-{{ count($data['content']['products'] ?? []) }}" />
    <x-layout.content>
        @if($status)

        <div class="flex justify-center w-full">
            <div class="w-full max-w-5xl">
                <form wire:submit='create'>
                    {{ $this->form }}
                    <div class="flex justify-end mt-8">
                        <x-ui.button :icon="'none'" type="submit" class="w-full md:w-auto px-12 py-4 text-lg">
                            {{ __('Send') }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6 inline-flex ml-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
        @else

        @auth
        <x-section.empty-section />
        @endauth

        @endif

    </x-layout.content>
    @endif
</x-layout.section>