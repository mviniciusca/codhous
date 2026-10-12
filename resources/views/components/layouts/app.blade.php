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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" data-theme="{{ \App\Models\Setting::get('website.theme', 'default') ?: 'default' }}">
<head>
    <x-site-head :title="$title" :description="$description" :keywords="$keywords" :ogImage="$ogImage" />
    {{ $meta }}

    @php
        $theme = \App\Models\Setting::get('website.theme', 'default') ?: 'default';
    @endphp
    {{-- Cor de destaque e tokens derivados (App\Support\SiteTheme) --}}
    <style>{!! \App\Support\SiteTheme::css() !!}</style>

    @livewireStyles
    @filamentStyles
    @stack('styles')
    
</head>
<body class="font-sans antialiased bg-background text-foreground flex flex-col min-h-screen selection:bg-primary-500/20">
    
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
            {{-- Inclui o aviso de cookies (alerta de estilo Consentimento) --}}
            <x-site-alerts />
        @endif
    @endunless

    @if($sitePreview || $alertPreview !== null)
        @include('filament.design.preview-bridge')
    @endif

    @livewireScripts
    @filamentScripts
    @stack('scripts')

</body>
</html>
