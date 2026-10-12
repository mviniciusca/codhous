@props(['theme' => 'default'])
@php
    $website = \App\Models\Setting::get('website', []);
    $company = \App\Models\Setting::get('company', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $websiteLogo = data_get($website, 'logo');
    $websiteDescription = data_get($website, 'description', 'Excelência em concreto usinado e locação de equipamentos.');
    $scripts = data_get($website, 'scripts', []);
    // 'footer' era a chave gravada pelo painel antes de alinhar com 'footer_scripts'.
    // Na prévia do painel, scripts de terceiros (Analytics, Pixel) não rodam.
    $footerScripts = \App\Models\Setting::isPreviewing() ? null : (data_get($scripts, 'footer_scripts') ?: data_get($scripts, 'footer'));

    $navigation = data_get($website, 'navigation', []);
    if (empty($navigation)) {
        $dynamicPages = \App\Models\Page::visible()->inMenu()->orderBy('sort_order')->get();
        $navigation   = $dynamicPages->map(fn ($p) => [
            'label' => $p->title,
            'url'   => $p->slug === '/' ? '/' : '/' . ltrim($p->slug, '/'),
        ])->toArray();
    }
    
    // Trade Name from resource
    $companyName = data_get($company, 'trade_name', 'ConcretoPro');
    $companyEmail = data_get($company, 'email', 'contato@concretopro.com.br');
    $companyPhone = data_get($company, 'phone', '(11) 99999-9999');
    $companyPhoneDigits = $companyPhone ? preg_replace('/\D/', '', $companyPhone) : '';
    $companyPhoneTel = $companyPhoneDigits && in_array(strlen($companyPhoneDigits), [10, 11], true)
        ? '55' . $companyPhoneDigits
        : $companyPhoneDigits;

    // Address is an array in SettingResource, we need to format it
    $addressData = data_get($company, 'address', []);
    if (is_array($addressData) && !empty($addressData)) {
        $companyAddress = sprintf(
            '%s, %s - %s, %s - %s',
            data_get($addressData, 'street'),
            data_get($addressData, 'number'),
            data_get($addressData, 'neighborhood'),
            data_get($addressData, 'city'),
            data_get($addressData, 'state')
        );
    } else {
        $companyAddress = is_string($addressData) ? $addressData : 'Av. Industrial, 1500 - Distrito Industrial, SP';
    }
@endphp
@php
    $networks = [
        'instagram' => 'logo-instagram',
        'facebook'  => 'logo-facebook',
        'linkedin'  => 'logo-linkedin',
        'twitter'   => 'logo-twitter',
        'whatsapp'  => 'logo-whatsapp',
    ];
@endphp

@php
    $websiteMascot = data_get($website, 'mascot');
    $activeNetworks = collect($networks)
        ->map(fn ($icon, $key) => ['icon' => $icon, 'key' => $key, 'url' => data_get($website, "social_networks.$key")])
        ->filter(fn ($n) => filled($n['url']));
@endphp

{{-- Rodapé escuro, com os tons profundos da marca; .dark ajusta os tokens de texto e borda --}}
<footer class="dark relative isolate overflow-hidden bg-[oklch(0.16_0.01_250)] text-white">
    <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-primary-500 to-transparent"></div>
        <div class="absolute -top-40 left-1/2 h-80 w-[60rem] -translate-x-1/2 rounded-full bg-primary-600/15 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 pb-8 pt-14 sm:px-6 lg:px-8">
        <div @class(['grid gap-10 md:grid-cols-2', 'lg:grid-cols-[1.6fr_1fr_1fr_0.9fr]' => $websiteMascot, 'lg:grid-cols-[1.6fr_1fr_1fr]' => ! $websiteMascot])>
            <div class="flex flex-col gap-6">
                <a href="/" class="inline-flex items-center gap-2 self-start">
                    @if($websiteLogo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($websiteLogo) }}" alt="{{ $websiteName }}" class="h-16 w-auto max-w-[260px] object-contain">
                    @else
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-500 text-primary-foreground"><i data-lucide="truck" class="h-5 w-5"></i></span>
                        <span class="font-mono text-xl font-bold tracking-tight">{{ $websiteName }}</span>
                    @endif
                </a>
                <p class="max-w-sm text-sm leading-relaxed text-muted-foreground">{{ $websiteDescription }}</p>

                @if($activeNetworks->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach($activeNetworks as $network)
                            <a href="{{ $network['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network['key']) }}"
                               class="flex h-10 w-10 items-center justify-center rounded-full border border-border bg-card text-white/80 transition-all duration-300 hover:-translate-y-0.5 hover:border-primary-500 hover:bg-primary-500 hover:text-primary-foreground">
                                <ion-icon name="{{ $network['icon'] }}" class="text-lg"></ion-icon>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h4 class="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-white/50">Navegação</h4>
                <ul class="flex flex-col gap-2.5 text-sm">
                    @foreach($navigation as $item)
                        <li>
                            <a href="{{ data_get($item, 'url') }}" class="group inline-flex items-center gap-1.5 text-white/75 transition-colors hover:text-white">
                                <span class="h-px w-0 bg-primary-400 transition-all duration-300 group-hover:w-3"></span>
                                {{ data_get($item, 'label') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-white/50">Contato</h4>
                <ul class="flex flex-col gap-3 text-sm text-white/75">
                    <li class="flex items-center gap-2.5">
                        <i data-lucide="phone" class="h-4 w-4 shrink-0 text-primary-400"></i>
                        @if($companyPhoneTel)
                            <a href="tel:{{ $companyPhoneTel }}" class="transition-colors hover:text-white">{{ $companyPhone }}</a>
                        @else
                            {{ $companyPhone }}
                        @endif
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i data-lucide="mail" class="h-4 w-4 shrink-0 text-primary-400"></i>
                        <a href="mailto:{{ $companyEmail }}" class="break-all transition-colors hover:text-white">{{ $companyEmail }}</a>
                    </li>
                    <li class="flex items-start gap-2.5"><i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary-400"></i>{{ $companyAddress }}</li>
                </ul>
            </div>

            @if($websiteMascot)
                {{-- No fluxo (não absolute): a linha cresce até a altura do mascote, sem cortar a cabeça --}}
                <div class="pointer-events-none relative hidden items-end justify-center self-stretch lg:flex">
                    <div class="absolute bottom-0 left-1/2 h-56 w-56 -translate-x-1/2 rounded-full bg-primary-500/20 blur-3xl"></div>
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($websiteMascot) }}" alt="" class="relative -mb-6 h-[300px] w-auto max-w-none object-contain drop-shadow-2xl">
                </div>
            @endif
        </div>

        @if(data_get($website, 'features.newsletter', true))
            <div class="mt-12 rounded-card border border-border bg-card p-5 md:p-6">
                @livewire('newsletter-form')
            </div>
        @endif

        <div class="relative z-10 mt-10 flex flex-col items-center justify-between gap-3 border-t border-border pt-6 text-xs text-white/50 md:flex-row">
            <span>{{ $companyName }} &copy; {{ date('Y') }}. Todos os direitos reservados.</span>
            <a href="#" onclick="window.scrollTo({ top: 0, behavior: 'smooth' }); return false;" class="inline-flex items-center gap-1 transition-colors hover:text-white">
                Voltar ao topo <i data-lucide="arrow-up" class="h-3.5 w-3.5"></i>
            </a>
        </div>
    </div>
</footer>
@if($footerScripts)
    {!! $footerScripts !!}
@endif
