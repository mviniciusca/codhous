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

@if($theme === 'creative')
{{-- ═══════════════════════════════════════════════════════════
     CREATIVE THEME — Sticky dark ribbon, logo centered,
     nav split left/right, neon accent top border, pill CTA
     ═══════════════════════════════════════════════════════════ --}}
<header
    id="site-header"
    x-data="{ open: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 40)"
    :class="scrolled ? 'shadow-[0_0_60px_rgba(0,0,0,0.6)]' : ''"
    class="fixed top-0 inset-x-0 z-50 transition-shadow duration-500"
>
    {{-- Neon accent line on top --}}
    <div class="h-[3px] w-full bg-gradient-to-r from-transparent via-primary to-transparent"></div>

    <div class="bg-zinc-950/80 backdrop-blur-xl border-b border-white/[0.06]">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="relative flex h-20 items-center justify-between">

                {{-- LEFT NAV (first half) --}}
                <nav class="hidden lg:flex flex-1 items-center gap-8">
                    @foreach(array_slice($navigation, 0, (int)ceil(count($navigation)/2)) as $item)
                        @php
                            $href = data_get($item, 'url', '/');
                            $isActive = ('/' . $currentPath) === $href;
                        @endphp
                        <a href="{{ $href }}"
                           class="relative group text-[11px] font-black uppercase tracking-[0.18em] transition-colors duration-200 {{ $isActive ? 'text-primary' : 'text-zinc-400 hover:text-white' }}">
                            {{ data_get($item, 'label') }}
                            <span class="absolute -bottom-0.5 left-0 h-[2px] w-0 bg-primary transition-all duration-300 group-hover:w-full {{ $isActive ? '!w-full' : '' }}"></span>
                        </a>
                    @endforeach
                </nav>

                {{-- CENTER LOGO --}}
                <a href="/" class="absolute left-1/2 -translate-x-1/2 flex items-center justify-center">
                    @if($websiteLogo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}"
                             alt="{{ $websiteName }}"
                             class="h-16 lg:h-20 w-auto object-contain brightness-0 invert">
                    @else
                        <span class="font-mono text-2xl font-black tracking-[-0.03em] text-white">
                            {{ $websiteName }}
                        </span>
                    @endif
                </a>

                {{-- RIGHT NAV (second half) + CTA --}}
                <div class="hidden lg:flex flex-1 items-center justify-end gap-8">
                    @foreach(array_slice($navigation, (int)ceil(count($navigation)/2)) as $item)
                        @php
                            $href = data_get($item, 'url', '/');
                            $isActive = ('/' . $currentPath) === $href;
                        @endphp
                        <a href="{{ $href }}"
                           class="relative group text-[11px] font-black uppercase tracking-[0.18em] transition-colors duration-200 {{ $isActive ? 'text-primary' : 'text-zinc-400 hover:text-white' }}">
                            {{ data_get($item, 'label') }}
                            <span class="absolute -bottom-0.5 left-0 h-[2px] w-0 bg-primary transition-all duration-300 group-hover:w-full {{ $isActive ? '!w-full' : '' }}"></span>
                        </a>
                    @endforeach
                    <a href="#orcamento"
                       class="rounded-full border border-primary/60 bg-primary/10 px-6 py-2.5 text-[11px] font-black uppercase tracking-[0.15em] text-primary backdrop-blur-sm transition-all duration-200 hover:bg-primary hover:text-primary-foreground hover:border-primary hover:scale-105">
                        Orçamento
                    </a>
                </div>

                {{-- MOBILE BURGER --}}
                <button @click="open = !open"
                        class="lg:hidden flex items-center justify-center w-10 h-10 rounded-lg border border-white/10 text-zinc-300 hover:border-primary/60 hover:text-primary transition-all">
                    <svg x-show="!open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
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
         class="lg:hidden bg-zinc-950 border-b border-white/10">
        <nav class="mx-auto max-w-7xl px-4 py-6 flex flex-col gap-1">
            @foreach($navigation as $item)
                @php
                    $href = data_get($item, 'url', '/');
                    $isActive = ('/' . $currentPath) === $href;
                @endphp
                <a href="{{ $href }}" @click="open=false"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-bold uppercase tracking-wider transition-all {{ $isActive ? 'text-primary bg-primary/10' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    @if($isActive)<span class="h-1.5 w-1.5 rounded-full bg-primary flex-shrink-0"></span>@endif
                    {{ data_get($item, 'label') }}
                </a>
            @endforeach
            <div class="mt-4 pt-4 border-t border-white/10">
                <a href="#orcamento" @click="open=false"
                   class="block w-full text-center rounded-full bg-primary py-3 text-sm font-bold tracking-wider text-primary-foreground hover:bg-primary/90 transition-colors">
                    Solicitar Orçamento
                </a>
            </div>
        </nav>
    </div>
</header>
<div class="h-[83px]"></div>{{-- Spacer to compensate fixed header --}}

@elseif($theme === 'corporate')
{{-- ═══════════════════════════════════════════════════════════
     CORPORATE THEME — Two-tier header:
     Tier 1 (dark): contact info + social pill
     Tier 2 (white): Logo left + full nav right + CTA button
     ═══════════════════════════════════════════════════════════ --}}
<header x-data="{ open: false }" id="site-header" class="relative z-50">

    {{-- TOP BAR: company contact strip --}}
    <div class="bg-zinc-900 py-2">
        <div class="mx-auto max-w-7xl px-4 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-6 text-[11px] font-medium text-zinc-400">
                @if($addrStr)
                    <span class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-primary/70 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $addrStr }}
                    </span>
                @endif
                @if($companyPhone)
                    <span class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 text-primary/70 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $companyPhone }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- MAIN BAR --}}
    <div class="bg-white border-b-2 border-zinc-100 shadow-lg">
        <div class="mx-auto max-w-7xl px-4 lg:px-8 flex items-center justify-between gap-8 py-4">

            {{-- Logo block --}}
            <a href="/" class="flex items-center gap-3 flex-shrink-0">
                @if($websiteLogo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}"
                         alt="{{ $websiteName }}"
                         class="h-14 lg:h-20 w-auto object-contain">
                @else
                    <div class="flex flex-col leading-none">
                        <span class="text-2xl font-black tracking-tight text-zinc-900">{{ $websiteName }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary">Concreto Usinado</span>
                    </div>
                @endif
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                @foreach($navigation as $item)
                    @php
                        $href = data_get($item, 'url', '/');
                        $isActive = ('/' . $currentPath) === $href;
                    @endphp
                    <a href="{{ $href }}"
                       class="relative px-4 py-2 text-[13px] font-semibold tracking-wide rounded-md transition-all duration-150 {{ $isActive ? 'bg-zinc-100 text-primary' : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900' }}">
                        {{ data_get($item, 'label') }}
                        @if($isActive)
                            <span class="absolute bottom-0 left-4 right-4 h-[2px] bg-primary rounded-full"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- CTA --}}
            <div class="hidden lg:flex items-center gap-3 flex-shrink-0">
                <a href="/#calculadora"
                   class="flex items-center gap-2 rounded-lg border-2 border-zinc-900 px-5 py-2.5 text-[13px] font-bold text-zinc-900 transition-all hover:bg-zinc-900 hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M9 7h9V4a2 2 0 00-2-2H9a2 2 0 00-2 2v3m6 0H9"/></svg>
                    Calculadora
                </a>
                <a href="#orcamento"
                   class="flex items-center gap-2 rounded-lg bg-zinc-900 px-6 py-3 text-[13px] font-bold text-white shadow-md transition-all hover:bg-primary hover:shadow-primary/20 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    Orçamento Grátis
                </a>
            </div>

            {{-- MOBILE BURGER --}}
            <button @click="open = !open"
                    class="lg:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100 text-zinc-700 hover:bg-zinc-200 transition-colors">
                <svg x-show="!open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="lg:hidden bg-white border-b-2 border-zinc-100 shadow-xl">
        <nav class="mx-auto max-w-7xl px-4 py-5 flex flex-col gap-1">
            @foreach($navigation as $item)
                @php
                    $href = data_get($item, 'url', '/');
                    $isActive = ('/' . $currentPath) === $href;
                @endphp
                <a href="{{ $href }}" @click="open=false"
                   class="flex items-center justify-between rounded-lg px-4 py-3.5 text-[14px] font-semibold border transition-all {{ $isActive ? 'border-primary/20 bg-primary/5 text-primary' : 'border-transparent text-zinc-600 hover:border-zinc-100 hover:bg-zinc-50' }}">
                    {{ data_get($item, 'label') }}
                    @if($isActive)
                        <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    @endif
                </a>
            @endforeach
            <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-zinc-100">
                <a href="/#calculadora" @click="open=false"
                   class="flex items-center justify-center gap-2 rounded-lg border-2 border-zinc-900 py-3 text-sm font-bold text-zinc-900 hover:bg-zinc-900 hover:text-white transition-colors">
                    Calculadora
                </a>
                <a href="#orcamento" @click="open=false"
                   class="flex items-center justify-center gap-2 rounded-lg bg-zinc-900 py-3 text-sm font-bold text-white hover:bg-primary transition-colors">
                    Orçamento
                </a>
            </div>
        </nav>
    </div>
</header>

@else
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
@endif
