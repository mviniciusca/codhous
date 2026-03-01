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
$baseClasses = 'w-full rounded-md border px-4 py-3 focus:outline-none focus:ring-1 transition-colors disabled:cursor-not-allowed disabled:opacity-50 appearance-none bg-no-repeat bg-[url(\'data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23131313%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E\')] bg-[length:12px_auto] bg-[position:right_1rem_center] pr-10';
$colorClasses = $error 
    ? 'border-red-500 focus:border-red-500 focus:ring-red-500 text-red-900 placeholder:text-red-300' 
    : 'border-border bg-background text-foreground focus:border-primary focus:ring-primary placeholder:text-muted-foreground';
?>

<select <?php echo e($disabled ? 'disabled' : ''); ?> <?php echo $attributes->merge(['class' => $baseClasses . ' ' . $colorClasses]); ?>>
    <?php echo e($slot); ?>

</select>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/components/ui/select.blade.php ENDPATH**/ ?>