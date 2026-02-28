<?php
    $offColor = $getOffColor() ?? 'gray';
    $onColor = $getOnColor() ?? 'primary';
    $statePath = $getStatePath();
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $getFieldWrapperView()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => $field,'inline-label-vertical-alignment' => \Filament\Support\Enums\VerticalAlignment::Center]); ?>
    
            <?php $content = (function ($args) {
                return function () use ($args) {
                    extract($args, EXTR_SKIP);
                    ob_start(); ?>
        
        <button
            wire:key="<?php echo e($this->getId()); ?>"
            x-data="{
                state: $wire.<?php echo e($applyStateBindingModifiers("\$entangle('{$statePath}')")); ?>,
                checkboxes: [],
                checkboxLists: [],
                toggleAll: function () {
                    this.state = ! this.state;
                    this.checkboxLists.forEach(checkboxList => {
                        Alpine.$data(checkboxList.parentNode).areAllCheckboxesChecked = false;
                        Alpine.$data(checkboxList.parentNode).checkIfAllCheckboxesAreChecked()
                        Alpine.$data(checkboxList.parentNode).updateVisibleCheckboxListOptions();
                        Alpine.$data(checkboxList.parentNode).toggleAllCheckboxes();
                    })

                    this.checkboxes.forEach(checkbox => {
                        checkbox.checked = this.state;
                    });
                    this.updateStateBasedOnCheckboxes();
                },

                updateStateBasedOnCheckboxes: function () {
                    this.state = this.checkboxes.every(checkbox => checkbox.checked );
                },

                init: function() {
                    this.checkboxLists = Array.from(document.querySelectorAll('.fi-fo-checkbox-list'))
                    this.checkboxes = Array.from(document.querySelectorAll('.fi-fo-checkbox-list-option-label input[type=\'checkbox\']'));


                    this.checkboxes.forEach((checkbox) => {
                        checkbox.addEventListener('change', () => {
                            this.updateStateBasedOnCheckboxes();
                        });
                    });

                    $nextTick(() => {
                        this.updateStateBasedOnCheckboxes();
                    });

                    $watch('state', (value, old) => {
                        if (value === old) {
                            this.toggleAll();
                        }
                    });
                }
            }"
            x-init="init()"
            x-on:click="toggleAll();"
            x-bind:class="
                state
                    ? '<?php echo e(match ($onColor) {
                            'gray' => 'fi-color-gray bg-gray-200 dark:bg-gray-700',
                            default => 'fi-color-custom bg-custom-600',
                        }); ?>'
                    : '<?php echo e(match ($offColor) {
                            'gray' => 'fi-color-gray bg-gray-200 dark:bg-gray-700',
                            default => 'fi-color-custom bg-custom-600',
                        }); ?>'
            "
            x-bind:style="
                state
                    ? '<?php echo e(\Filament\Support\get_color_css_variables(
                            $onColor,
                            shades: [600],
                            alias: 'forms::components.toggle.on',
                        )); ?>'
                    : '<?php echo e(\Filament\Support\get_color_css_variables(
                            $offColor,
                            shades: [600],
                            alias: 'forms::components.toggle.off',
                        )); ?>'
            "
            <?php echo e($attributes
                    ->merge([
                        'aria-checked' => 'false',
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabled(),
                        'id' => $getId(),
                        'role' => 'switch',
                        'type' => 'button',
                        'wire:loading.attr' => 'disabled',
                        'wire:target' => $statePath,
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraAlpineAttributes(), escape: false)
                    ->class(['fi-fo-toggle relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent outline-none transition-colors duration-200 ease-in-out disabled:pointer-events-none disabled:opacity-70'])); ?>

        >
            <span
                class="relative inline-block w-5 h-5 transition duration-200 ease-in-out transform bg-white rounded-full shadow pointer-events-none ring-0"
                x-bind:class="{
                    'translate-x-5 rtl:-translate-x-5': state,
                    'translate-x-0': ! state,
                }"
            >
                <span
                    class="absolute inset-0 flex items-center justify-center w-full h-full transition-opacity"
                    aria-hidden="true"
                    x-bind:class="{
                        'opacity-0 ease-out duration-100': state,
                        'opacity-100 ease-in duration-200': ! state,
                    }"
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOffIcon()): ?>
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => $getOffIcon(),'class' => \Illuminate\Support\Arr::toCssClasses([
                                'fi-fo-toggle-off-icon h-3 w-3',
                                match ($offColor) {
                                    'gray' => 'text-gray-400 dark:text-gray-700',
                                    default => 'text-custom-600',
                                },
                            ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($getOffIcon()),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([
                                'fi-fo-toggle-off-icon h-3 w-3',
                                match ($offColor) {
                                    'gray' => 'text-gray-400 dark:text-gray-700',
                                    default => 'text-custom-600',
                                },
                            ]))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>

                <span
                    class="absolute inset-0 flex items-center justify-center w-full h-full transition-opacity"
                    aria-hidden="true"
                    x-bind:class="{
                        'opacity-100 ease-in duration-200': state,
                        'opacity-0 ease-out duration-100': ! state,
                    }"
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOnIcon()): ?>
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => $getOnIcon(),'xCloak' => 'x-cloak','class' => \Illuminate\Support\Arr::toCssClasses([
                                'fi-fo-toggle-on-icon h-3 w-3',
                                match ($onColor) {
                                    'gray' => 'text-gray-400 dark:text-gray-700',
                                    default => 'text-custom-600',
                                },
                            ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($getOnIcon()),'x-cloak' => 'x-cloak','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([
                                'fi-fo-toggle-on-icon h-3 w-3',
                                match ($onColor) {
                                    'gray' => 'text-gray-400 dark:text-gray-700',
                                    default => 'text-custom-600',
                                },
                            ]))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </span>
        </button>
    
            <?php return new \Illuminate\Support\HtmlString(ob_get_clean()); };
                })(get_defined_vars()); ?>
        

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isInline()): ?>
         <?php $__env->slot('labelPrefix', null, []); ?> 
            <?php echo e($content()); ?>

         <?php $__env->endSlot(); ?>
    <?php else: ?>
        <?php echo e($content()); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH /home/marvincoelho/projects/codhous/vendor/bezhansalleh/filament-shield/resources/views/forms/shield-toggle.blade.php ENDPATH**/ ?>