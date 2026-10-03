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
    $bgClass = empty($bgMedia) ? 'bg-muted/50' : 'bg-zinc-950 text-white';
    $textForeground = empty($bgMedia) ? 'text-foreground' : 'text-white';
    $textMuted = empty($bgMedia) ? 'text-muted-foreground' : 'text-zinc-300';
    $cardBg = empty($bgMedia) ? 'bg-card border-border' : 'bg-zinc-900/40 backdrop-blur-md border-white/10 text-white';
@endphp
@if(!\App\Models\ContentSection::isHidden('coverage'))
<section id="onde-atuamos" class="relative border-b border-border {{ $bgClass }} py-20 lg:py-28 overflow-hidden">
    @php
        $isVideo = false;
        if ($bgMedia) {
            $isVideo = \Illuminate\Support\Str::endsWith($bgMedia, ['.mp4', '.webm', '.mov', '.quicktime']);
            $mediaUrl = str_starts_with($bgMedia, 'http') ? $bgMedia : \Illuminate\Support\Facades\Storage::url($bgMedia);
        }
    @endphp

    @if(!empty($bgMedia))
        <div class="absolute inset-0 z-0 pointer-events-none" style="clip-path: inset(0);">
            @if($isVideo)
                <video autoplay loop muted playsinline class="fixed inset-0 h-[100vh] w-[100vw] object-cover">
                    <source src="{{ $mediaUrl }}" type="video/mp4">
                </video>
            @else
                <img src="{{ $mediaUrl }}" alt="Background" class="fixed inset-0 h-[100vh] w-[100vw] object-cover">
            @endif
            @if($overlayEnabled)
                <div class="absolute inset-0 pointer-events-none" style="background-color: rgba({{ $overlayColor }}, {{ $overlayOpacity }});"></div>
            @endif
        </div>
    @endif

    <div class="relative z-10 mx-auto max-w-7xl px-4 lg:px-8">
        <x-ui.section-header 
            :header="$header ?? []"
            :fallback-title="$title ?? 'Onde atendemos'"
            :fallback-subtitle="$subtitle ?? null"
            :fallback-description="$description ?? null"
            :text-color="$textColor"
        />

        <div class="flex flex-col gap-12 lg:flex-row lg:items-start lg:gap-16">
            <div class="flex-1">
                <div class="rounded-xl border {{ $cardBg }} p-6 lg:p-8 shadow-sm">
                    <h3 class="mb-8 font-mono text-xl font-bold {{ $textForeground }}">Principais cidades e regiões</h3>
                    <ul class="grid gap-5 text-sm {{ $textMuted }} sm:grid-cols-2">
                        @foreach($cities as $city)
                            <li class="flex items-center gap-3 transition-colors hover:text-primary">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/10">
                                    <span class="h-2 w-2 rounded-full bg-primary"></span>
                                </span>
                                {{ is_array($city) ? ($city['label'] ?? '') : $city }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-8 grid gap-6 sm:grid-cols-2">
                    @foreach($sidebar as $index => $card)
                        @php
                            $icons = ['map-pin', 'truck'];
                            $icon = $card['icon'] ?? ($icons[$index] ?? 'map-pin');
                        @endphp
                        <div class="flex items-start gap-4 rounded-xl border {{ $cardBg }} p-6 shadow-sm">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10">
                                <i data-lucide="{{ $icon }}" class="h-6 w-6 text-primary"></i>
                            </div>
                            <div>
                                <p class="font-mono text-base font-bold {{ $textForeground }}">{{ $card['title'] ?? '' }}</p>
                                @if(!empty($card['description']))
                                    <p class="mt-1 text-sm {{ $textMuted }} leading-relaxed">{{ $card['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex-shrink-0 lg:w-[420px]">
                <div class="sticky top-24">
                    @include('livewire.partials.hero-cep-card', ['theme' => 'corporate'])
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
