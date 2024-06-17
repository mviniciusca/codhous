<x-layout.section>
    <x-layout.content>
        <footer>
            <div class="mx-auto w-full max-w-screen-xl p-4 md:py-8">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <x-core.header.logo />
                    <ul class="mb-6 flex flex-wrap items-center text-sm font-medium text-gray-500 sm:mb-0">
                        <li>
                            <a href="#" class="me-4 hover:underline md:me-6">About</a>
                        </li>
                        <li>
                            <a href="#" class="me-4 hover:underline md:me-6">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="#" class="me-4 hover:underline md:me-6">Licensing</a>
                        </li>
                        <li>
                            <a href="#" class="hover:underline">Contact</a>
                        </li>
                    </ul>
                </div>
                <hr class="my-6 border-gray-200 dark:border-primary-800 sm:mx-auto lg:my-8" />
                <span class="block text-sm text-gray-500 sm:text-center">
                    © {{ date('Y') }}
                    <a href="/" class="hover:underline">
                        {{ ENV('APP_NAME') }}
                    </a> -
                    {{ __('All Rights Reserved.') }}
                </span>
            </div>
        </footer>
    </x-layout.content>
</x-layout.section>
