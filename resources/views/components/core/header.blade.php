<header
    class="fixed z-10 mx-auto w-full bg-primary-50 bg-blend-multiply shadow-2xl dark:bg-primary-950 dark:bg-opacity-70">
    <div class="container mx-auto flex flex-col flex-wrap items-center p-3 md:flex-row">

        {{-- Application Logo --}}
        <a class="title-font mb-4 flex items-center font-medium text-gray-900 md:mb-0">
            <x-core.header.logo />
        </a>

        {{-- Menu Navigation //  --}}
        <nav class="flex flex-wrap items-center justify-center text-base md:ml-auto md:mr-auto">
            <x-core.header.navigation />
        </nav>

        {{-- Header Side Content // Button --}}
        <x-core.header.content />

    </div>
</header>
