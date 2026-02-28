<div
    x-data="{}"
    x-on:focus-first-global-search-result.stop="$el.querySelector('.fi-global-search-result-link')?.focus()"
    class="fi-global-search flex items-center"
>
    <?php echo e(\Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_START)); ?>


    <div class="sm:relative">
        <?php if (isset($component)) { $__componentOriginal1b0ab65785d9a1f9b804503a8a469787 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1b0ab65785d9a1f9b804503a8a469787 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.global-search.field','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::global-search.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1b0ab65785d9a1f9b804503a8a469787)): ?>
<?php $attributes = $__attributesOriginal1b0ab65785d9a1f9b804503a8a469787; ?>
<?php unset($__attributesOriginal1b0ab65785d9a1f9b804503a8a469787); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1b0ab65785d9a1f9b804503a8a469787)): ?>
<?php $component = $__componentOriginal1b0ab65785d9a1f9b804503a8a469787; ?>
<?php unset($__componentOriginal1b0ab65785d9a1f9b804503a8a469787); ?>
<?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results !== null): ?>
            <?php if (isset($component)) { $__componentOriginal0b1246772224f052c1dabd584ab4a6fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b1246772224f052c1dabd584ab4a6fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.global-search.results-container','data' => ['results' => $results]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::global-search.results-container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($results)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b1246772224f052c1dabd584ab4a6fc)): ?>
<?php $attributes = $__attributesOriginal0b1246772224f052c1dabd584ab4a6fc; ?>
<?php unset($__attributesOriginal0b1246772224f052c1dabd584ab4a6fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b1246772224f052c1dabd584ab4a6fc)): ?>
<?php $component = $__componentOriginal0b1246772224f052c1dabd584ab4a6fc; ?>
<?php unset($__componentOriginal0b1246772224f052c1dabd584ab4a6fc); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php echo e(\Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_END)); ?>

</div>
<?php /**PATH /home/marvincoelho/projects/codhous/vendor/filament/filament/resources/views/components/global-search/index.blade.php ENDPATH**/ ?>