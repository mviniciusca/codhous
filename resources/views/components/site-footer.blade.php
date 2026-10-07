@props(['theme' => 'default'])
@php
    $website = \App\Models\Setting::get('website', []);
    $company = \App\Models\Setting::get('company', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $websiteLogo = data_get($website, 'logo');
    $websiteDescription = data_get($website, 'description', 'Excelência em concreto usinado e locação de equipamentos.');
    $scripts = data_get($website, 'scripts', []);
    $footerScripts = data_get($scripts, 'footer_scripts');

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
    $footerClasses = 'border-t border-border bg-card';
@endphp

{{-- DEFAULT FOOTER --}}
    @php
        $websiteMascot = data_get($website, 'mascot');
        $activeNetworks = collect($networks)
            ->map(fn ($icon, $key) => ['icon' => $icon, 'key' => $key, 'url' => data_get($website, "social_networks.$key")])
            ->filter(fn ($n) => filled($n['url']));
    @endphp
    <footer class="{{ $footerClasses }} overflow-hidden relative">
        <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8 relative">
            <div class="grid gap-10 md:grid-cols-2 {{ $websiteMascot ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} relative z-10">
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <a href="/" class="inline-flex items-center gap-2 self-start">
                        @if($websiteLogo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($websiteLogo) }}" alt="{{ $websiteName }}" style="height: 72px; width: auto; max-width: 280px;" class="object-contain">
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                                <i data-lucide="truck" class="h-5 w-5 text-primary-foreground"></i>
                            </div>
                            <span class="font-mono text-xl font-bold tracking-tight text-foreground">{{ $websiteName }}</span>
                        @endif
                    </a>

                    @if($activeNetworks->isNotEmpty())
                        <div>
                            <p class="mb-3 font-mono text-xs font-bold uppercase tracking-wider text-muted-foreground">Siga a gente</p>
                            <div class="flex flex-wrap gap-3">
                                @foreach($activeNetworks as $network)
                                    <a href="{{ $network['url'] }}" target="_blank" rel="noopener noreferrer"
                                       aria-label="{{ ucfirst($network['key']) }}"
                                       class="group flex items-center justify-center rounded-full border border-border bg-background text-foreground shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-primary hover:bg-primary hover:text-primary-foreground hover:shadow-lg"
                                       style="width: 42px; height: 42px;">
                                        <ion-icon name="{{ $network['icon'] }}" style="font-size: 20px;"></ion-icon>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <h4 class="mb-4 font-mono text-sm font-bold uppercase tracking-wider text-foreground">Navegação</h4>
                    <ul class="flex flex-col gap-2 text-sm text-muted-foreground">
                        @foreach($navigation as $item)
                            <li><a href="{{ data_get($item, 'url') }}" class="transition-colors hover:text-primary">{{ data_get($item, 'label') }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="mb-4 font-mono text-sm font-bold uppercase tracking-wider text-foreground">Contato</h4>
                    <ul class="flex flex-col gap-3 text-sm text-muted-foreground">
                        <li class="flex items-center gap-2">
                            <i data-lucide="phone" class="h-4 w-4 shrink-0 text-primary"></i>
                            @if($companyPhoneTel)
                            <a href="tel:{{ $companyPhoneTel }}" class="transition-colors hover:text-primary">{{ $companyPhone }}</a>
                            @else
                            {{ $companyPhone }}
                            @endif
                        </li>
                        <li class="flex items-center gap-2"><i data-lucide="mail" class="h-4 w-4 text-primary"></i>{{ $companyEmail }}</li>
                        <li class="flex items-start gap-2"><i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary"></i>{{ $companyAddress }}</li>
                    </ul>
                </div>
            </div>
            
            @if($websiteMascot)
            <div class="hidden md:block absolute bottom-0 right-0 z-20 pointer-events-none" style="margin-right: -2rem;">
                <div class="absolute rounded-full bg-primary" style="width: 260px; height: 260px; left: 50%; top: 45%; transform: translate(-50%, -50%); opacity: .12; filter: blur(60px);"></div>
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($websiteMascot) }}" alt="Mascote" style="height: 350px; width: auto;" class="relative object-contain drop-shadow-2xl" />
            </div>
            @endif

            <div class="relative z-10 mt-12 pt-8 text-xs text-muted-foreground {{ $websiteMascot ? 'md:text-left' : 'text-center' }} text-center">
                <div class="absolute top-0 left-0 h-px bg-border"
                     style="width: {{ $websiteMascot ? '70%' : '100%' }}; -webkit-mask-image: linear-gradient(to right, #000 0%, #000 70%, transparent 100%); mask-image: linear-gradient(to right, #000 0%, #000 70%, transparent 100%);"></div>
                {{ $companyName }} &copy; {{ date('Y') }}. Todos os direitos reservados.
            </div>
        </div>
    </footer>
@if($footerScripts)
    {!! $footerScripts !!}
@endif
