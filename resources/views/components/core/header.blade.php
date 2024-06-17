<header class="fixed z-10 mx-auto w-full bg-primary-100 dark:bg-primary-950">
    <x-layout.content>
        <div class="mx-auto flex flex-col flex-wrap items-center md:flex-row">
            {{-- Application Logo --}}
            <a class="title-font flex items-center font-medium text-gray-900 md:mb-0">
                <x-core.header.logo />
            </a>
            {{-- Menu Navigation //  --}}
            <nav class="flex flex-wrap items-center md:ml-auto md:mr-auto">
                <x-core.header.navigation />
            </nav>
            {{-- Header Side Content // Button --}}
            <x-core.header.content />
        </div>
    </x-layout.content>
</header>
