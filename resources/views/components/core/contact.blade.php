@if($contact && $contact->status)
<x-layout.section>
    <x-layout.content>
        <div class="container mx-auto flex flex-wrap px-5 py-24 sm:flex-nowrap">
            <div
                class="relative flex items-end justify-start overflow-hidden rounded-lg p-10 {{ $contact->map ? 'bg-primary-300 dark:bg-primary-950' : 'bg-none' }} sm:mr-10 md:w-1/2 lg:w-2/3">
                @if($contact->map)
                <iframe width="100%" height="100%" class="absolute inset-0" frameborder="0" title="map" marginheight="0"
                    marginwidth="0" scrolling="no" src="{{ $contact->map }}"
                    style="filter: grayscale(1) contrast(1) opacity(0.7);"></iframe>
                @else
                <img class="absolute inset-0 object-cover" src="{{ asset('img/paper-map.svg') }}" alt="map-image">
                @endif
                <div
                    class="relative flex flex-wrap rounded bg-primary-50 py-6 text-primary-700 shadow-md dark:bg-primary-900 dark:text-primary-300">
                    <div class="px-6 lg:w-1/2">
                        <h2 class="title-font text-xs font-semibold uppercase tracking-widest">{{ __('Address') }}</h2>
                        <p class="mt-1">{{ $contact->address }}</p>
                    </div>
                    <div class="mt-4 px-6 lg:mt-0 lg:w-1/2">
                        <h2 class="title-font text-xs font-semibold uppercase tracking-widest">{{ __('Email') }}</h2>
                        <a class="leading-relaxed">{{ $contact->setting->email }}</a>
                        <h2 class="title-font mt-4 text-xs font-semibold uppercase tracking-widest">{{ __('Phone') }}
                        </h2>
                        <p class="leading-relaxed">{{ $contact->setting->phone }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex w-full flex-col md:ml-auto md:mt-0 md:w-1/2 md:py-2 lg:w-1/3">
                {{-- Form Component --}}
                <livewire:mail.form />
            </div>
        </div>
        </div>
    </x-layout.content>
</x-layout.section>
@endif
