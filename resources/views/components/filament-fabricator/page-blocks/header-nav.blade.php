@aware(['page'])
@props(['logo'=> null, 'navigations'=> null, 'menu_btn' => null, 'menu_btn_link' => null])

<div class="px-4 py-4 md:py-8">
    <div class="mx-auto max-w-7xl">
        <header class="body-font text-gray-600">
            <div class="container mx-auto flex flex-col flex-wrap items-center p-5 md:flex-row">
                <a class="title-font mb-4 flex items-center font-medium text-gray-900 md:mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2"
                        class="h-10 w-10 rounded-full bg-indigo-500 p-2 text-white" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                    <span class="ml-3 text-xl">Tailblocks</span>
                </a>
                <nav class="flex flex-wrap items-center justify-center text-base md:ml-auto md:mr-auto">
                    @foreach ( $navigations as $item )
                    <a href="{{ $item['navlink_url'] }}"
                        class="mr-5 hover:text-gray-900">{{ $item['navlink_text'] }}</a>
                    @endforeach
                </nav>
                <a href="">
                    <button
                        class="mt-4 inline-flex items-center rounded border-0 bg-gray-100 px-3 py-1 text-base hover:bg-gray-200 focus:outline-none md:mt-0">Button
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" class="ml-1 h-4 w-4" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </a>
            </div>
        </header>
    </div>
</div>
