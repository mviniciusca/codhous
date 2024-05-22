@aware(['page'])
@props(['title' => null, 'subtitle' => null, 'HeroSectionImage' => null, 'btn_text' => null, 'btn_url' => null,
'btn_full_text' => null, 'btn_full_url' => null])


<section class="bg-white dark:bg-gray-900">
    <div class="mx-auto grid max-w-screen-xl px-4 py-8 lg:grid-cols-12 lg:gap-8 lg:py-16 xl:gap-0">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1
                class="dark:text-white mb-4 max-w-2xl text-4xl font-extrabold leading-none tracking-tight md:text-5xl xl:text-6xl">
                {{ $title }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-2xl font-light md:text-lg lg:mb-8 lg:text-xl">
                {{ $subtitle }}
            </p>

            @if($btn_url && $btn_text)
            <a href="{{ $btn_url }}"
                class="text-white mr-3 inline-flex items-center justify-center rounded-lg bg-primary-700 px-5 py-3 text-center text-base font-medium hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                {{ $btn_text }}
                <svg class="-mr-1 ml-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </a>
            @endif

            @if($btn_full_url && $btn_full_text)
            <a href="{{ $btn_full_url }}"
                class="border-gray-300 text-gray-900 hover:bg-gray-100 focus:ring-gray-100 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-800 inline-flex items-center justify-center rounded-lg border px-5 py-3 text-center text-base font-medium focus:ring-4">
                {{ $btn_full_text }}
            </a>
            @endif

        </div>
        <div class="hidden lg:col-span-5 lg:mt-0 lg:flex">
            <img src="{{ asset('storage/' .  $HeroSectionImage) }}" alt="mockup">
        </div>
    </div>
</section>
