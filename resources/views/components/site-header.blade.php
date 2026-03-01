@php
    $website = \App\Models\Setting::get('website', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $navigation = data_get($website, 'navigation', []);
@endphp

<header class="fixed top-0 left-0 right-0 z-50 bg-card/90 backdrop-blur-md border-b border-border" id="site-header">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 lg:px-8">
        <a href="/" class="flex items-center gap-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                <i data-lucide="truck" class="h-5 w-5 text-primary-foreground"></i>
            </div>
            <span class="font-mono text-xl font-bold tracking-tight text-foreground">{{ $websiteName }}</span>
        </a>

        <nav class="hidden items-center gap-8 md:flex">
            @foreach($navigation as $item)
                <a href="{{ data_get($item, 'url', '#') }}" 
                   class="text-sm font-medium text-zinc-950 transition-colors hover:text-primary">
                   {{ data_get($item, 'label') }}
                </a>
            @endforeach
            
            @if(empty($navigation))
                <a href="#servicos" class="text-sm font-medium text-zinc-950 transition-colors hover:text-primary">Serviços</a>
                <a href="#calculadora" class="text-sm font-medium text-zinc-950 transition-colors hover:text-primary">Calculadora</a>
                <a href="#orcamento" class="text-sm font-medium text-zinc-950 transition-colors hover:text-primary">Orçamento</a>
                <a href="#diferenciais" class="text-sm font-medium text-zinc-950 transition-colors hover:text-primary">Diferenciais</a>
            @endif
        </nav>

        <a href="#orcamento" class="hidden rounded-md bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90 md:inline-flex">
            Solicitar Orçamento
        </a>

        <button onclick="toggleMobileMenu()" class="inline-flex items-center justify-center rounded-md p-2 text-zinc-950 md:hidden" aria-label="Abrir menu" id="mobile-menu-btn">
            <i data-lucide="menu" class="h-6 w-6" id="menu-icon-open"></i>
            <i data-lucide="x" class="h-6 w-6 hidden" id="menu-icon-close"></i>
        </button>
    </div>

    <div class="hidden border-t border-border bg-card px-4 pb-4 md:hidden" id="mobile-menu">
        <nav class="flex flex-col gap-3 pt-3">
            @foreach($navigation as $item)
                <a href="{{ data_get($item, 'url', '#') }}" onclick="closeMobileMenu()" 
                   class="rounded-md px-3 py-2 text-sm font-medium text-zinc-950 transition-colors hover:bg-muted hover:text-primary">
                   {{ data_get($item, 'label') }}
                </a>
            @endforeach

            @if(empty($navigation))
                <a href="#servicos" onclick="closeMobileMenu()" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-950 transition-colors hover:bg-muted hover:text-primary">Serviços</a>
                <a href="#calculadora" onclick="closeMobileMenu()" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-950 transition-colors hover:bg-muted hover:text-primary">Calculadora</a>
                <a href="#orcamento" onclick="closeMobileMenu()" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-950 transition-colors hover:bg-muted hover:text-primary">Orçamento</a>
                <a href="#diferenciais" onclick="closeMobileMenu()" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-950 transition-colors hover:bg-muted hover:text-primary">Diferenciais</a>
            @endif
            
            <a href="#orcamento" onclick="closeMobileMenu()" class="mt-2 rounded-md bg-primary px-5 py-2.5 text-center text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90">Solicitar Orçamento</a>
        </nav>
    </div>
</header>
