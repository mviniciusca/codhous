@props([
    'data' => [],
    'bgColor' => null,
    'textColor' => 'light',
    'header' => [],
    'items' => [],
])
@php
    $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
    $overflowClass = $bgPullUpAmount !== 0 ? '' : 'overflow-hidden';

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
                'bio' => 'Engenheiro formado pela PUC Minas. Atua desde 2012 liderando a expansão técnica e estrutural da empresa.',
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
                'bio' => 'Com 11 anos de experiência, possui passagens marcantes por grandes construtoras como MTX e SAGA.',
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
                'bio' => 'Engenheiro paulista pela Unicamp. Traz na bagagem experiência internacional por gigantes como a AT&T.',
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
                'bio' => 'Especialista pela USP com foco em obras civis e química. Premiada pela Universidade de Nova York (NYU).',

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

<section id="nosso-time" class="{{ $bgColor ?? 'bg-background' }} py-20 lg:py-32 relative {{ $overflowClass }} {{ ($textColor ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }}">
    <x-ui.section-background :data="$data" />
    
    <div class="mx-auto max-w-7xl px-4 lg:px-8 relative z-10">
        
        <!-- Header da Seção -->
        <div class="flex justify-center w-full">
            <x-ui.section-header 
                :header="$header"
                :fallbackSubtitle="'Nosso Time'"
                :fallbackTitle="'Conheça os especialistas por trás da Codhous'"
                :fallbackDescription="'Uma equipe dedicada a entregar o melhor concreto com eficiência, tecnologia e transparência para a sua obra.'"
                :text-color="$textColor"
            />
        </div>

        <!-- Grid do Time -->
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4 text-scheme-light">
            @foreach($items as $item)
                <div class="group flex flex-col overflow-hidden rounded-[24px] bg-card border border-border/40 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl hover:border-primary/30 hover:-translate-y-2">
                    
                    <!-- Foto -->
                    <div class="relative aspect-[4/3] overflow-hidden bg-muted">
                        @php
                            $avatarUrl = null;
                            if (!empty($item['avatar'])) {
                                $avatarUrl = str_starts_with($item['avatar'], 'http') ? $item['avatar'] : \Illuminate\Support\Facades\Storage::url($item['avatar']);
                            }
                        @endphp
                        
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $item['name'] ?? '' }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-primary/5 text-primary/40">
                                <i data-lucide="user" class="h-16 w-16"></i>
                            </div>
                        @endif
                        
                        <!-- Gradiente por cima da foto para dar um charme e ligar com o card -->
                        <div class="absolute inset-0 bg-gradient-to-t from-card via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                    </div>
                    
                    <!-- Conteúdo -->
                    <div class="flex flex-1 flex-col p-6 lg:p-8 relative z-10 bg-card">
                        <div class="mb-4 flex items-start gap-2">
                            @php
                                $mainIcon = ($item['icon_type'] ?? '') === 'other' ? ($item['icon'] ?? '') : ($item['icon_type'] ?? $item['icon'] ?? '');
                            @endphp
                            @if(!empty($mainIcon))
                                <i data-lucide="{{ $mainIcon }}" class="h-4 w-4 shrink-0 text-primary mt-0.5"></i>
                            @endif
                            <div class="flex flex-col leading-none">
                                <span class="text-[10px] font-bold tracking-[0.05em] uppercase text-primary">
                                    {{ $item['role'] ?? '' }}
                                </span>
                                @if(!empty($item['sub_role']))
                                    <span class="text-[10px] font-semibold tracking-[0.05em] uppercase text-muted-foreground mt-1">
                                        {{ $item['sub_role'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-bold text-foreground mb-3 leading-tight">
                            {{ $item['name'] ?? '' }}
                        </h3>
                        
                        @if(!empty($item['bio']))
                            <p class="text-sm leading-relaxed text-muted-foreground flex-1 line-clamp-3" title="{{ $item['bio'] }}">
                                {{ $item['bio'] }}
                            </p>
                        @endif
                        
                        <!-- Destaques (Highlights) -->
                        @if(!empty($item['highlight_1_title']) || !empty($item['highlight_2_title']))
                            <div class="mt-6 pt-5 grid grid-cols-2 gap-4 border-t border-border/30">
                                <!-- Destaque 1 -->
                                @if(!empty($item['highlight_1_title']))
                                    <div class="flex items-center gap-3">
                                        @if(!empty($item['highlight_1_icon']))
                                            <i data-lucide="{{ $item['highlight_1_icon'] }}" class="h-6 w-6 shrink-0 text-primary"></i>
                                        @endif
                                        <div class="flex flex-col leading-none">
                                            <span class="text-[11px] font-bold text-foreground mb-1">{{ $item['highlight_1_title'] }}</span>
                                            @if(!empty($item['highlight_1_subtitle']))
                                                <span class="text-[10px] font-medium text-muted-foreground">{{ $item['highlight_1_subtitle'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Destaque 2 -->
                                @if(!empty($item['highlight_2_title']))
                                    <div class="flex items-center gap-3">
                                        @if(!empty($item['highlight_2_icon']))
                                            <i data-lucide="{{ $item['highlight_2_icon'] }}" class="h-6 w-6 shrink-0 text-primary"></i>
                                        @endif
                                        <div class="flex flex-col leading-none">
                                            <span class="text-[11px] font-bold text-foreground mb-1">{{ $item['highlight_2_title'] }}</span>
                                            @if(!empty($item['highlight_2_subtitle']))
                                                <span class="text-[10px] font-medium text-muted-foreground">{{ $item['highlight_2_subtitle'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                </div>
            @endforeach
        </div>
        
    </div>
</section>
