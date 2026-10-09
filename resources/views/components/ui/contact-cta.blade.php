@props(['image' => null, 'imageSize' => 155, 'imageOffsetX' => 0, 'imageOffsetY' => 0, 'showFeatures' => true])

@php
    use App\Models\Setting;
    $whatsapp = Setting::get('website.social_networks.whatsapp') ?? '#';
    $phone = Setting::get('company.phone');
    $email = Setting::get('company.email');

    // Imagem do modelo (PNG com fundo transparente) enviada no admin do bloco.
    $modelUrl = null;
    if (!empty($image)) {
        $img = is_array($image) ? (array_values($image)[0] ?? null) : $image;
        if (is_string($img) && $img !== '') {
            $modelUrl = str_starts_with($img, 'http') ? $img : \Illuminate\Support\Facades\Storage::url($img);
        } elseif (is_object($img) && method_exists($img, 'temporaryUrl')) {
            $modelUrl = $img->temporaryUrl();
        }
    }
    $hasModel = (bool) $modelUrl;
    $imageSize = max(100, min(220, (int) $imageSize));
    $imageOffsetX = max(-200, min(200, (int) $imageOffsetX));
    $imageOffsetY = max(-100, min(100, (int) $imageOffsetY));
    // Espaço reservado no topo para a cabeça do modelo não sobrepor a seção anterior
    $topSpace = $hasModel ? max(24, (int) round(($imageSize - 110) * 1.3)) : 0;

    $features = [
        ['icon' => 'headset', 'title' => 'Atendimento rápido', 'text' => 'Resposta em poucos minutos'],
        ['icon' => 'file-text', 'title' => 'Solicite seu orçamento', 'text' => 'Sem compromisso'],
        ['icon' => 'message-square', 'title' => 'Fale pelo canal preferido', 'text' => 'WhatsApp, telefone e e-mail'],
    ];
@endphp

<div class="relative" @if($topSpace) style="--cta-top: {{ $topSpace }}px" @endif>
    @if($topSpace)<div class="hidden md:block" style="height: var(--cta-top)"></div>@endif
    <div class="relative rounded-3xl border border-border bg-card shadow-xl">
      <div class="pointer-events-none absolute inset-0 overflow-hidden rounded-3xl">
        {{-- Decoração de fundo --}}
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -left-24 -bottom-24 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-[42%] md:block">
            <div class="absolute right-24 top-0 h-full w-44 -skew-x-[18deg] bg-primary/15"></div>
            <div class="absolute right-6 top-0 h-full w-28 -skew-x-[18deg] bg-primary/25"></div>
        </div>
      </div>

        <div class="relative grid items-center gap-6 px-6 py-6 md:grid-cols-[1.3fr_0.95fr_0.75fr] md:px-10 md:py-7">
            {{-- Texto + botões --}}
            <div class="text-center lg:text-left">
                <span class="mb-2 inline-flex items-center gap-3 text-[11px] font-bold uppercase tracking-[0.25em] text-primary">
                    <span class="hidden h-0.5 w-10 bg-primary lg:block"></span>
                    Atendimento
                </span>
                <h2 class="font-mono text-4xl font-extrabold leading-[0.95] tracking-tight text-foreground md:text-5xl">
                    Fale <br><span class="text-primary">conosco</span>
                </h2>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-muted-foreground">
                    Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.
                </p>

                <div class="mt-5 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="group inline-flex min-w-[150px] items-center justify-between gap-3 rounded-xl bg-[#16a34a] px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-green-600/30 transition-all hover:-translate-y-0.5 hover:shadow-xl">
                        <span class="inline-flex items-center gap-2"><ion-icon name="logo-whatsapp" class="h-5 w-5"></ion-icon> WhatsApp</span>
                        <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                    </a>

                    @if($phone)
                        <a href="tel:{{ preg_replace('/\D/', '', $phone) }}" class="group inline-flex min-w-[130px] items-center justify-between gap-3 rounded-xl border border-border bg-background/80 px-5 py-2.5 text-sm font-bold text-foreground shadow-sm transition-all hover:-translate-y-0.5 hover:bg-border/40">
                            <span class="inline-flex items-center gap-2"><i data-lucide="phone" class="h-4 w-4"></i> Ligar</span>
                            <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    @endif

                    <a href="/contato" class="group inline-flex min-w-[130px] items-center justify-between gap-3 rounded-xl border border-border bg-background/80 px-5 py-2.5 text-sm font-bold text-foreground shadow-sm transition-all hover:-translate-y-0.5 hover:bg-border/40">
                        <span class="inline-flex items-center gap-2"><i data-lucide="mail" class="h-4 w-4"></i> E-mail</span>
                        <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>

            {{-- Diferenciais --}}
            @if($showFeatures)
            <ul class="hidden flex-col gap-3 border-l border-border pl-8 md:flex">
                @foreach($features as $feature)
                    <li class="flex items-center gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                            <i data-lucide="{{ $feature['icon'] }}" class="h-5 w-5"></i>
                        </span>
                        <span class="flex flex-col leading-tight">
                            <strong class="text-sm font-bold text-foreground">{{ $feature['title'] }}</strong>
                            <span class="text-xs text-muted-foreground">{{ $feature['text'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>
            @endif

            {{-- Coluna do modelo: a imagem ancora na base do card e sobressai pelo topo --}}
            @if($hasModel)
                <div class="relative hidden self-stretch md:block" style="min-height: 9rem">
                    <img src="{{ $modelUrl }}" alt="Atendente Codhous"
                         class="pointer-events-none absolute left-1/2 z-10 w-auto max-w-none select-none object-contain object-bottom drop-shadow-2xl"
                         style="height: {{ $imageSize }}%; bottom: calc(-1.75rem + {{ (int) $imageOffsetY }}px); transform: translateX(calc(-50% + {{ $imageOffsetX }}px));">
                </div>
            @endif
        </div>
    </div>
</div>
