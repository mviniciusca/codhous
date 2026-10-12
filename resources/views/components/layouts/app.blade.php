@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'ogImage' => null,
    'meta' => null,
    'builderPreview' => false,
    'sitePreview' => false,
    // Prévia de alertas do painel: coleção de alertas a exibir no lugar dos ativos
    'alertPreview' => null,
    // Sem cabeçalho e rodapé do site (prévia isolada de uma seção)
    'bare' => false,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <x-site-head :title="$title" :description="$description" :keywords="$keywords" :ogImage="$ogImage" />
    {{ $meta }}

    @php
        $websiteSettings = \App\Models\Setting::get('website', []);
        $primaryColor = data_get($websiteSettings, 'primary_color', '249 115 22');
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
    
    @unless($bare)
        <x-site-header :theme="$theme" />
    @endunless
    

    <main class="flex-grow">
        {{ $slot }}
    </main>

    @unless($bare)
        <x-site-footer :theme="$theme" />
    @endunless
    @unless($builderPreview)
        <x-site-whatsapp />
        <x-site-scroll-to-top />
        {{-- Na prévia do Design e SEO, avisos e cookies cobririam o que está sendo ajustado --}}
        @if($alertPreview !== null)
            @include('filament.alerts.preview-bridge', ['alerts' => $alertPreview])
        @elseif(! $sitePreview)
            <x-site-alerts />
            <x-site-cookie-consent />
        @endif
    @endunless

    @if($sitePreview || $alertPreview !== null)
        @include('filament.design.preview-bridge')
    @endif

    @livewireScripts
    @filamentScripts
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Outras inicializações globais se houver
        });
    </script>
</body>
</html>
