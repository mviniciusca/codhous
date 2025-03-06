@aware(['page'])
@props(['cards', 'grid_view', 'centered', 'icon_filled', 'status', 'section_filled'])

@if ($status)
    <x-layout.section :$section_filled>
        <x-layout.content>
            <div
                class="{{ $grid_view === 'default' ? 'lg:grid-cols-3' : 'lg:grid-cols-4' }} space-y-8 py-8 md:grid md:grid-cols-2 md:gap-12 md:space-y-0">
                @foreach ($cards as $card)
                    <a href="{{ $card['link'] }}">
                        <div class="{{ $centered ? 'text-center' : 'text-left' }}">
                            <div
                                class="{{ $centered ? 'mx-auto' : '' }} {{ $icon_filled ? 'dark:bg-secondary-500 bg-secondary-500 text-primary-50' : 'bg-none' }} my-4 flex h-10 w-10 items-center justify-center rounded-full lg:h-14 lg:w-14">
                                <x-ionicon :size="'big'" :icon="$card['icon']" />
                            </div>
                            <h3 class="mb-2 text-xl font-bold">
                                {!! $card['title'] !!}
                            </h3>
                            <p class="md:text-md text-sm lg:text-base">
                                {!! $card['info'] !!}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </x-layout.content>
    </x-layout.section>
@endif
