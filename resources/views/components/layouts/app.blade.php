@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'ogImage' => null,
    'meta' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <x-site-head :title="$title" :description="$description" :keywords="$keywords" :ogImage="$ogImage" />
    {{ $meta }}

    @php
        $websiteSettings = \App\Models\Setting::get('website', []);
        $primaryColor = data_get($websiteSettings, 'primary_color', '239 68 68');
        $theme = data_get($websiteSettings, 'theme', 'default');
    @endphp
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --primary-400: {{ $primaryColor }};
            --primary-500: {{ $primaryColor }};
            --primary-600: {{ $primaryColor }};
            --gray-400: 156, 163, 175;
            --gray-500: 107, 114, 128;
            --gray-600: 75, 85, 99;
        }
    </style>

    @livewireStyles
    @filamentStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-background text-foreground flex flex-col min-h-screen">
    
    <x-site-header :theme="$theme" />   
    

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <x-site-footer :theme="$theme" />
    <x-site-whatsapp />
    <x-site-alerts />

    @livewireScripts
    @filamentScripts
    @stack('scripts')
</body>
</html>
