<section class="relative flex min-h-[90vh] items-center overflow-hidden bg-foreground pt-16">
    
    <div class="pointer-events-none absolute inset-0 opacity-5">
        <div class="absolute left-1/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-2/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-3/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-0 top-1/3 h-px w-full bg-background"></div>
        <div class="absolute left-0 top-2/3 h-px w-full bg-background"></div>
    </div>

    <div  class="relative mx-auto w-full max-w-7xl px-4 py-20 lg:px-8">
        <div class="flex flex-col items-start gap-10 lg:flex-row lg:items-center lg:gap-16">
            
            <div class="flex-1">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5">
                    <span class="h-2 w-2 rounded-full bg-primary"></span>
                    <span class="text-xs font-medium uppercase tracking-wider text-primary">Qualidade Certificada</span>
                </div>

                <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-background md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                    <?php echo str_replace('agilidade', '<span class="text-primary">agilidade</span>', data_get($mainSlide, 'title', 'Concreto usinado com <span class="text-primary">agilidade</span> e precisao no traco')); ?>

                </h1>

                <p class="mt-6 max-w-xl text-lg leading-relaxed text-background/60">
                    <?php echo e(data_get($mainSlide, 'subtitle', 'Entrega rapida, rastreamento em tempo real e suporte tecnico especializado para garantir o sucesso da sua obra do inicio ao fim.')); ?>

                </p>

                
                <div class="mt-10 flex flex-wrap items-center gap-6 lg:gap-10">
                    <div>
                        <p class="font-mono text-3xl font-bold text-primary">500+</p>
                        <p class="text-xs text-background/50">Obras atendidas</p>
                    </div>
                    <div class="h-10 w-px bg-background/10"></div>
                    <div>
                        <p class="font-mono text-3xl font-bold text-background">98%</p>
                        <p class="text-xs text-background/50">Pontualidade</p>
                    </div>
                    <div class="h-10 w-px bg-background/10"></div>
                    <div>
                        <p class="font-mono text-3xl font-bold text-background">15+</p>
                        <p class="text-xs text-background/50">Anos de experiência</p>
                    </div>
                </div>
            </div>

            
            <div class="w-full max-w-md flex-shrink-0">
                <div class="rounded-xl border border-background/10 bg-background/5 p-8 backdrop-blur-sm">
                    <div class="mb-6 flex items-center gap-3">
                        <div wire:ignore class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                            <i data-lucide="map-pin" class="h-5 w-5 text-primary"></i>
                        </div>
                        <div>
                            <h3 class="font-mono text-lg font-bold text-background">Atendemos sua região?</h3>
                            <p class="text-xs text-background/50">Digite o CEP da obra para verificar</p>
                        </div>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$success): ?>
                    <div id="hero-cep-form">
                        <div class="flex gap-3">
                            <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['wire:model.live.debounce.500ms' => 'cep','wire:keydown.enter' => 'lookupCep','id' => 'hero-cep-input','type' => 'text','maxlength' => '9','placeholder' => '00000-000','class' => 'flex-1 text-lg font-mono']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.500ms' => 'cep','wire:keydown.enter' => 'lookupCep','id' => 'hero-cep-input','type' => 'text','maxlength' => '9','placeholder' => '00000-000','class' => 'flex-1 text-lg font-mono']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
                            <button
                                wire:click="lookupCep"
                                id="hero-cep-btn"
                                class="flex items-center justify-center gap-2 rounded-md bg-primary px-5 py-3.5 font-semibold text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                <span wire:ignore wire:loading.remove wire:target="lookupCep">
                                    <i data-lucide="search" class="h-5 w-5"></i>
                                </span>
                                <span wire:ignore wire:loading wire:target="lookupCep">
                                    <i data-lucide="loader-2" class="h-5 w-5 animate-spin"></i>
                                </span>
                            </button>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($error): ?>
                            <p class="mt-2 text-xs text-red-500"><?php echo e($error); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($success): ?>
                    <div id="hero-cep-success">
                        <div class="rounded-lg border border-primary/30 bg-primary/10 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span wire:ignore class="flex items-center justify-center"><i data-lucide="check-circle" class="h-5 w-5 text-primary"></i></span>
                                <span class="font-semibold text-primary text-sm">Atendemos sua região!</span>
                            </div>
                            <p class="text-sm text-background/70"><?php echo e($addressText); ?></p>
                        </div>
                        <div class="mt-4 flex flex-col gap-3">
                            <a id="hero-whatsapp-link" href="https://wa.me/5511999999999?text=Ol%C3%A1!%20Verifiquei%20que%20voc%C3%AAs%20atendem%20minha%20regi%C3%A3o%20(<?php echo e($cep); ?>).%20Preciso%20de%20concreto%20usinado%20para%20minha%20obra." target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-2 rounded-md bg-[#25D366] px-5 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-[#1fb855]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Falar pelo WhatsApp
                            </a>
                            <a href="#orcamento" class="inline-flex items-center justify-center gap-2 rounded-md border border-primary/30 px-5 py-3 text-sm font-semibold text-primary transition-colors hover:bg-primary/10">
                                Solicitar Orçamento Online
                                <span wire:ignore class="flex items-center justify-center"><i data-lucide="arrow-right" class="h-4 w-4"></i></span>
                            </a>
                        </div>
                        <button wire:click="resetCepForm" class="mt-3 w-full text-center text-xs text-background/40 transition-colors hover:text-background/60">
                            Consultar outro CEP
                        </button>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="mt-6 flex items-center gap-4 border-t border-background/10 pt-5">
                        <div class="flex items-center gap-1.5 text-xs text-background/40">
                            <span wire:ignore class="flex items-center justify-center"><i data-lucide="shield-check" class="h-3.5 w-3.5 text-primary/60"></i></span>
                            Consulta gratuita
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-background/40">
                            <span wire:ignore class="flex items-center justify-center"><i data-lucide="clock" class="h-3.5 w-3.5 text-primary/60"></i></span>
                            Resposta em minutos
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/livewire/section-hero-cep.blade.php ENDPATH**/ ?>