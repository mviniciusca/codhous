@php
    // Se não for passado via prop, tenta buscar da seção global 'coverage'
    $section = \App\Models\ContentSection::getBySlug('coverage');
    
    $title = $title ?? ($section?->content['header']['title'] ?? 'Onde atendemos');
    $subtitle = $subtitle ?? ($section?->content['header']['subtitle'] ?? 'Cobertura');
    $description = $description ?? ($section?->content['header']['description'] ?? 'Atuamos na região com frota própria e logística integrada para garantir entrega no prazo.');
    $cities = $cities ?: ($section?->content['cities'] ?? []);
    $sidebar = $sidebar ?: ($section?->content['sidebar'] ?? [
        ['title' => 'Raio de entrega', 'description' => 'Consulte disponibilidade e prazo para sua cidade no orçamento.', 'icon' => 'map-pin'],
        ['title' => 'Frota própria', 'description' => 'Rastreamento e pontualidade em todas as entregas.', 'icon' => 'truck'],
    ]);
    
    $bgMedia = $backgroundMedia ?? ($section?->content['background_media'] ?? null);

    // Fallback final: se ainda estiver vazio, pega todas as cidades ativas do OperationArea
    if (empty($cities)) {
        $cities = \App\Models\OperationArea::query()
            ->where('is_active', true)
            ->pluck('city')
            ->toArray();
    }
    
    $overlayEnabled = $overlayData['enabled'] ?? ($section?->content['background_overlay_enabled'] ?? false);
    $overlayType = $overlayData['type'] ?? ($section?->content['background_overlay_type'] ?? 'dark');
    $overlayOpacity = ($overlayData['opacity'] ?? ($section?->content['background_overlay_opacity'] ?? '50')) / 100;
    $overlayColor = $overlayType === 'light' ? '255, 255, 255' : '0, 0, 0';
@endphp
@php
    $hasMedia = !empty($bgMedia);
    // Com mídia de fundo a seção fica escura; sem mídia segue o fundo/esquema do bloco
    $scheme = $hasMedia ? 'dark' : \App\Support\SectionStyle::scheme(['background_color' => $bgColor, 'text_color' => $textColor]);
    $sectionBg = $hasMedia ? 'bg-black' : ($bgColor ?: 'bg-muted/40');
    if ($hasMedia) {
        $isVideo = \Illuminate\Support\Str::endsWith($bgMedia, ['.mp4', '.webm', '.mov', '.quicktime']);
        $mediaUrl = str_starts_with($bgMedia, 'http') ? $bgMedia : \Illuminate\Support\Facades\Storage::url($bgMedia);
    }
    $cityList = collect($cities)->map(fn ($c) => is_array($c) ? ($c['label'] ?? '') : $c)->filter()->values();
@endphp
@if(!\App\Models\ContentSection::isHidden('coverage'))
<section id="onde-atuamos" class="relative isolate overflow-hidden {{ $sectionBg }} {{ \App\Support\SectionStyle::schemeClass($scheme) }} {{ $spacing ?: 'py-14 md:py-20' }}">
    @if($hasMedia)
        {{-- Mídia fixa (efeito parallax) recortada pela seção --}}
        <div class="pointer-events-none absolute inset-0 -z-10" style="clip-path: inset(0);">
            @if($isVideo)
                <video autoplay loop muted playsinline class="fixed inset-0 h-screen w-screen object-cover"><source src="{{ $mediaUrl }}" type="video/mp4"></video>
            @else
                <img src="{{ $mediaUrl }}" alt="" class="fixed inset-0 h-screen w-screen object-cover">
            @endif
            <div class="absolute inset-0" style="background-color: rgba({{ $overlayEnabled ? $overlayColor : '0, 0, 0' }}, {{ $overlayEnabled ? $overlayOpacity : 0.6 }});"></div>
        </div>
    @endif

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">
            <div class="lg:col-span-7">
                <x-ui.section-header
                    :header="$header ?? []"
                    :fallback-title="$title ?? 'Onde atendemos'"
                    :fallback-subtitle="$subtitle ?? null"
                    :fallback-description="$description ?? null"
                    align="left"
                    class="!mb-8"
                />

                @if($cityList->isNotEmpty())
                    <div class="surface-card p-5 md:p-6" data-reveal>
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <h3 class="text-sm font-semibold text-foreground">Principais cidades e regiões</h3>
                            <span class="rounded-full bg-primary-500/10 px-2.5 py-0.5 font-mono text-xs font-semibold text-primary-ink">{{ $cityList->count() }}</span>
                        </div>
                        <ul class="flex flex-wrap gap-2">
                            @foreach($cityList as $city)
                                <li class="inline-flex cursor-default items-center gap-1.5 rounded-full border border-border/70 px-3 py-1.5 text-sm text-foreground/80 transition-colors hover:border-primary-500/40 hover:bg-primary-500/10 hover:text-primary-ink">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary-500"></span>
                                    {{ $city }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach($sidebar as $index => $card)
                        <div class="surface-card group flex items-start gap-4 p-5" data-reveal style="--reveal-delay: {{ ($index + 1) * 80 }}ms">
                            <x-ui.icon-box :icon="$card['icon'] ?? (['map-pin', 'truck'][$index] ?? 'map-pin')" />
                            <div>
                                <p class="font-semibold text-foreground">{{ $card['title'] ?? '' }}</p>
                                @if(!empty($card['description']))
                                    <p class="mt-1 text-sm leading-relaxed text-muted-foreground">{{ $card['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-28" data-reveal style="--reveal-delay: 150ms">
                    @include('livewire.partials.hero-cep-card', ['theme' => $scheme === 'light' ? 'corporate' : 'default'])
                </div>
            </div>
        </div>
    </div>
</section>

@script
<script>
    Livewire.hook('morph.updated', () => {
        if (typeof window.lucide !== 'undefined') {
            window.lucide.createIcons();
        }
    });
</script>
@endscript
@endif
