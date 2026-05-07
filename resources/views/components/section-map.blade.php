@props([
    'title' => null,
    'iframe' => null,
])

@php
    $company = \App\Models\Setting::get('company', []);
    $globalMapsCode = data_get($company, 'maps_code');
    
    // Fallback do Galeão fornecido pelo usuário
    $defaultIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3677.924270850591!2d-43.2566277!3d-22.805269799999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x99798f3b16364f%3A0xcfa9dfbf2f584512!2sAeroporto%20Internacional%20do%20Rio%20de%20Janeiro%20-%20Gale%C3%A3o%20%E2%80%93%20Antonio%20Carlos%20Jobim!5e0!3m2!1spt-BR!2sbr!4v1778129346036!5m2!1spt-BR!2sbr" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';

    // Lógica de prioridade: Bloco > Global > Padrão (Galeão)
    $mapsCode = trim($iframe ?? $globalMapsCode ?? $defaultIframe);
    $displayTitle = $title ?? 'Nossa Localização';
@endphp

<section class="bg-muted py-16">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        @if(!empty($displayTitle))
            <div class="mb-8 max-w-2xl text-center md:text-left">
                <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                    {{ $displayTitle }}
                </h2>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-border shadow-sm h-[400px] lg:h-[500px] w-full grayscale hover:grayscale-0 transition-all duration-700 [&>iframe]:w-full [&>iframe]:h-full">
            {!! $mapsCode !!}
        </div>
    </div>
</section>
