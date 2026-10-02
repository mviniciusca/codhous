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
@endphp

{{-- ═══════════════════════════════════════════════════════════
     DEFAULT THEME — Colorful accent top bar (primary color)
     + white main bar with logo + nav pills + dual CTA
     ═══════════════════════════════════════════════════════════ --}}
<header x-data="{ open: false }" id="site-header" class="relative z-50">

    {{-- ACCENT BAR: brand color strip with contact info --}}
    <div class="bg-primary py-2">
        <div class="mx-auto max-w-7xl px-4 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-5 text-[11px] font-semibold text-primary-foreground/80">
                @if($addrStr)
                    <span class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-primary-foreground/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $addrStr }}
                    </span>
                @endif
                @if($companyPhone)
                    <span class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-primary-foreground/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $companyPhone }}
                    </span>
                @endif
            </div>
            <div class="hidden md:flex items-center gap-3 text-[11px] font-semibold text-primary-foreground/80">
                <svg class="h-3.5 w-3.5 text-primary-foreground/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ data_get($company, 'opening_hours', 'Seg – Sex: 08:00 – 18:00') }}
            </div>
        </div>
    </div>

    {{-- MAIN BAR --}}
    <div class="bg-white border-b border-border shadow-sm">
        <div class="mx-auto max-w-7xl px-4 lg:px-8 flex items-center gap-8 py-3">

            {{-- Logo --}}
            <a href="/" class="flex items-center flex-shrink-0">
                @if($websiteLogo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}"
                         alt="{{ $websiteName }}"
                         class="h-14 lg:h-16 w-auto object-contain">
                @else
                    <span class="font-mono text-xl font-black tracking-tight text-zinc-950">{{ $websiteName }}</span>
                @endif
            </a>

            {{-- Desktop nav – pill style --}}
            <nav class="hidden lg:flex items-center gap-1 flex-1">
                @foreach($navigation as $item)
                    @php
                        $href = data_get($item, 'url', '/');
                        $isActive = ('/' . $currentPath) === $href;
                    @endphp
                    <a href="{{ $href }}"
                       class="px-4 py-2 rounded-full text-[13px] font-semibold tracking-wide transition-all duration-150 {{ $isActive ? 'bg-primary/10 text-primary' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        {{ data_get($item, 'label') }}
                    </a>
                @endforeach
            </nav>

            {{-- Dual CTA --}}
            <div class="hidden lg:flex items-center gap-3 flex-shrink-0">
                <a href="/#calculadora"
                   class="flex items-center gap-2 rounded-full border-2 border-primary px-5 py-2 text-[12px] font-bold tracking-wide text-primary transition-all hover:bg-primary hover:text-primary-foreground">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M9 7h9V4a2 2 0 00-2-2H9a2 2 0 00-2 2v3m6 0H9"/></svg>
                    Calculadora
                </a>
                <a href="#orcamento"
                   class="flex items-center gap-2 rounded-full bg-primary px-6 py-2.5 text-[12px] font-bold tracking-wide text-primary-foreground shadow-md shadow-primary/25 transition-all hover:bg-primary/90 hover:shadow-primary/40 hover:-translate-y-0.5 active:translate-y-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    Orçamento Grátis
                </a>
            </div>

            {{-- MOBILE BURGER --}}
            <button @click="open = !open"
                    class="lg:hidden ml-auto flex items-center justify-center w-10 h-10 rounded-full border-2 border-primary text-primary hover:bg-primary hover:text-primary-foreground transition-all">
                <svg x-show="!open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden bg-white border-b border-zinc-100 shadow-lg">
        <nav class="mx-auto max-w-7xl px-4 py-5 flex flex-col gap-1">
            @foreach($navigation as $item)
                @php
                    $href = data_get($item, 'url', '/');
                    $isActive = ('/' . $currentPath) === $href;
                @endphp
                <a href="{{ $href }}" @click="open=false"
                   class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[14px] font-semibold transition-all {{ $isActive ? 'bg-primary/10 text-primary' : 'text-zinc-700 hover:bg-zinc-50' }}">
                    @if($isActive)<span class="h-2 w-2 rounded-full bg-primary flex-shrink-0"></span>@endif
                    {{ data_get($item, 'label') }}
                </a>
            @endforeach
            <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-zinc-100">
                <a href="/#calculadora" @click="open=false"
                   class="flex items-center justify-center gap-2 rounded-full border-2 border-primary py-3 text-sm font-bold text-primary hover:bg-primary hover:text-primary-foreground transition-colors">
                    Calculadora
                </a>
                <a href="#orcamento" @click="open=false"
                   class="flex items-center justify-center gap-2 rounded-full bg-primary py-3 text-sm font-bold text-primary-foreground hover:bg-primary/90 transition-colors">
                    Orçamento
                </a>
            </div>
        </nav>
    </div>
</header>
