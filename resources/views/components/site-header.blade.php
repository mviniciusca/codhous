@props(['theme' => 'default'])
@php
    $website  = \App\Models\Setting::get('website', []);
    $company  = \App\Models\Setting::get('company', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $websiteLogo = data_get($website, 'logo');

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

<header class="relative z-50 m-0 p-0" id="site-header">
    @if($theme === 'creative')
        {{-- CREATIVE HEADER (Dark/Glass, Centered Logo, No Top Bar) --}}
        <div class="bg-zinc-950/90 backdrop-blur-md border-b border-white/10 text-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 lg:px-8">
                
                {{-- Left Nav --}}
                <nav class="hidden flex-1 items-center gap-6 md:flex">
                    @foreach(array_slice($navigation, 0, ceil(count($navigation)/2)) as $item)
                        @php
                            $href = data_get($item, 'url', '/');
                            $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                        @endphp
                        <a href="{{ $href }}" class="font-mono text-[12px] font-bold tracking-widest uppercase transition-all hover:text-primary {{ $isActive ? 'text-primary' : 'text-zinc-300' }}">
                            {{ data_get($item, 'label') }}
                        </a>
                    @endforeach
                </nav>

                {{-- Centered Logo --}}
                <a href="/" class="flex flex-shrink-0 items-center justify-center mx-4">
                    @if($websiteLogo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}" alt="{{ $websiteName }}" class="h-12 w-auto object-contain brightness-0 invert">
                    @else
                        <span class="font-mono text-2xl font-bold tracking-tighter text-white">{{ $websiteName }}</span>
                    @endif
                </a>

                {{-- Right Nav & CTA --}}
                <div class="hidden flex-1 items-center justify-end gap-6 md:flex">
                    @foreach(array_slice($navigation, ceil(count($navigation)/2)) as $item)
                        @php
                            $href = data_get($item, 'url', '/');
                            $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                        @endphp
                        <a href="{{ $href }}" class="font-mono text-[12px] font-bold tracking-widest uppercase transition-all hover:text-primary {{ $isActive ? 'text-primary' : 'text-zinc-300' }}">
                            {{ data_get($item, 'label') }}
                        </a>
                    @endforeach
                    <a href="#orcamento" class="rounded-full bg-primary px-6 py-2.5 font-mono text-[11px] font-bold tracking-widest text-primary-foreground transition-all hover:bg-primary/90 hover:scale-105">
                        ORÇAMENTO
                    </a>
                </div>

                {{-- Mobile burger --}}
                <button onclick="toggleMobileMenu()" class="inline-flex items-center justify-center rounded-md p-2 text-white md:hidden" id="mobile-menu-btn">
                    <i data-lucide="menu" class="h-6 w-6" id="menu-icon-open"></i>
                    <i data-lucide="x" class="h-6 w-6 hidden" id="menu-icon-close"></i>
                </button>
            </div>
        </div>

    @elseif($theme === 'corporate')
        {{-- CORPORATE HEADER (Strict, Info on top right, Nav below) --}}
        <div class="bg-white border-b border-zinc-200 shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 lg:px-8">
                {{-- Logo --}}
                <a href="/" class="flex items-center">
                    @if($websiteLogo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}" alt="{{ $websiteName }}" class="h-10 w-auto object-contain">
                    @else
                        <span class="font-mono text-2xl font-extrabold tracking-tighter text-zinc-900 uppercase">{{ $websiteName }}</span>
                    @endif
                </a>

                {{-- Info + Nav (Desktop) --}}
                <div class="hidden md:flex flex-col items-end gap-3">
                    <div class="flex items-center gap-6 text-[11px] font-mono font-bold tracking-wide text-zinc-500">
                        @if($companyPhone)
                            <div class="flex items-center gap-1.5"><i data-lucide="phone" class="h-3.5 w-3.5 text-primary"></i> {{ $companyPhone }}</div>
                        @endif
                        <div class="flex items-center gap-1.5"><i data-lucide="mail" class="h-3.5 w-3.5 text-primary"></i> contato@empresa.com</div>
                    </div>
                    <nav class="flex items-center gap-8">
                        @foreach($navigation as $item)
                            @php
                                $href = data_get($item, 'url', '/');
                                $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                            @endphp
                            <a href="{{ $href }}" class="font-sans text-[14px] font-bold transition-all hover:text-primary {{ $isActive ? 'text-primary' : 'text-zinc-700' }}">
                                {{ data_get($item, 'label') }}
                            </a>
                        @endforeach
                        <a href="#orcamento" class="ml-4 rounded bg-zinc-900 px-5 py-2 font-sans text-[13px] font-bold text-white transition-all hover:bg-zinc-800">
                            SOLICITAR PROPOSTA
                        </a>
                    </nav>
                </div>

                {{-- Mobile burger --}}
                <button onclick="toggleMobileMenu()" class="inline-flex items-center justify-center rounded-md p-2 text-zinc-900 md:hidden" id="mobile-menu-btn">
                    <i data-lucide="menu" class="h-6 w-6" id="menu-icon-open"></i>
                    <i data-lucide="x" class="h-6 w-6 hidden" id="menu-icon-close"></i>
                </button>
            </div>
        </div>

    @else
        {{-- DEFAULT HEADER (Top Bar + Main Bar) --}}
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
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="h-3.5 w-3.5 text-primary-foreground/70"></i>
                        <span>{{ data_get($company, 'opening_hours', 'Seg - Sex: 08:00 - 18:00') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white border-b border-border shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 lg:px-8">
                <a href="/" class="flex items-center gap-2.5">
                    @if($websiteLogo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}" alt="{{ $websiteName }}" class="h-10 w-auto object-contain">
                    @else
                        <span class="font-mono text-xl font-bold tracking-tighter text-zinc-950">{{ $websiteName }}</span>
                    @endif
                </a>
                <nav class="hidden items-center gap-10 md:flex">
                    @foreach($navigation as $item)
                        @php
                            $href = data_get($item, 'url', '/');
                            $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                        @endphp
                        <a href="{{ $href }}" class="font-mono text-[13px] font-bold tracking-wide transition-all hover:text-primary {{ $isActive ? 'text-primary' : 'text-zinc-600' }}">
                            {{ data_get($item, 'label') }}
                        </a>
                    @endforeach
                </nav>
                <div class="hidden items-center gap-4 md:flex">
                    <a href="/#calculadora" class="flex items-center gap-2 font-mono rounded-md border-2 border-primary px-4 py-2 text-[11px] font-bold tracking-wide text-primary transition-all hover:bg-primary hover:text-primary-foreground hover:scale-105 active:scale-95">
                        <i data-lucide="calculator" class="h-4 w-4"></i> Calculadora
                    </a>
                    <a href="#orcamento" class="flex items-center gap-2 font-mono rounded-md bg-primary px-5 py-2.5 text-[11px] font-bold tracking-wide text-primary-foreground transition-all hover:bg-primary/90 hover:scale-105 active:scale-95 shadow-md">
                        <i data-lucide="file-text" class="h-4 w-4"></i> Orçamento Grátis
                    </a>
                </div>
                <button onclick="toggleMobileMenu()" class="inline-flex items-center justify-center rounded-md p-2 text-zinc-900 md:hidden" id="mobile-menu-btn">
                    <i data-lucide="menu" class="h-6 w-6" id="menu-icon-open"></i>
                    <i data-lucide="x" class="h-6 w-6 hidden" id="menu-icon-close"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Mobile menu (Shared across all themes) --}}
    <div class="hidden border-t border-border bg-white px-4 pb-6 md:hidden shadow-xl" id="mobile-menu">
        <nav class="flex flex-col gap-2 pt-4">
            @foreach($navigation as $item)
                @php
                    $href = data_get($item, 'url', '/');
                    $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                @endphp
                <a href="{{ $href }}" onclick="closeMobileMenu()" class="font-mono rounded-md px-4 py-3 text-sm font-bold tracking-wide transition-all hover:bg-zinc-50 hover:text-primary {{ $isActive ? 'text-primary bg-zinc-50' : 'text-zinc-600' }}">
                    {{ data_get($item, 'label') }}
                </a>
            @endforeach
            <div class="mt-6 pt-6 border-t border-zinc-100">
                <a href="#orcamento" onclick="closeMobileMenu()" class="font-mono rounded-md bg-primary block w-full py-4 text-center text-xs font-bold tracking-wide text-primary-foreground transition-all">
                    Solicitar Orçamento
                </a>
            </div>
        </nav>
    </div>
</header>
