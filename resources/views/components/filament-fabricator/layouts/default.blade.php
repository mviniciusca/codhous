@props(['page'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('theme') == 'dark' ? 'dark' : '' }}">

<head>
    <x-layout.meta />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body class="bg-primary-50 text-primary-600 dark:bg-primary-900 dark:text-primary-300">
    @livewire('notifications')
    <div class="main-base">

        {{-- Application --}}
        @if(!$maintenance_mode || $discovery_mode && auth()->hasUser())
        <x-core.header />
        <x-layout.background />
        <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
        <x-ui.whatsapp />
        @endif

        {{-- Maintenance Mode Module --}}
        @if($maintenance_mode && (!$discovery_mode || !auth()->hasUser()))
        <x-maintenance.section />
        @endif
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    @if(!$maintenance_mode || ($maintenance_mode && $discovery_mode && auth()->hasUser()))
    <x-layout.footer />
    @endif

    @filamentScripts
    @vite('resources/js/app.js')
</body>

</html>