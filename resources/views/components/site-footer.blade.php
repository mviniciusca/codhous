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
    if ($theme === 'corporate') {
        $footerClasses = 'bg-zinc-950 text-zinc-400 border-t border-zinc-900';
    } elseif ($theme === 'creative') {
        $footerClasses = 'bg-zinc-50 border-t border-zinc-200';
    }
@endphp

@if($theme === 'creative')
    {{-- CREATIVE FOOTER (Big CTA, Vibrant, Minimalist Links) --}}
    <footer class="bg-primary pt-20 pb-10 text-primary-foreground border-t-0">
        <div class="mx-auto max-w-7xl px-4 text-center lg:px-8 mb-16">
            <h2 class="font-mono text-4xl md:text-5xl lg:text-6xl font-black mb-8 tracking-tight" style="text-wrap: balance;">
                Pronto para inovar na sua obra?
            </h2>
            <a href="#orcamento" class="inline-block bg-white text-primary px-10 py-5 rounded-full font-mono text-sm font-bold uppercase tracking-widest shadow-xl hover:scale-105 active:scale-95 transition-all">
                Fale com nossos especialistas
            </a>
        </div>
        <div class="mx-auto max-w-7xl px-4 lg:px-8 flex flex-col md:flex-row items-center justify-between border-t border-white/20 pt-8 gap-6">
            <a href="/" class="flex items-center gap-2">
                @if($websiteLogo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}" alt="{{ $websiteName }}" class="h-8 w-auto object-contain brightness-0 invert opacity-90">
                @else
                    <span class="font-mono text-xl font-bold tracking-tighter">{{ $websiteName }}</span>
                @endif
            </a>
            <div class="flex flex-wrap gap-6 text-sm font-medium opacity-80">
                @foreach($navigation as $item)
                    <a href="{{ data_get($item, 'url') }}" class="hover:opacity-100 transition-opacity">{{ data_get($item, 'label') }}</a>
                @endforeach
            </div>
            <div class="flex gap-4">
                @foreach($networks as $key => $iconName)
                    @if($url = data_get($website, "social_networks.$key"))
                        <a href="{{ $url }}" target="_blank" class="hover:scale-110 hover:text-white opacity-80 transition-all">
                            <ion-icon name="{{ $iconName }}" class="h-6 w-6"></ion-icon>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="text-center text-xs opacity-60 mt-12 font-mono">
            {{ $companyName }} &copy; {{ date('Y') }}. Todos os direitos reservados.
        </div>
    </footer>

@elseif($theme === 'corporate')
    {{-- CORPORATE FOOTER (Dark, Structured, Professional) --}}
    <footer class="bg-zinc-950 pt-16 pb-8 text-zinc-400 border-t border-zinc-900">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-5 border-b border-zinc-800 pb-12">
                <div class="lg:col-span-2">
                    <a href="/" class="mb-6 inline-block">
                        @if($websiteLogo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($websiteLogo) }}" alt="{{ $websiteName }}" class="h-10 w-auto object-contain brightness-0 invert">
                        @else
                            <span class="font-mono text-2xl font-bold tracking-tighter text-white">{{ $websiteName }}</span>
                        @endif
                    </a>
                    <p class="max-w-sm text-sm leading-relaxed">{{ $websiteDescription }}</p>
                    <div class="mt-6 flex items-center gap-4">
                        @foreach($networks as $key => $iconName)
                            @if($url = data_get($website, "social_networks.$key"))
                                <a href="{{ $url }}" target="_blank" class="bg-zinc-900 p-2 rounded-md hover:bg-primary hover:text-white transition-all text-zinc-500">
                                    <ion-icon name="{{ $iconName }}" class="h-5 w-5"></ion-icon>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <h4 class="mb-6 font-mono text-sm font-bold text-white uppercase tracking-wider">Navegação</h4>
                    <ul class="flex flex-col gap-3 text-sm">
                        @foreach($navigation as $item)
                            <li><a href="{{ data_get($item, 'url') }}" class="hover:text-primary transition-colors">{{ data_get($item, 'label') }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="lg:col-span-2">
                    <h4 class="mb-6 font-mono text-sm font-bold text-white uppercase tracking-wider">Atendimento Corporativo</h4>
                    <ul class="flex flex-col gap-4 text-sm">
                        <li class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary"></i>
                            <span>{{ $companyAddress }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="phone" class="h-4 w-4 shrink-0 text-primary"></i>
                            @if($companyPhoneTel)
                                <a href="tel:{{ $companyPhoneTel }}" class="hover:text-primary transition-colors">{{ $companyPhone }}</a>
                            @else
                                {{ $companyPhone }}
                            @endif
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="mail" class="h-4 w-4 text-primary"></i>
                            {{ $companyEmail }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-8 text-xs font-mono">
                <span>{{ $companyName }} &copy; {{ date('Y') }}. Todos os direitos reservados.</span>
                <span class="text-zinc-600">CNPJ: 00.000.000/0000-00</span>
            </div>
        </div>
    </footer>

@else
    {{-- DEFAULT FOOTER --}}
    <footer class="{{ $footerClasses }}">
        <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <a href="#" class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                            <i data-lucide="truck" class="h-5 w-5 text-primary-foreground"></i>
                        </div>
                        <span class="font-mono text-xl font-bold tracking-tight text-foreground">{{ $websiteName }}</span>
                    </a>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-muted-foreground">{{ $websiteDescription }} Sua obra merece o melhor desde a fundação.</p>
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

                    <h4 class="mt-8 mb-4 font-mono text-sm font-bold uppercase tracking-wider text-foreground">Redes Sociais</h4>
                    <div class="flex gap-4">
                        @foreach($networks as $key => $iconName)
                            @if($url = data_get($website, "social_networks.$key"))
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-foreground transition-all hover:scale-110 hover:text-primary">
                                    <ion-icon name="{{ $iconName }}" class="h-6 w-6"></ion-icon>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-12 border-t border-border pt-8 text-center text-xs text-muted-foreground">
                {{ $companyName }} &copy; {{ date('Y') }}. Todos os direitos reservados.
            </div>
        </div>
    </footer>
@endif

@if($footerScripts)
    {!! $footerScripts !!}
@endif
