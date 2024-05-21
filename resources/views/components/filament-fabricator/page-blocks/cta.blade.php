@aware(['page'])
@props(['title' => null, 'image' => null, 'subtitle' => null, 'btn_text' => null, 'btn_url' => null])

<div class="px-4 py-4 md:py-8">
    <div class="mx-auto max-w-7xl">
        <section class="bg-white dark:bg-gray-900">
            <div
                class="mx-auto max-w-screen-xl items-center gap-8 px-4 py-8 sm:py-16 md:grid md:grid-cols-2 lg:px-6 xl:gap-16">
                <img class="w-full" src="{{ asset('storage/' . $image) }}" alt="image">
                <div class="mt-4 md:mt-0">
                    <h2 class="dark:text-white mb-4 text-4xl font-extrabold tracking-tight text-primary-700">
                        {{ $title }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 font-light md:text-lg">{{ $subtitle }}</p>
                    <a href="{{ $btn_url }}"
                        class="text-white inline-flex items-center rounded-lg bg-primary-700 px-5 py-2.5 text-center text-sm font-medium hover:bg-primary-200 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                        {{ $btn_text }}
                        <svg class="-mr-1 ml-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>
