@props([
    'data' => [],
    'bgColor' => null,
    'textColor' => null,
    'header' => [],
    'items' => [],
])
@php
    // Se estiver vazio, vamos surpreender o usuário com um conteúdo padrão impecável
    if (empty($items)) {
        $header = [
            'subtitle' => 'Nosso Time',
            'title' => 'Conheça os especialistas por trás da Codhous',
            'description' => 'Uma equipe dedicada a entregar o melhor concreto com eficiência, tecnologia e transparência para a sua obra.'
        ];
        $items = [
            [
                'name' => 'Luis Fabrizo Spernza Solkberg',
                'icon' => 'graduation-cap',
                'role' => 'MESTRE DE ENGENHARIA',
                'sub_role' => 'DIRETOR EXECUTIVO',
                'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg',
                'highlight_1_icon' => 'building-2',
                'highlight_1_title' => '12+ anos',
                'highlight_1_subtitle' => 'de experiência',
                'highlight_2_icon' => 'users',
                'highlight_2_title' => 'Liderança',
                'highlight_2_subtitle' => 'e estratégia',
            ],
            [
                'name' => 'Marcelo Tokai',
                'icon' => 'settings',
                'role' => 'ENGENHEIRO-CHEFE, CEO',
                'sub_role' => '',
                'avatar' => 'https://randomuser.me/api/portraits/men/46.jpg',
                'highlight_1_icon' => 'briefcase',
                'highlight_1_title' => '11+ anos',
                'highlight_1_subtitle' => 'de experiência',
                'highlight_2_icon' => 'bar-chart',
                'highlight_2_title' => 'Gestão',
                'highlight_2_subtitle' => 'e operações',
            ],
            [
                'name' => 'Igor Ferraz-Thompson',
                'icon' => 'hard-hat',
                'role' => 'ENGENHEIRO',
                'sub_role' => '',
                'avatar' => 'https://randomuser.me/api/portraits/men/22.jpg',
                'highlight_1_icon' => 'building-2',
                'highlight_1_title' => '10+ anos',
                'highlight_1_subtitle' => 'de experiência',
                'highlight_2_icon' => 'award',
                'highlight_2_title' => 'Projetos',
                'highlight_2_subtitle' => 'de grande porte',
            ],
            [
                'name' => 'Aline Trentinni Lopes',
                'icon' => 'flask-conical',
                'role' => 'ENGENHEIRA QUÍMICA',
                'sub_role' => '',
                'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg',
                'highlight_1_icon' => 'flask-conical',
                'highlight_1_title' => '8+ anos',
                'highlight_1_subtitle' => 'de experiência',
                'highlight_2_icon' => 'check-circle-2',
                'highlight_2_title' => 'Inovação',
                'highlight_2_subtitle' => 'e qualidade',
            ],
        ];
    }
@endphp

@php
    $data = array_merge(array_filter(['background_color' => $bgColor, 'text_color' => $textColor]), (array) $data);
    $avatarFor = fn ($path) => empty($path) ? null : (str_starts_with($path, 'http') ? $path : \Illuminate\Support\Facades\Storage::url($path));
    $teamCols = match (min(count($items), 4)) {
        1, 2 => 'sm:grid-cols-2',
        3 => 'sm:grid-cols-2 lg:grid-cols-3',
        default => 'sm:grid-cols-2 lg:grid-cols-4',
    };
@endphp

<x-ui.section :data="$data" id="nosso-time" background="bg-background">
    <x-ui.section-header
        :header="$header"
        default-align="center"
        fallback-subtitle="Nosso Time"
        fallback-title="Conheça os especialistas por trás da Codhous"
        fallback-description="Uma equipe dedicada a entregar o melhor concreto com eficiência, tecnologia e transparência para a sua obra."
    />

    <div class="text-scheme-light grid gap-5 {{ $teamCols }}">
        @foreach($items as $index => $item)
            @php
                $mainIcon = ($item['icon_type'] ?? '') === 'other' ? ($item['icon'] ?? '') : ($item['icon_type'] ?? $item['icon'] ?? '');
                $highlights = collect([1, 2])
                    ->map(fn ($n) => ['icon' => $item["highlight_{$n}_icon"] ?? null, 'title' => $item["highlight_{$n}_title"] ?? null, 'subtitle' => $item["highlight_{$n}_subtitle"] ?? null])
                    ->filter(fn ($h) => filled($h['title']));
            @endphp
            <article class="surface-card surface-card-interactive group flex flex-col overflow-hidden" data-reveal style="--reveal-delay: {{ ($index % 4) * 80 }}ms">
                <div class="relative aspect-[4/4.2] overflow-hidden bg-muted">
                    @if($avatar = $avatarFor($item['avatar'] ?? null))
                        <img src="{{ $avatar }}" alt="{{ $item['name'] ?? '' }}" loading="lazy"
                             class="h-full w-full object-cover object-top grayscale-[35%] transition-all duration-700 ease-out-expo group-hover:scale-105 group-hover:grayscale-0">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-primary-500/5 text-primary-500/40">
                            <i data-lucide="user" class="h-16 w-16"></i>
                        </div>
                    @endif
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/50 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    @if(!empty($mainIcon))
                        <span class="absolute left-3 top-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white/90 text-primary-ink shadow-sm backdrop-blur">
                            <i data-lucide="{{ $mainIcon }}" class="h-4 w-4"></i>
                        </span>
                    @endif
                </div>

                <div class="flex flex-1 flex-col p-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary-ink">{{ $item['role'] ?? '' }}</p>
                    @if(!empty($item['sub_role']))
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-muted-foreground">{{ $item['sub_role'] }}</p>
                    @endif
                    <h3 class="mt-1.5 text-lg font-bold leading-tight text-foreground">{{ $item['name'] ?? '' }}</h3>

                    @if($highlights->isNotEmpty())
                        <div class="mt-auto pt-5"><dl class="grid grid-cols-2 gap-3 border-t border-border/60 pt-4">
                            @foreach($highlights as $highlight)
                                <div class="flex items-center gap-2">
                                    @if($highlight['icon'])
                                        <i data-lucide="{{ $highlight['icon'] }}" class="h-5 w-5 shrink-0 text-primary-ink"></i>
                                    @endif
                                    <div class="min-w-0 leading-tight">
                                        <dt class="text-xs font-bold text-foreground">{{ $highlight['title'] }}</dt>
                                        @if($highlight['subtitle'])
                                            <dd class="truncate text-[11px] text-muted-foreground">{{ $highlight['subtitle'] }}</dd>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </dl></div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</x-ui.section>
