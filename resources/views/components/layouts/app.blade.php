@props([
    'title' => null,
    'meta' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <x-site-head :title="$title" />
    {{ $meta }}

    @php
        $websiteSettings = \App\Models\Setting::get('website', []);
        $primaryColor = data_get($websiteSettings, 'primary_color', '239 68 68');
    @endphp
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-background text-foreground flex flex-col min-h-screen">
    
    <x-site-header />   
    

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <x-site-footer />
    <x-site-whatsapp />
    <x-site-alerts />

    @livewireScripts
    @stack('scripts')
</body>
</html>
