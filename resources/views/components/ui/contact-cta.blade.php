@props([
    'image' => null,
    'imageSize' => 155,
    'imageOffsetX' => 0,
    'imageOffsetY' => 0,
    'showFeatures' => true,
    'title' => null,
    'subtitle' => null,
    'buttonLabel' => null,
    'buttonUrl' => null,
])

@php
    use App\Models\Setting;

    $whatsapp = Setting::get('website.social_networks.whatsapp');
    $phone = Setting::get('company.phone');

    // Imagem do modelo (PNG transparente) enviada no bloco
    $modelUrl = null;
    if (!empty($image)) {
        $img = is_array($image) ? (array_values($image)[0] ?? null) : $image;
        if (is_string($img) && $img !== '') {
            $modelUrl = str_starts_with($img, 'http') ? $img : \Illuminate\Support\Facades\Storage::url($img);
        } elseif (is_object($img) && method_exists($img, 'temporaryUrl')) {
            $modelUrl = $img->temporaryUrl();
        }
    }
    $imageSize = max(100, min(220, (int) $imageSize));
    $imageOffsetX = max(-200, min(200, (int) $imageOffsetX));
    $imageOffsetY = max(-100, min(100, (int) $imageOffsetY));
    // Espaço no topo para a cabeça do modelo não cobrir a seção anterior
    $topSpace = $modelUrl ? max(24, (int) round(($imageSize - 110) * 1.3)) : 0;

    $features = [
        ['icon' => 'zap', 'title' => 'Atendimento rápido', 'text' => 'Resposta em poucos minutos'],
        ['icon' => 'file-text', 'title' => 'Orçamento sem compromisso', 'text' => 'Preço claro, sem surpresas'],
        ['icon' => 'messages-square', 'title' => 'Canal que preferir', 'text' => 'WhatsApp, telefone e e-mail'],
    ];
@endphp

<div class="relative" @if($topSpace) style="--cta-top: {{ $topSpace }}px" @endif>
    @if($topSpace)<div class="hidden md:block" style="height: var(--cta-top)"></div>@endif

    <div class="dark relative rounded-[calc(var(--radius-card)*1.5)] bg-primary-950 text-white shadow-[0_30px_80px_-40px_rgb(0_0_0/0.6)]" data-reveal>
        {{-- Profundidade com os tons escuros da marca --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden rounded-[inherit]">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-800 via-primary-950 to-black"></div>
            <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-primary-500/40 blur-3xl"></div>
            <div class="absolute -bottom-32 left-10 h-64 w-64 rounded-full bg-primary-600/30 blur-3xl"></div>
            <div class="absolute inset-0 opacity-[0.07] [background-image:radial-gradient(white_1px,transparent_1px)] [background-size:18px_18px]"></div>
        </div>

        <div @class([
            'relative grid items-center gap-8 p-6 md:p-10',
            'md:grid-cols-[1.4fr_1fr]' => $showFeatures && ! $modelUrl,
            'md:grid-cols-[1.3fr_1fr_0.8fr]' => $showFeatures && $modelUrl,
            'md:grid-cols-[1.6fr_0.8fr]' => ! $showFeatures && $modelUrl,
        ])>
            <div class="text-center md:text-left">
                <span class="kicker mb-3"><span class="kicker-dot"></span> Atendimento</span>
                <h2 class="font-mono text-3xl font-bold leading-[1.05] tracking-tight md:text-5xl" style="text-wrap: balance;">
                    @if($title)
                        {{ $title }}
                    @else
                        Fale <span class="text-primary-400">conosco</span>
                    @endif
                </h2>
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-white/70 md:mx-0 md:text-base">
                    {{ $subtitle ?: 'Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.' }}
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-3 md:justify-start">
                    @if($buttonLabel && $buttonUrl)
                        <x-ui.button :href="$buttonUrl" trailing-icon="arrow-right">{{ $buttonLabel }}</x-ui.button>
                    @endif
                    @if($whatsapp)
                        <x-ui.button :href="$whatsapp" target="_blank" variant="whatsapp" icon="message-circle">WhatsApp</x-ui.button>
                    @endif
                    @if($phone)
                        <x-ui.button :href="'tel:' . preg_replace('/\D/', '', $phone)" variant="secondary" icon="phone" class="backdrop-blur">Ligar</x-ui.button>
                    @endif
                    <x-ui.button href="/contato" variant="ghost" icon="mail">E-mail</x-ui.button>
                </div>
            </div>

            @if($showFeatures)
                <ul class="hidden flex-col gap-4 border-l border-white/10 pl-8 md:flex">
                    @foreach($features as $feature)
                        <li class="group flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-primary-400 transition-all duration-300 group-hover:bg-primary-500 group-hover:text-primary-foreground">
                                <i data-lucide="{{ $feature['icon'] }}" class="h-5 w-5"></i>
                            </span>
                            <span class="leading-tight">
                                <strong class="block text-sm font-semibold">{{ $feature['title'] }}</strong>
                                <span class="text-xs text-white/60">{{ $feature['text'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Modelo ancorado na base do cartão, sobressaindo pelo topo --}}
            @if($modelUrl)
                <div class="relative hidden self-stretch md:block" style="min-height: 9rem">
                    <img src="{{ $modelUrl }}" alt="Atendimento"
                         class="pointer-events-none absolute left-1/2 z-10 w-auto max-w-none select-none object-contain object-bottom drop-shadow-2xl"
                         style="height: {{ $imageSize }}%; bottom: calc(-2.5rem + {{ $imageOffsetY }}px); transform: translateX(calc(-50% + {{ $imageOffsetX }}px));">
                </div>
            @endif
        </div>
    </div>
</div>
