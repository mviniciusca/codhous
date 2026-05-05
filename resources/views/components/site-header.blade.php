@php
    $website  = \App\Models\Setting::get('website', []);
    $company  = \App\Models\Setting::get('company', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');

    // Prioridade: navegação manual no Settings → páginas cadastradas no resource
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

    $currentPath = request()->path();
@endphp

<header class="fixed top-0 left-0 right-0 z-50 bg-card/90 backdrop-blur-md border-b border-border" id="site-header">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 lg:px-8">

        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                <i data-lucide="truck" class="h-5 w-5 text-primary-foreground"></i>
            </div>
            <span class="font-mono text-xl font-bold tracking-tight text-foreground">{{ $websiteName }}</span>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-8 md:flex">
            @foreach($navigation as $item)
                @php
                    $href    = data_get($item, 'url', '/');
                    $label   = data_get($item, 'label', '');
                    $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                @endphp
                <a href="{{ $href }}"
                   class="text-sm font-medium transition-colors hover:text-primary {{ $isActive ? 'text-primary' : 'text-foreground' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        {{-- Desktop CTA --}}
        <div class="hidden items-center gap-2 md:flex">
            @if($companyPhoneTel)
                <a href="tel:{{ $companyPhoneTel }}"
                   class="inline-flex items-center gap-2 rounded-md border border-border bg-transparent px-4 py-2.5 text-sm font-medium text-foreground transition-colors hover:bg-muted hover:text-primary"
                   aria-label="Ligar para a empresa">
                    <i data-lucide="phone" class="h-4 w-4"></i>
                    <span>Fale conosco</span>
                </a>
            @endif
            <a href="#orcamento"
               class="rounded-md bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90">
                Solicitar Orçamento
            </a>
        </div>

        {{-- Mobile burger --}}
        <button onclick="toggleMobileMenu()"
                class="inline-flex items-center justify-center rounded-md p-2 text-foreground md:hidden"
                aria-label="Abrir menu" id="mobile-menu-btn">
            <i data-lucide="menu" class="h-6 w-6" id="menu-icon-open"></i>
            <i data-lucide="x"    class="h-6 w-6 hidden" id="menu-icon-close"></i>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div class="hidden border-t border-border bg-card px-4 pb-4 md:hidden" id="mobile-menu">
        <nav class="flex flex-col gap-1 pt-3">
            @foreach($navigation as $item)
                @php
                    $href    = data_get($item, 'url', '/');
                    $label   = data_get($item, 'label', '');
                    $isActive = ('/' . $currentPath) === $href || $currentPath === ltrim($href, '/');
                @endphp
                <a href="{{ $href }}" onclick="closeMobileMenu()"
                   class="rounded-md px-3 py-2.5 text-sm font-medium transition-colors hover:bg-muted hover:text-primary {{ $isActive ? 'text-primary bg-muted' : 'text-foreground' }}">
                    {{ $label }}
                </a>
            @endforeach

            <div class="mt-4 flex flex-col gap-2 border-t border-border pt-4">
                @if($companyPhoneTel)
                    <a href="tel:{{ $companyPhoneTel }}" onclick="closeMobileMenu()"
                       class="flex items-center justify-center gap-2 rounded-md border border-border bg-transparent px-5 py-2.5 text-sm font-medium text-foreground transition-colors hover:bg-muted hover:text-primary">
                        <i data-lucide="phone" class="h-4 w-4"></i>
                        Fale conosco: {{ $companyPhone }}
                    </a>
                @endif
                <a href="#orcamento" onclick="closeMobileMenu()"
                   class="rounded-md bg-primary px-5 py-2.5 text-center text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90">
                    Solicitar Orçamento
                </a>
            </div>
        </nav>
    </div>
</header>
