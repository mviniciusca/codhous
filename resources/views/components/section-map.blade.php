@props([
    'data' => [],
    'textColor' => null,
    'bgColor' => null,
    'title' => null,
    'header' => [],
    'iframe' => null,
])

@php
    $company = \App\Models\Setting::get('company', []);
    $defaultIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3677.924270850591!2d-43.2566277!3d-22.805269799999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x99798f3b16364f%3A0xcfa9dfbf2f584512!2sAeroporto%20Internacional%20do%20Rio%20de%20Janeiro%20-%20Gale%C3%A3o%20%E2%80%93%20Antonio%20Carlos%20Jobim!5e0!3m2!1spt-BR!2sbr!4v1778129346036!5m2!1spt-BR!2sbr" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';

    // Prioridade: bloco > configuração global > padrão
    $mapsCode = trim($iframe ?: data_get($company, 'maps_code') ?: $defaultIframe);

    $addr = data_get($company, 'address', []);
    $addrStr = is_array($addr)
        ? implode(', ', array_filter([trim(data_get($addr, 'street') . ', ' . data_get($addr, 'number'), ', '), data_get($addr, 'neighborhood'), trim(data_get($addr, 'city') . ' - ' . data_get($addr, 'state'), ' -')]))
        : (string) $addr;
    $phone = data_get($company, 'phone');
    $hours = data_get($company, 'opening_hours');
    $directionsUrl = $addrStr ? 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($addrStr) : null;
    $cardTitle = $title ?: ($header['title'] ?? 'Nossa localização');
@endphp

<x-ui.section :data="$data" spacing="none" background="bg-muted" container="none">
    <div class="group relative">
        <div class="h-[380px] w-full grayscale transition-all duration-700 group-hover:grayscale-0 lg:h-[480px] [&>iframe]:h-full [&>iframe]:w-full">
            {!! $mapsCode !!}
        </div>

        {{-- Cartão de endereço flutuando sobre o mapa --}}
        @if($addrStr || $phone)
            <div class="pointer-events-none absolute inset-x-0 bottom-0 lg:bottom-auto lg:top-1/2 lg:-translate-y-1/2">
                <div class="mx-auto max-w-7xl px-4 pb-4 sm:px-6 lg:px-8 lg:pb-0">
                    <div class="surface-card text-scheme-light pointer-events-auto w-full max-w-sm p-5 shadow-xl md:p-6" data-reveal>
                        <span class="kicker mb-2"><span class="kicker-dot"></span> Visite-nos</span>
                        <h2 class="font-mono text-xl font-bold text-foreground">{{ $cardTitle }}</h2>
                        <ul class="mt-4 space-y-2.5 text-sm text-muted-foreground">
                            @if($addrStr)
                                <li class="flex gap-2.5"><i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary-ink"></i>{{ $addrStr }}</li>
                            @endif
                            @if($phone)
                                <li class="flex gap-2.5"><i data-lucide="phone" class="mt-0.5 h-4 w-4 shrink-0 text-primary-ink"></i>{{ $phone }}</li>
                            @endif
                            @if($hours)
                                <li class="flex gap-2.5"><i data-lucide="clock" class="mt-0.5 h-4 w-4 shrink-0 text-primary-ink"></i>{{ $hours }}</li>
                            @endif
                        </ul>
                        @if($directionsUrl)
                            <x-ui.button :href="$directionsUrl" target="_blank" size="sm" icon="navigation" class="mt-5 w-full">Como chegar</x-ui.button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-ui.section>
