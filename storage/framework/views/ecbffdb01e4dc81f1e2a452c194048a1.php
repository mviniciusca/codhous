<?php
    $website = \App\Models\Setting::get('website', []);
    $homepage = data_get($website, 'homepage', []);
    $slideshow = data_get($homepage, 'slideshow', []);
    
    // For now, let's just use the first slide if available, otherwise default
    $mainSlide = count($slideshow) > 0 ? $slideshow[0] : [
        'title' => 'Concreto usinado com agilidade e precisao no traco',
        'subtitle' => 'Entrega rapida, rastreamento em tempo real e suporte tecnico especializado para garantir o sucesso da sua obra do inicio ao fim.',
    ];
?>

<section class="relative flex min-h-[90vh] items-center overflow-hidden bg-foreground pt-16">
    <div class="pointer-events-none absolute inset-0 opacity-5">
        <div class="absolute left-1/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-2/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-3/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-0 top-1/3 h-px w-full bg-background"></div>
        <div class="absolute left-0 top-2/3 h-px w-full bg-background"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 py-20 lg:px-8">
        <div class="flex flex-col items-start gap-8 lg:flex-row lg:items-center lg:gap-16">
            <div class="flex-1">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5">
                    <span class="h-2 w-2 rounded-full bg-primary"></span>
                    <span class="text-xs font-medium uppercase tracking-wider text-primary">Qualidade Certificada</span>
                </div>

                <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-background md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                    <?php echo str_replace('agilidade', '<span class="text-primary">agilidade</span>', data_get($mainSlide, 'title')); ?>

                </h1>

                <p class="mt-6 max-w-xl text-lg leading-relaxed text-background/60">
                    <?php echo e(data_get($mainSlide, 'subtitle')); ?>

                </p>

                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                    <a href="#orcamento" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-8 py-4 text-base font-semibold text-primary-foreground transition-all hover:bg-primary/90 hover:gap-3">
                        Solicitar Orçamento Agora
                        <i data-lucide="arrow-right" class="h-5 w-5"></i>
                    </a>
                    <a href="#calculadora" class="inline-flex items-center justify-center gap-2 rounded-md border border-background/20 px-8 py-4 text-base font-medium text-background transition-colors hover:bg-background/10">
                        Calcular Volume
                    </a>
                </div>
            </div>

            <div class="flex-1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-4">
                        <div class="rounded-lg border border-primary/20 bg-primary/10 p-6">
                            <p class="font-mono text-4xl font-bold text-primary">500+</p>
                            <p class="mt-1 text-sm text-background/60">Obras atendidas</p>
                        </div>
                        <div class="rounded-lg border border-background/10 bg-background/5 p-6">
                            <p class="font-mono text-4xl font-bold text-background">98%</p>
                            <p class="mt-1 text-sm text-background/60">Pontualidade</p>
                        </div>
                    </div>
                    <div class="mt-8 flex flex-col gap-4">
                        <div class="rounded-lg border border-background/10 bg-background/5 p-6">
                            <p class="font-mono text-4xl font-bold text-background">24h</p>
                            <p class="mt-1 text-sm text-background/60">Suporte técnico</p>
                        </div>
                        <div class="rounded-lg border border-primary/20 bg-primary/10 p-6">
                            <p class="font-mono text-4xl font-bold text-primary">15+</p>
                            <p class="mt-1 text-sm text-background/60">Anos de experiência</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/components/section-hero.blade.php ENDPATH**/ ?>