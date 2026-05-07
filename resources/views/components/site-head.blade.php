@props([
    'title' => null,
    'description' => null,
])

@php
    $website = \App\Models\Setting::get('website', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $websiteTitle = data_get($website, 'title', 'Concreto Usinado & Equipamentos');
    $defaultDescription = data_get($website, 'description', 'Concreto usinado de alta qualidade e locação de equipamentos.');
    
    // Usa a descrição passada pela prop ou a do banco
    $finalDescription = $description ?: $defaultDescription;
    
    $scripts = data_get($website, 'scripts', []);
    $fontFamily = data_get($scripts, 'google_font_family', 'Inter');
    
    // Constrói a URL do Google Fonts dinamicamente
    $fonts = ['Inter:wght@400;500;600;700'];
    if ($fontFamily && $fontFamily !== 'Inter') {
        $fonts[] = urlencode($fontFamily) . ':wght@400;500;600;700';
    }
    $googleFontsUrl = "https://fonts.googleapis.com/css2?family=" . implode('&family=', $fonts) . "&display=swap";
    
    $headScripts = data_get($scripts, 'head_scripts');
@endphp

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<meta name="theme-color" content="#e5b800">
<meta name="description" content="{{ $finalDescription }}">
<title>{{ $websiteName }} | {{ $websiteTitle }}</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="{{ $googleFontsUrl }}" rel="stylesheet">

<!-- Swiper Slider -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- GLightbox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    :root {
        --font-titles: '{{ $fontFamily }}', sans-serif;
    }
    h1, h2, h3, h4, h5, h6, .font-title, header, header *, nav, button, .btn, .font-mono {
        font-family: var(--font-titles) !important;
    }
</style>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

@if($headScripts)
    {!! $headScripts !!}
@endif
