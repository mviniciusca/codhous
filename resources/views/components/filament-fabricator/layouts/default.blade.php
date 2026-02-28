@props(['page'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title ?? 'Budget' }}</title>
    <style>[x-cloak] { display: none !important; }</style>
    @livewireStyles
    @filamentStyles
    @vite('resources/css/app.css')
</head>
<body class="antialiased">
    <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

    @livewireScripts
    @filamentScripts
    @vite('resources/js/app.js')
    @livewire('notifications')
</body>
</html>
