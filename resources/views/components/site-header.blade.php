@props([
    'theme' => 'default',
])
@php
    $website     = \App\Models\Setting::get('website', []);
    $company     = \App\Models\Setting::get('company', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $websiteLogo = data_get($website, 'logo');
    $navigation  = data_get($website, 'navigation', []);
    if (empty($navigation)) {
        $dynamicPages = \App\Models\Page::visible()->inMenu()->orderBy('sort_order')->get();
        $navigation   = $dynamicPages->map(fn ($p) => [
            'label' => $p->title,
            'url'   => $p->slug === '/' ? '/' : '/' . ltrim($p->slug, '/'),
        ])->toArray();
    }
    $companyPhone       = data_get($company, 'phone', '');
    $companyPhoneDigits = $companyPhone ? preg_replace('/\D/', '', $companyPhone) : '';
    $addr               = data_get($company, 'address', []);
    $addrCity           = is_array($addr) ? data_get($addr, 'city', '') : '';
    $addrStr            = is_array($addr) 
                            ? implode(', ', array_filter([
                                data_get($addr, 'street') . (data_get($addr, 'number') ? ', ' . data_get($addr, 'number') : ''),
                                data_get($addr, 'neighborhood'),
                                data_get($addr, 'city') . (data_get($addr, 'state') ? ' - ' . data_get($addr, 'state') : ''),
                            ])) 
                            : (string) $addr;
    $currentPath        = request()->path();
    $stickyMenu         = data_get($website, 'sticky_menu', true);
@endphp

{{-- Faixa superior: contato rápido sobre o tom mais profundo da marca --}}
<div class="relative z-40 hidden bg-primary-950 text-white/75 sm:block">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-2 text-xs font-medium sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-5">
            @if($addrStr)
                <span class="flex min-w-0 items-center gap-1.5">
                    <i data-lucide="map-pin" class="h-3.5 w-3.5 shrink-0 text-primary-400"></i>
                    <span class="truncate">{{ $addrStr }}</span>
                </span>
            @endif
            @if($companyPhone)
                <a href="tel:{{ $companyPhoneDigits }}" class="flex shrink-0 items-center gap-1.5 transition-colors hover:text-white">
                    <i data-lucide="phone" class="h-3.5 w-3.5 text-primary-400"></i>
                    {{ $companyPhone }}
                </a>
            @endif
        </div>
        <span class="hidden shrink-0 items-center gap-1.5 md:flex">
            <i data-lucide="clock" class="h-3.5 w-3.5 text-primary-400"></i>
            {{ data_get($company, 'opening_hours', 'Seg – Sex: 08:00 – 18:00') }}
        </span>
    </div>
</div>

<header
    x-data="{ open: false, scrolled: false }"
    @if($stickyMenu) @scroll.window.passive="scrolled = window.scrollY > 24" @endif
    id="site-header"
    @class([
        'z-50 border-b transition-all duration-300',
        'sticky top-0' => $stickyMenu,
        'relative' => ! $stickyMenu,
    ])
    :class="scrolled ? 'border-border/70 bg-card/85 shadow-[0_8px_30px_-12px_rgb(0_0_0/0.15)] backdrop-blur-xl' : 'border-border/50 bg-card'"
>
    <div class="mx-auto flex max-w-7xl items-center gap-6 px-4 transition-all duration-300 sm:px-6 lg:px-8" :class="scrolled ? 'py-2' : 'py-3'">
        <a href="/" class="flex shrink-0 items-center" aria-label="{{ $websiteName }} — início">
            @if($websiteLogo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}" alt="{{ $websiteName }}"
                     class="w-auto object-contain transition-all duration-300" :class="scrolled ? 'h-11 lg:h-12' : 'h-12 lg:h-14'">
            @else
                <span class="font-mono text-xl font-bold tracking-tight text-foreground">{{ $websiteName }}</span>
            @endif
        </a>

        {{-- Navegação desktop --}}
        <nav class="hidden flex-1 items-center gap-0.5 lg:flex" aria-label="Principal">
            @foreach($navigation as $item)
                @php
                    $href = data_get($item, 'url', '/');
                    $isActive = ('/' . $currentPath) === $href || ($currentPath === '/' && $href === '/');
                @endphp
                <a href="{{ $href }}" @if($isActive) aria-current="page" @endif
                   @class([
                       'relative rounded-full px-3.5 py-2 text-[13px] font-semibold transition-colors duration-200',
                       'bg-primary-500/10 text-primary-ink' => $isActive,
                       'text-foreground/70 hover:bg-muted hover:text-foreground' => ! $isActive,
                   ])>
                    {{ data_get($item, 'label') }}
                </a>
            @endforeach
        </nav>

        <div class="hidden shrink-0 items-center gap-2 lg:flex">
            <x-ui.button href="/#calculadora" variant="ghost" size="sm" icon="calculator">Calculadora</x-ui.button>
            <x-ui.button href="#orcamento" size="sm" icon="sparkles">Orçamento grátis</x-ui.button>
        </div>

        <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="mobile-nav" aria-label="Menu"
                class="ml-auto flex h-10 w-10 items-center justify-center rounded-control border border-border text-foreground transition-colors hover:border-primary-500/40 hover:text-primary-ink lg:hidden">
            <span x-show="!open" class="flex"><i data-lucide="menu" class="h-5 w-5"></i></span>
            <span x-show="open" x-cloak class="flex"><i data-lucide="x" class="h-5 w-5"></i></span>
        </button>
    </div>

    {{-- Menu mobile --}}
    <div id="mobile-nav" x-show="open" x-cloak x-collapse @click.outside="open = false" class="border-t border-border/60 bg-card lg:hidden">
        <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-4 sm:px-6">
            @foreach($navigation as $item)
                @php
                    $href = data_get($item, 'url', '/');
                    $isActive = ('/' . $currentPath) === $href;
                @endphp
                <a href="{{ $href }}" @click="open = false"
                   @class([
                       'flex items-center justify-between rounded-control px-4 py-3 text-sm font-semibold transition-colors',
                       'bg-primary-500/10 text-primary-ink' => $isActive,
                       'text-foreground/80 hover:bg-muted' => ! $isActive,
                   ])>
                    {{ data_get($item, 'label') }}
                    <i data-lucide="chevron-right" class="h-4 w-4 opacity-40"></i>
                </a>
            @endforeach
            <div class="mt-3 grid grid-cols-2 gap-2 border-t border-border/60 pt-4">
                <x-ui.button href="/#calculadora" variant="outline" icon="calculator" @click="open = false">Calculadora</x-ui.button>
                <x-ui.button href="#orcamento" icon="sparkles" @click="open = false">Orçamento</x-ui.button>
            </div>
            @if($companyPhone)
                <a href="tel:{{ $companyPhoneDigits }}" class="mt-2 flex items-center justify-center gap-2 py-2 text-xs font-medium text-muted-foreground">
                    <i data-lucide="phone" class="h-3.5 w-3.5 text-primary-ink"></i> {{ $companyPhone }}
                </a>
            @endif
        </nav>
    </div>
</header>
