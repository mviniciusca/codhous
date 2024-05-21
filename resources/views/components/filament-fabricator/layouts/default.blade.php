@props(['page'])
<x-filament-fabricator::layouts.base :title="$page->title">
    @vite('resources/css/app.css')
    {{-- Header Here --}}

    <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

    {{-- Footer Here --}}
    <footer class="body-font">
        <div class="px-4 py-4 md:py-8">
            <div class="mx-auto max-w-7xl">
                <div class="container mx-auto px-5 py-24">
                    <div class="order-first flex flex-wrap text-center md:text-left">
                        <div class="w-full px-4 md:w-1/2 lg:w-1/4">
                            <h2 class="title-font text-gray-900 mb-3 text-sm font-medium tracking-widest">CATEGORIES
                            </h2>
                            <nav class="mb-10 list-none">
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">First Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Second Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Third Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Fourth Link</a>
                                </li>
                            </nav>
                        </div>
                        <div class="w-full px-4 md:w-1/2 lg:w-1/4">
                            <h2 class="title-font text-gray-900 mb-3 text-sm font-medium tracking-widest">CATEGORIES
                            </h2>
                            <nav class="mb-10 list-none">
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">First Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Second Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Third Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Fourth Link</a>
                                </li>
                            </nav>
                        </div>
                        <div class="w-full px-4 md:w-1/2 lg:w-1/4">
                            <h2 class="title-font text-gray-900 mb-3 text-sm font-medium tracking-widest">CATEGORIES
                            </h2>
                            <nav class="mb-10 list-none">
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">First Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Second Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Third Link</a>
                                </li>
                                <li>
                                    <a class="text-gray-600 hover:text-gray-800">Fourth Link</a>
                                </li>
                            </nav>
                        </div>
                        <div class="w-full px-4 md:w-1/2 lg:w-1/4">
                            <h2 class="title-font text-gray-900 mb-3 text-sm font-medium tracking-widest">SUBSCRIBE</h2>
                            <div
                                class="flex flex-wrap items-end justify-center md:flex-nowrap md:justify-start lg:flex-wrap xl:flex-nowrap">
                                <div class="relative mr-2 w-40 sm:mr-4 sm:w-auto lg:mr-0 xl:mr-4">
                                    <label for="footer-field"
                                        class="text-gray-600 text-sm leading-7">Placeholder</label>
                                    <input type="text" id="footer-field" name="footer-field"
                                        class="bg-gray-100 border-gray-300 focus:bg-transparent focus:ring-indigo-200 focus:border-indigo-500 text-gray-700 w-full rounded border bg-opacity-50 px-3 py-1 text-base leading-8 outline-none transition-colors duration-200 ease-in-out focus:ring-2">
                                </div>
                                <button
                                    class="text-white bg-indigo-500 hover:bg-indigo-600 inline-flex flex-shrink-0 rounded border-0 px-6 py-2 focus:outline-none lg:mt-2 xl:mt-0">Button</button>
                            </div>
                            <p class="text-gray-500 mt-2 text-center text-sm md:text-left">Bitters chicharrones fanny
                                pack
                                <br class="hidden lg:block">waistcoat green juice
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="container mx-auto flex flex-col items-center px-5 py-6 sm:flex-row">
                        <a
                            class="title-font text-gray-900 flex items-center justify-center font-medium md:justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                class="text-white bg-indigo-500 h-10 w-10 rounded-full p-2" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                            </svg>
                            <span class="ml-3 text-xl">Tailblocks</span>
                        </a>
                        <p class="text-gray-500 mt-4 text-sm sm:ml-6 sm:mt-0">© 2020 Tailblocks —
                            <a href="https://twitter.com/knyttneve" rel="noopener noreferrer" class="text-gray-600 ml-1"
                                target="_blank">@knyttneve</a>
                        </p>
                        <span class="mt-4 inline-flex justify-center sm:ml-auto sm:mt-0 sm:justify-start">
                            <a class="text-gray-500">
                                <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    class="h-5 w-5" viewBox="0 0 24 24">
                                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                                </svg>
                            </a>
                            <a class="text-gray-500 ml-3">
                                <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    class="h-5 w-5" viewBox="0 0 24 24">
                                    <path
                                        d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z">
                                    </path>
                                </svg>
                            </a>
                            <a class="text-gray-500 ml-3">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" class="h-5 w-5" viewBox="0 0 24 24">
                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                    <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
                                </svg>
                            </a>
                            <a class="text-gray-500 ml-3">
                                <svg fill="currentColor" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="0" class="h-5 w-5" viewBox="0 0 24 24">
                                    <path stroke="none"
                                        d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z">
                                    </path>
                                    <circle cx="4" cy="4" r="2" stroke="none"></circle>
                                </svg>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</x-filament-fabricator::layouts.base>
