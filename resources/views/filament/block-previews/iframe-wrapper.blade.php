<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <x-site-head />
    
    @php
        $websiteSettings = \App\Models\Setting::get('website', []);
        $primaryColor = data_get($websiteSettings, 'primary_color', '249 115 22');
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
        body { 
            margin: 0; 
            padding: 0; 
            overflow: hidden; 
        }
        /* Bloqueia cliques para evitar navegação acidental dentro do preview */
        a, button, input, select, textarea { 
            pointer-events: none !important; 
        }
    </style>
</head>
<body class="font-sans antialiased bg-background text-foreground" style="background-color: #ffffff !important;">
    <x-render-block :type="$type" :data="$data" />
    
    <script>
        function sendHeight() {
            const height = document.documentElement.scrollHeight;
            window.parent.postMessage({ type: 'resize', height: height, id: '{{ $id }}' }, '*');
        }
        window.addEventListener('load', sendHeight);
        setTimeout(sendHeight, 500);
        setTimeout(sendHeight, 1500);
        if (window.ResizeObserver) {
            new ResizeObserver(sendHeight).observe(document.body);
        }
        
        // Lucide icons init
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
