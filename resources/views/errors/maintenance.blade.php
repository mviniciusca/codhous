<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção | {{ \App\Models\Setting::get('website.name', 'Codhous') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #ffffff;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-8">
    <div class="max-w-md w-full text-center">
        {{-- Indicador de Status Minimalista --}}
        <div class="mb-8 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200">
            <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Modo Manutenção</span>
        </div>

        <h1 class="text-3xl font-semibold tracking-tight text-zinc-900 mb-4">
            Voltaremos em instantes.
        </h1>
        
        <p class="text-zinc-500 leading-relaxed mb-10">
            {{ $message }}
        </p>

        <div class="flex flex-col gap-4">
            <a href="https://wa.me/5511999999999" class="inline-flex items-center justify-center gap-2 bg-[#25D366] text-white px-6 py-3 rounded-lg font-bold shadow-sm transition-all hover:bg-[#22c35e] active:scale-[0.98]">
                <ion-icon name="logo-whatsapp" class="h-5 w-5"></ion-icon>
                Falar no WhatsApp
            </a>
            
            @php
                $socials = \App\Models\Setting::get('website.social_networks');
            @endphp
            
            @if(is_array($socials) && count(array_filter($socials)) > 0)
            <div class="flex items-center justify-center gap-6 py-6">
                @foreach(['instagram', 'facebook', 'linkedin', 'twitter', 'youtube'] as $network)
                    @if(!empty($socials[$network]))
                        @php
                            $url = $socials[$network];
                            if (!str_contains($url, 'http')) {
                                $baseUrl = match($network) {
                                    'instagram' => 'https://instagram.com/',
                                    'facebook' => 'https://facebook.com/',
                                    'linkedin' => 'https://linkedin.com/in/',
                                    'twitter' => 'https://x.com/',
                                    'youtube' => 'https://youtube.com/',
                                    default => ''
                                };
                                $url = $baseUrl . ltrim($url, '@');
                            }
                            $iconName = match($network) {
                                'instagram' => 'logo-instagram',
                                'facebook' => 'logo-facebook',
                                'linkedin' => 'logo-linkedin',
                                'twitter' => 'logo-twitter',
                                'youtube' => 'logo-youtube',
                                default => 'share-social-outline'
                            };
                        @endphp
                        <a href="{{ $url }}" target="_blank" class="text-zinc-400 hover:text-zinc-900 transition-colors">
                            <ion-icon name="{{ $iconName }}" class="h-6 w-6"></ion-icon>
                        </a>
                    @endif
                @endforeach
            </div>
            @endif
            
            <div class="pt-8 border-t border-zinc-100">
                <p class="text-xs text-zinc-400">
                    &copy; {{ date('Y') }} {{ \App\Models\Setting::get('website.name', 'Codhous') }}
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
