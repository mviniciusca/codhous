@if($header)
<header
    class="{{ $navbar->fixed ? 'fixed shadow-primary-200 dark:shadow-primary-950 shadow-md' : 'block' }} z-10 mx-auto w-full bg-primary-100 dark:bg-primary-950">

    {{-- Discovery Mode --}}
    @if($maintenance_mode && ($discovery_mode && auth()->hasUser()))
    <x-maintenance.discovery />
    @endif

    <x-layout.content>
        <div class="mx-auto flex flex-col flex-wrap items-center md:flex-row">
            {{-- Application Logo --}}
            <a class="title-font flex items-center font-medium text-gray-900 md:mb-0">
                <x-core.header.logo />
            </a>
            {{-- Menu Navigation // --}}
            <nav class="flex flex-wrap items-center md:ml-auto md:mr-auto">
                <x-core.header.navigation />
            </nav>
            {{-- Header Side Content // Button --}}
            <x-core.header.content />
        </div>
    </x-layout.content>
</header>

@if($navbar->fixed)
<div id="layout-breaker" class="py-12"></div>
@endif

@endif