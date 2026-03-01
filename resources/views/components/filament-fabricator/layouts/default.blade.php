@props([
    'title' => null,
    'meta' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <x-site-head :title="$title" />
    {{ $meta }}
    @livewireStyles
</head>
<body class="font-sans antialiased bg-background text-foreground">
    <x-site-header />

    <main>
        <x-section-home />
        {{ $slot }}
    </main>

    <x-site-footer />
    <x-site-whatsapp />

    @livewireScripts
</body>
</html>
