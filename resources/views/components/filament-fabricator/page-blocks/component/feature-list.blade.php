@aware(['page'])
@props(['cards', 'grid_view', 'centered', 'icon_filled', 'status'])

@if($status)
<section class="bg-primary-950 bg-opacity-5 py-8 dark:bg-opacity-25">
    <div class="px-4 py-4">
        <div class="mx-auto max-w-7xl">
            <div class="space-y-8 md:grid md:grid-cols-2 md:gap-12 md:space-y-0
        {{ $grid_view === 'default' ? 'lg:grid-cols-3' : 'lg:grid-cols-4' }}">

                @foreach ($cards as $card)
                <a href="{{ $card['link'] }}">
                    <div class="{{ $centered ? 'text-center' : 'text-left' }}">
                        <div class="{{ $centered ? 'mx-auto' : '' }}
                mb-4 flex h-10 w-10 items-center justify-center rounded-full lg:h-12 lg:w-12
                {{ $icon_filled ? 'dark:bg-primary-950 bg-secondary-500 text-primary-50' : 'bg-none' }}
                ">
                            <x-ionicon :icon="$card['icon']" />
                        </div>
                        <h3 class="mb-2 text-xl font-bold">{{ $card['title'] }}</h3>
                        <p> {{ $card['info'] }} </p>
                    </div>
                </a>
                @endforeach

            </div>
        </div>
    </div>
</section>
@endif
