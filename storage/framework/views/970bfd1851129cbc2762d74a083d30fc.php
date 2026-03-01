<?php foreach ((['page']) as $__key => $__value) {
    $__consumeVariable = is_string($__key) ? $__key : $__value;
    $$__consumeVariable = is_string($__key) ? $__env->getConsumableComponentData($__key, $__value) : $__env->getConsumableComponentData($__value);
} ?>

<?php
    $title = $title ?? __('Request Your Budget');
    $subtitle = $subtitle ?? __('Fill in the form below and our team will get back to you within 24 hours');
?>

<section id="budget-tool" class="px-4 py-8 md:py-16">
    <div class="max-w-4xl mx-auto">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2"><?php echo e($title); ?></h2>
            <p class="text-sm md:text-base text-slate-500"><?php echo e($subtitle); ?></p>
        </div>

        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('budget');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-231929296-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
</section>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/components/filament-fabricator/page-blocks/budget-tool.blade.php ENDPATH**/ ?>