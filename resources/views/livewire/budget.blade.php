@props(['code' => null])
<x-layout.section>
    @if($module)
    <livewire:cart-bag :count="count($data['content']['products'] ?? [])" wire:key="bag-count-{{ count($data['content']['products'] ?? []) }}" />
    <x-layout.content>
        @if($status)

        <div class="flex justify-center w-full min-h-[400px]">
            <div class="w-full max-w-5xl">
                @if(!$isSubmitted)
                <form wire:submit='create'>
                    {{ $this->form }}
                    <div class="flex justify-start mt-12">
                        <button type="submit" class="w-full md:w-auto px-16 py-4 text-xs font-black tracking-[0.3em] uppercase bg-black text-white hover:bg-white hover:text-black border-2 border-black transition-all duration-500 ease-in-out flex items-center justify-center group focus:outline-none">
                            {{ __('Send Request') }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-4 ml-4 transform transition-transform duration-500 group-hover:translate-x-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </form>
                @else
                <div class="flex flex-col items-center justify-center p-12 border-2 border-black/5 rounded-3xl bg-white shadow-sm space-y-8 animate-in fade-in zoom-in duration-700">
                    <div class="p-6 bg-black rounded-full text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-12">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    
                    <div class="text-center space-y-3">
                        <h2 class="text-2xl font-black uppercase tracking-widest text-black">{{ __('Thank You!') }}</h2>
                        <p class="text-sm text-gray-400 font-medium tracking-wide">{{ __('Your budget request has been successfully received.') }}</p>
                        <p class="text-xs text-gray-400 uppercase tracking-widest">{{ __('Expect a response within 24 hours.') }}</p>
                    </div>

                    <div class="pt-4">
                        <button wire:click="resetForm" class="px-12 py-4 text-[10px] font-black tracking-[0.4em] uppercase bg-white text-black border-2 border-black hover:bg-black hover:text-white transition-all duration-500 ease-in-out flex items-center justify-center focus:outline-none">
                            {{ __('Create New Budget Request') }}
                        </button>
                    </div>
                </div>
                @endif
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