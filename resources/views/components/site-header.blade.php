@php
    $website  = \App\Models\Setting::get('website', []);
    $company  = \App\Models\Setting::get('company', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');

    $navigation = data_get($website, 'navigation', []);
    if (empty($navigation)) {
        $dynamicPages = \App\Models\Page::visible()->inMenu()->orderBy('sort_order')->get();
        $navigation   = $dynamicPages->map(fn ($p) => [
            'label' => $p->title,
            'url'   => $p->slug === '/' ? '/' : '/' . ltrim($p->slug, '/'),
        ])->toArray();
    }

    $companyPhone       = data_get($company, 'phone', '');
    $companyPhoneDigits = $companyPhone ? preg_replace('/\D/', '', $companyPhone) : '';
    $companyPhoneTel    = $companyPhoneDigits && in_array(strlen($companyPhoneDigits), [10, 11], true)
        ? '55' . $companyPhoneDigits
        : $companyPhoneDigits;

    $addr = data_get($company, 'address', []);
    $addrStr = is_array($addr)
        ? implode(', ', array_filter([
            data_get($addr, 'street') . (data_get($addr, 'number') ? ', ' . data_get($addr, 'number') : ''),
            data_get($addr, 'neighborhood'),
            data_get($addr, 'city') . (data_get($addr, 'state') ? ' - ' . data_get($addr, 'state') : ''),
          ]))
        : (string) $addr;

    $currentPath = request()->path();
@endphp

<header class="relative z-50 m-0 p-0 border-b border-border shadow-sm" id="site-header">
    {{-- Top Bar --}}
    <div class="bg-primary py-2.5">
        <div class="mx-auto max-w-7xl px-4 lg:px-8 flex justify-between items-center text-[10px] lg:text-[11px] font-mono font-bold tracking-wide text-primary-foreground">
            <div class="flex items-center gap-2">
                <i data-lucide="map-pin" class="h-3.5 w-3.5 text-primary-foreground/70"></i>
                <span>{{ $addrStr }}</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                @if($companyPhone)
                    <div class="flex items-center gap-2">
                        <i data-lucide="phone" class="h-3.5 w-3.5 text-primary-foreground/70"></i>
                        <span>{{ $companyPhone }}</span>
                    </div>
                @endif
                @php
                    $mainHours = data_get($company, 'opening_hours', 'Seg - Sex: 08:00 - 18:00');
                @endphp
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="h-3.5 w-3.5 text-primary-foreground/70"></i>
                    <span>{{ $mainHours }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Nav Bar --}}
    <div class="bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 lg:px-8">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary">
                    <i data-lucide="truck" class="h-4.5 w-4.5 text-primary-foreground"></i>
                </div>
                <span class="font-mono text-xl font-bold tracking-tighter text-zinc-950">{{ $websiteName }}</span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-10 md:flex">
                @foreach($navigation as $item)
                    @php
                        $href    = data_get($item, 'url', '/');
                        $label   = data_get($item, 'label', '');
                        $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                    @endphp
                    <a href="{{ $href }}"
                       class="font-mono text-[13px] font-bold tracking-wide transition-all hover:text-primary {{ $isActive ? 'text-primary' : 'text-zinc-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            {{-- Desktop CTA --}}
            <div class="hidden items-center gap-4 md:flex">
                <a href="/#calculadora"
                   class="flex items-center gap-2 font-mono rounded-md border-2 border-primary px-4 py-2 text-[11px] font-bold tracking-wide text-primary transition-all hover:bg-primary hover:text-primary-foreground hover:scale-105 active:scale-95">
                    <i data-lucide="calculator" class="h-4 w-4"></i>
                    Calculadora
                </a>
                <a href="#orcamento"
                   class="flex items-center gap-2 font-mono rounded-md bg-primary px-5 py-2.5 text-[11px] font-bold tracking-wide text-primary-foreground transition-all hover:bg-primary/90 hover:scale-105 active:scale-95 shadow-md">
                    <i data-lucide="file-text" class="h-4 w-4"></i>
                    Orçamento Grátis
                </a>
            </div>

            {{-- Mobile burger --}}
            <button onclick="toggleMobileMenu()"
                    class="inline-flex items-center justify-center rounded-md p-2 text-zinc-900 md:hidden"
                    aria-label="Abrir menu" id="mobile-menu-btn">
                <i data-lucide="menu" class="h-6 w-6" id="menu-icon-open"></i>
                <i data-lucide="x"    class="h-6 w-6 hidden" id="menu-icon-close"></i>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div class="hidden border-t border-border bg-white px-4 pb-6 md:hidden shadow-xl" id="mobile-menu">
        <nav class="flex flex-col gap-2 pt-4">
            @foreach($navigation as $item)
                @php
                    $href    = data_get($item, 'url', '/');
                    $label   = data_get($item, 'label', '');
                    $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                @endphp
                <a href="{{ $href }}" onclick="closeMobileMenu()"
                   class="font-mono rounded-md px-4 py-3 text-sm font-bold tracking-wide transition-all hover:bg-zinc-50 hover:text-primary {{ $isActive ? 'text-primary bg-zinc-50' : 'text-zinc-600' }}">
                    {{ $label }}
                </a>
            @endforeach

            <div class="mt-6 pt-6 border-t border-zinc-100">
                <a href="#orcamento" onclick="closeMobileMenu()"
                   class="font-mono rounded-md bg-primary block w-full py-4 text-center text-xs font-bold tracking-wide text-primary-foreground transition-all">
                    Solicitar Orçamento
                </a>
            </div>
        </nav>
    </div>
</header>
