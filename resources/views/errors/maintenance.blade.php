<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção | Codhous</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
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
    <div class="max-w-md w-full">
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
                <i data-lucide="message-circle" class="h-4 w-4"></i>
                Falar no WhatsApp
            </a>
            
            <div class="pt-6 border-t border-zinc-100">
                <p class="text-xs text-zinc-400">
                    &copy; {{ date('Y') }} Codhous. Tecnologia para construção.
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
