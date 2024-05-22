@aware(['page'])
@props(['cards'])

<section>
    <div class="space-y-8 md:grid md:grid-cols-2 md:gap-12 md:space-y-0 lg:grid-cols-3">
        @foreach ($cards as $card)
        <a href="{{ $card['link'] }}">
            <div>
                <div
                    class="mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-950">
                    <x-ionicon :icon="$card['icon']" />
                </div>
                <h3 class="dark:text-white mb-2 text-xl font-bold">{{ $card['title'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {{ $card['info'] }}
                </p>
            </div>
        </a>
        @endforeach
    </div>
</section>
