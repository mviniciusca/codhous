<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['page']));

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

foreach (array_filter((['page']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<?php if (isset($component)) { $__componentOriginal6ee7b1329c61ca7c1ddbaecab002daa2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ee7b1329c61ca7c1ddbaecab002daa2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filament-fabricator.layouts.default','data' => ['page' => $page]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-fabricator.layouts.default'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['page' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($page)]); ?>

<?php echo e($slot ?? ""); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ee7b1329c61ca7c1ddbaecab002daa2)): ?>
<?php $attributes = $__attributesOriginal6ee7b1329c61ca7c1ddbaecab002daa2; ?>
<?php unset($__attributesOriginal6ee7b1329c61ca7c1ddbaecab002daa2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ee7b1329c61ca7c1ddbaecab002daa2)): ?>
<?php $component = $__componentOriginal6ee7b1329c61ca7c1ddbaecab002daa2; ?>
<?php unset($__componentOriginal6ee7b1329c61ca7c1ddbaecab002daa2); ?>
<?php endif; ?><?php /**PATH /home/marvincoelho/projects/codhous/storage/framework/views/7454d58a73dc71bfbf9705b5ea4ad434.blade.php ENDPATH**/ ?>