@props(['page'])
<!DOCTYPE html>
<html lang="en">

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
        @livewire('darkmode')
        <x-core.header />
        <x-layout.background />
        <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <x-layout.footer />
    @filamentScripts
    @vite('resources/js/app.js')
</body>

</html>
