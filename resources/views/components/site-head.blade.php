@php
    $website = \App\Models\Setting::get('website', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $websiteTitle = data_get($website, 'title', 'Concreto Usinado & Equipamentos');
    $websiteDescription = data_get($website, 'description', 'Concreto usinado de alta qualidade e locação de equipamentos.');
    $scripts = data_get($website, 'scripts', []);
    $googleFontsUrl = data_get($scripts, 'google_fonts_url', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap');
    $headScripts = data_get($scripts, 'head_scripts');
@endphp

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<meta name="theme-color" content="#e5b800">
<meta name="description" content="{{ $websiteDescription }}">
<title>{{ $websiteName }} | {{ $websiteTitle }}</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="{{ $googleFontsUrl }}" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

@if($headScripts)
    {!! $headScripts !!}
@endif
