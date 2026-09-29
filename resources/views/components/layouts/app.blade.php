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
        $headerTheme = data_get($websiteSettings, 'header_theme', 'default');
        $footerTheme = data_get($websiteSettings, 'footer_theme', 'default');
    @endphp
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
        }
    </style>

    @livewireStyles
    @filamentStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-background text-foreground flex flex-col min-h-screen">
    
    <x-site-header :theme="$headerTheme" />   
    

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <x-site-footer :theme="$footerTheme" />
    <x-site-whatsapp />
    <x-site-alerts />

    @livewireScripts
    @filamentScripts
    @stack('scripts')
</body>
</html>
