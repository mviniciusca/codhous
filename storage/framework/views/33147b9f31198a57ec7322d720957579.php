<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'meta' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title' => null,
    'meta' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
<head>
    <?php if (isset($component)) { $__componentOriginalbbed054b083bfd0a8c6b9cc4fd839f01 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbbed054b083bfd0a8c6b9cc4fd839f01 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-head','data' => ['title' => $title]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbbed054b083bfd0a8c6b9cc4fd839f01)): ?>
<?php $attributes = $__attributesOriginalbbed054b083bfd0a8c6b9cc4fd839f01; ?>
<?php unset($__attributesOriginalbbed054b083bfd0a8c6b9cc4fd839f01); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbbed054b083bfd0a8c6b9cc4fd839f01)): ?>
<?php $component = $__componentOriginalbbed054b083bfd0a8c6b9cc4fd839f01; ?>
<?php unset($__componentOriginalbbed054b083bfd0a8c6b9cc4fd839f01); ?>
<?php endif; ?>
    <?php echo e($meta); ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="font-sans antialiased bg-background text-foreground">
    
    <?php if (isset($component)) { $__componentOriginalfdc8967a87956c0a7185abbef03fae20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfdc8967a87956c0a7185abbef03fae20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-header','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfdc8967a87956c0a7185abbef03fae20)): ?>
<?php $attributes = $__attributesOriginalfdc8967a87956c0a7185abbef03fae20; ?>
<?php unset($__attributesOriginalfdc8967a87956c0a7185abbef03fae20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfdc8967a87956c0a7185abbef03fae20)): ?>
<?php $component = $__componentOriginalfdc8967a87956c0a7185abbef03fae20; ?>
<?php unset($__componentOriginalfdc8967a87956c0a7185abbef03fae20); ?>
<?php endif; ?>   
    

    <main>
        
        <?php if (isset($component)) { $__componentOriginalcc2b6f54f08a7d7e868cd3ed75c160fe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc2b6f54f08a7d7e868cd3ed75c160fe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-home','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-home'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcc2b6f54f08a7d7e868cd3ed75c160fe)): ?>
<?php $attributes = $__attributesOriginalcc2b6f54f08a7d7e868cd3ed75c160fe; ?>
<?php unset($__attributesOriginalcc2b6f54f08a7d7e868cd3ed75c160fe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcc2b6f54f08a7d7e868cd3ed75c160fe)): ?>
<?php $component = $__componentOriginalcc2b6f54f08a7d7e868cd3ed75c160fe; ?>
<?php unset($__componentOriginalcc2b6f54f08a7d7e868cd3ed75c160fe); ?>
<?php endif; ?>
        <?php echo e($slot); ?>

        
    </main>

    <?php if (isset($component)) { $__componentOriginal222c87a019257fb1d70ae0ff46ab02e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal222c87a019257fb1d70ae0ff46ab02e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal222c87a019257fb1d70ae0ff46ab02e1)): ?>
<?php $attributes = $__attributesOriginal222c87a019257fb1d70ae0ff46ab02e1; ?>
<?php unset($__attributesOriginal222c87a019257fb1d70ae0ff46ab02e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal222c87a019257fb1d70ae0ff46ab02e1)): ?>
<?php $component = $__componentOriginal222c87a019257fb1d70ae0ff46ab02e1; ?>
<?php unset($__componentOriginal222c87a019257fb1d70ae0ff46ab02e1); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal4cd511beed065845b3399c440fbb825c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cd511beed065845b3399c440fbb825c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-whatsapp','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-whatsapp'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4cd511beed065845b3399c440fbb825c)): ?>
<?php $attributes = $__attributesOriginal4cd511beed065845b3399c440fbb825c; ?>
<?php unset($__attributesOriginal4cd511beed065845b3399c440fbb825c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4cd511beed065845b3399c440fbb825c)): ?>
<?php $component = $__componentOriginal4cd511beed065845b3399c440fbb825c; ?>
<?php unset($__componentOriginal4cd511beed065845b3399c440fbb825c); ?>
<?php endif; ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/components/filament-fabricator/layouts/default.blade.php ENDPATH**/ ?>