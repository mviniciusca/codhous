@props([
    'badge' => 'APROVEITE ESSA MEGA OPORTUNIDADE',
    'title' => 'Parcelamento em até 12x sem juros',
    'subtitle' => 'ou com desconto no pagamento à vista em dinheiro ou com o pix.',
    'buttonLabel' => 'Fazer orçamento grátis',
    'buttonUrl' => '#',
    'whatsappNumber' => '',
    'backgroundImage' => null,
    'paymentMethodsImage' => null,
])

@php
    $website = \App\Models\Setting::get('website', []);
    $number = $whatsappNumber ?: data_get($website, 'features.whatsapp_widget.number', '');
    $whatsappUrl = $number 
        ? "https://wa.me/55" . preg_replace('/[^0-9]/', '', $number) . "?text=" . urlencode('Olá! Gostaria de fazer um orçamento.')
        : $buttonUrl;

    $bgUrl = $backgroundImage 
        ? (str_starts_with($backgroundImage, 'http') ? $backgroundImage : \Illuminate\Support\Facades\Storage::url($backgroundImage)) 
        : null;

    $methodsUrl = $paymentMethodsImage 
        ? (str_starts_with($paymentMethodsImage, 'http') ? $paymentMethodsImage : \Illuminate\Support\Facades\Storage::url($paymentMethodsImage)) 
        : null;
@endphp

<section class="relative bg-zinc-950 pt-10 pb-8 lg:pt-12 lg:pb-10 overflow-hidden border-y border-primary/20 shadow-xl shadow-primary/5" {{ $attributes }}>
    <!-- Background overlay se existir imagem -->
    @if($bgUrl)
        <div class="absolute inset-0 z-0">
            <img src="{{ $bgUrl }}" alt="Background" class="w-full h-full object-cover opacity-[0.25] scale-105 transition-transform duration-[2000ms] hover:scale-100">
            <div class="absolute inset-0 bg-zinc-950/80"></div>
        </div>
    @else
        <!-- Gradiente sofisticado caso não tenha imagem -->
        <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary/10 via-zinc-950 to-zinc-950"></div>
    @endif

    <div class="relative z-10 mx-auto max-w-5xl px-4 lg:px-8 text-center">
        @if($badge)
            <div class="mb-4 inline-flex items-center gap-2.5 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5 backdrop-blur-md shadow-lg shadow-primary/10">
                <span class="h-2 w-2 rounded-full bg-primary animate-pulse shadow-md shadow-primary/50"></span>
                <span class="font-bold text-primary uppercase tracking-widest text-xs">
                    {{ $badge }}
                </span>
            </div>
        @endif

        @if($title)
            <h2 class="font-mono text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-primary mb-2 drop-shadow-sm" style="text-wrap: balance;">
                {{ $title }}
            </h2>
        @endif

        @if($subtitle)
            <p class="text-base sm:text-lg font-medium text-zinc-300 mb-6 max-w-2xl mx-auto leading-relaxed" style="text-wrap: balance;">
                {{ $subtitle }}
            </p>
        @endif

        <div class="flex flex-col items-center gap-4">
            <div class="relative group">
                <div class="absolute -inset-1.5 rounded-full bg-gradient-to-r from-[#25D366] to-[#128C7E] opacity-60 blur-md transition duration-1000 group-hover:opacity-100 group-hover:duration-200"></div>
                <a href="{{ $whatsappUrl }}" target="_blank"
                   class="relative inline-flex items-center gap-3 rounded-full bg-[#25D366] px-6 py-3 text-sm sm:text-base font-extrabold text-white shadow-2xl transition-all hover:scale-105 hover:bg-[#20ba5a] border border-white/10">
                    <i data-lucide="message-circle" class="h-5 w-5"></i>
                    {{ $buttonLabel }}
                </a>
            </div>

            <!-- Meios de pagamento (Imagem customizada ou fallback) -->
            <div class="mt-2 opacity-70 transition-opacity hover:opacity-100">
                @if($methodsUrl)
                    <img src="{{ $methodsUrl }}" alt="Meios de Pagamento Aceitos" class="h-16 sm:h-20 md:h-28 w-auto object-contain mx-auto filter drop-shadow-lg">
                @else
                    <div class="flex items-center justify-center gap-5 flex-wrap text-zinc-400 text-xs sm:text-sm font-semibold tracking-widest">
                        <div class="flex items-center gap-1.5 hover:text-white transition-colors cursor-default">
                            <i data-lucide="credit-card" class="h-5 w-5"></i>
                            <span>VISA</span>
                        </div>
                        <div class="flex items-center gap-1.5 hover:text-white transition-colors cursor-default">
                            <i data-lucide="credit-card" class="h-5 w-5"></i>
                            <span>MASTER</span>
                        </div>
                        <div class="flex items-center gap-1.5 hover:text-white transition-colors cursor-default">
                            <i data-lucide="credit-card" class="h-5 w-5"></i>
                            <span>AMEX</span>
                        </div>
                        <div class="flex items-center gap-1.5 hover:text-white transition-colors cursor-default">
                            <i data-lucide="credit-card" class="h-5 w-5"></i>
                            <span>ELO</span>
                        </div>
                        <div class="flex items-center gap-1.5 hover:text-white transition-colors cursor-default">
                            <i data-lucide="credit-card" class="h-5 w-5"></i>
                            <span>HIPERCARD</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
