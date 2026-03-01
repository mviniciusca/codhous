<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['disabled' => false, 'error' => false]));

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

foreach (array_filter((['disabled' => false, 'error' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$baseClasses = 'w-full rounded-md border px-4 py-3 focus:outline-none focus:ring-1 transition-colors disabled:cursor-not-allowed disabled:opacity-50 resize-y min-h-[80px]';
$colorClasses = $error 
    ? 'border-red-500 focus:border-red-500 focus:ring-red-500 text-red-900 placeholder:text-red-300' 
    : 'border-border bg-background text-foreground focus:border-primary focus:ring-primary placeholder:text-muted-foreground';
?>

<textarea <?php echo e($disabled ? 'disabled' : ''); ?> <?php echo $attributes->merge(['class' => $baseClasses . ' ' . $colorClasses]); ?>><?php echo e($slot); ?></textarea>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/components/ui/textarea.blade.php ENDPATH**/ ?>