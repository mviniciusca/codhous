<header class="body-font text-gray-600 mb-8">
    <div class="container mx-auto flex flex-col flex-wrap items-center p-5 md:flex-row">

        {{-- Application Logo --}}
        <a class="title-font text-gray-900 mb-4 flex items-center font-medium md:mb-0">
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
