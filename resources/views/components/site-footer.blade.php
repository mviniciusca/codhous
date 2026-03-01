@php
    $website = \App\Models\Setting::get('website', []);
    $company = \App\Models\Setting::get('company', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $websiteDescription = data_get($website, 'description', 'Excelência em concreto usinado e locação de equipamentos.');
    $scripts = data_get($website, 'scripts', []);
    $footerScripts = data_get($scripts, 'footer_scripts');
    
    // Trade Name from resource
    $companyName = data_get($company, 'trade_name', 'ConcretoPro');
    $companyEmail = data_get($company, 'email', 'contato@concretopro.com.br');
    $companyPhone = data_get($company, 'phone', '(11) 99999-9999');
    
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

<footer class="border-t border-border bg-card">
    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <a href="#" class="flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                        <i data-lucide="truck" class="h-5 w-5 text-zinc-950"></i>
                    </div>
                    <span class="font-mono text-xl font-bold tracking-tight text-foreground">{{ $websiteName }}</span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-muted-foreground">{{ $websiteDescription }} Sua obra merece o melhor desde a fundação.</p>
            </div>

            <div>
                <h4 class="mb-4 font-mono text-sm font-bold uppercase tracking-wider text-foreground">Serviços</h4>
                <ul class="flex flex-col gap-2 text-sm text-muted-foreground">
                    <li><a href="#servicos" class="transition-colors hover:text-primary">Concreto Usinado</a></li>
                    <li><a href="#servicos" class="transition-colors hover:text-primary">Bombeamento</a></li>
                    <li><a href="#servicos" class="transition-colors hover:text-primary">Locação de Máquinas</a></li>
                    <li><a href="#calculadora" class="transition-colors hover:text-primary">Calculadora de Volume</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 font-mono text-sm font-bold uppercase tracking-wider text-foreground">Contato</h4>
                <ul class="flex flex-col gap-3 text-sm text-muted-foreground">
                    <li class="flex items-center gap-2"><i data-lucide="phone" class="h-4 w-4 text-primary"></i>{{ $companyPhone }}</li>
                    <li class="flex items-center gap-2"><i data-lucide="mail" class="h-4 w-4 text-primary"></i>{{ $companyEmail }}</li>
                    <li class="flex items-start gap-2"><i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary"></i>{{ $companyAddress }}</li>
                </ul>
            </div>
        </div>

        <div class="mt-12 border-t border-border pt-8 text-center text-xs text-muted-foreground">
            {{ $companyName }} &copy; {{ date('Y') }}. Todos os direitos reservados.
        </div>
    </div>
</footer>

@if($footerScripts)
    {!! $footerScripts !!}
@endif
