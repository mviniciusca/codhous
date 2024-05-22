@aware(['page'])
@props(['images'])

<section>
    <div class="container mx-auto px-5 py-24">
        <div class="-m-4 flex flex-wrap">
            @foreach ($images as $image )
            <div class="p-4 sm:w-1/2 lg:w-1/3">
                <div class="relative flex">
                    <img alt="image" class="absolute inset-0 h-full w-full object-cover object-center"
                        src="{{ asset('storage/' . $image['image']) }}">
                    <div
                        class="relative z-10 w-full border-4 border-primary-200 bg-primary-50 px-8 py-10 opacity-0 hover:opacity-100 dark:border-primary-700 dark:bg-primary-800">
                        <h2 class="mb-3 text-sm font-bold uppercase tracking-widest text-secondary-400">
                            {{ $image['subtitle'] }}
                        </h2>
                        <h1 class="mb-3 text-lg font-bold">{{ $image['title'] }}</h1>
                        <p class="leading-relaxed">{{ $image['info'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
