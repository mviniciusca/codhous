<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\Pboivin\FilamentPeek\Support\View::needsPreviewModal()): ?>
    <div
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="filament-peek-modal-title"
        x-data="PeekPreviewModal({
            devicePresets: <?php echo \Illuminate\Support\Js::from(config('filament-peek.devicePresets', false))->toHtml() ?>,
            initialDevicePreset: <?php echo \Illuminate\Support\Js::from(config('filament-peek.initialDevicePreset', 'fullscreen'))->toHtml() ?>,
            allowIframeOverflow: <?php echo \Illuminate\Support\Js::from(config('filament-peek.allowIframeOverflow', false))->toHtml() ?>,
            shouldCloseModalWithEscapeKey: <?php echo \Illuminate\Support\Js::from(config('filament-peek.closeModalWithEscapeKey', true))->toHtml() ?>,
            editorAutoRefreshDebounceTime: <?php echo \Illuminate\Support\Js::from(config('filament-peek.builderEditor.autoRefreshDebounceMilliseconds', 500))->toHtml() ?>,
            shouldRestoreIframePositionOnRefresh: <?php echo \Illuminate\Support\Js::from(config('filament-peek.builderEditor.preservePreviewScrollPosition', false))->toHtml() ?>,
            canResizeEditorSidebar: <?php echo \Illuminate\Support\Js::from(config('filament-peek.builderEditor.canResizeSidebar', true))->toHtml() ?>,
            editorSidebarMinWidth: <?php echo \Illuminate\Support\Js::from(config('filament-peek.builderEditor.sidebarMinWidth', '30rem'))->toHtml() ?>,
            editorSidebarInitialWidth: <?php echo \Illuminate\Support\Js::from(config('filament-peek.builderEditor.sidebarInitialWidth', '30rem'))->toHtml() ?>,
        })"
        x-bind:class="{
            'filament-peek-modal': true,
            'is-filament-peek-editor-resizing': editorIsResizing,
        }"
        x-bind:style="modalStyle"
        x-on:open-preview-tab.window="onOpenPreviewTab($event)"
        x-on:open-preview-modal.window="onOpenPreviewModal($event)"
        x-on:refresh-preview-modal.window="onRefreshPreviewModal($event)"
        x-on:close-preview-modal.window="onClosePreviewModal($event)"
        x-on:keyup.escape.window="handleEscapeKey()"
        x-on:mouseup.window="onMouseUp($event)"
        x-on:mousemove.debounce.5ms.window="onMouseMove($event)"
        x-trap="isOpen"
        x-cloak
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\Pboivin\FilamentPeek\Support\View::needsBuilderEditor()): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('filament-peek::builder-editor');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-873379320-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="filament-peek-panel filament-peek-preview">
            <div class="filament-peek-panel-header">
                <div
                    id="filament-peek-modal-title"
                    class="filament-peek-modal-title"
                    x-text="modalTitle"
                ></div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('filament-peek.devicePresets', false)): ?>
                    <div class="filament-peek-device-presets">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('filament-peek.devicePresets'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $presetName => $presetConfig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button
                                type="button"
                                data-preset-name="<?php echo e($presetName); ?>"
                                x-on:click="setDevicePreset('<?php echo e($presetName); ?>')"
                                x-bind:class="{'is-active-device-preset': isActiveDevicePreset('<?php echo e($presetName); ?>')}"
                            >
                                <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => $presetConfig['icon'] ?? 'heroicon-o-computer-desktop','class' => \Illuminate\Support\Arr::toCssClasses(['rotate-90' => $presetConfig['rotateIcon'] ?? false])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($presetConfig['icon'] ?? 'heroicon-o-computer-desktop'),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['rotate-90' => $presetConfig['rotateIcon'] ?? false]))]); ?>
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
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <button
                            type="button"
                            class="filament-peek-rotate-preset"
                            x-on:click="rotateDevicePreset()"
                            x-bind:disabled="!canRotatePreset"
                        >
                            <?php echo $__env->make('filament-peek::partials.icon-rotate', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="filament-peek-modal-actions">
                    <?php echo $__env->make('filament-peek::partials.modal-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            <div
                x-ref="previewModalBody"
                class="<?php echo e(\Illuminate\Support\Arr::toCssClasses([
                    'filament-peek-panel-body' => true,
                    'allow-iframe-overflow' => config('filament-peek.allowIframeOverflow', false),
                ])); ?>"
            >
                <template x-if="iframeUrl">
                    <iframe
                        x-bind:src="iframeUrl"
                        x-bind:style="iframeStyle"
                        frameborder="0"
                    ></iframe>
                </template>

                <template x-if="!iframeUrl && iframeContent">
                    <iframe
                        x-bind:srcdoc="iframeContent"
                        x-bind:style="iframeStyle"
                        frameborder="0"
                    ></iframe>
                </template>

                <div class="filament-peek-iframe-cover"></div>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/marvincoelho/projects/codhous/vendor/pboivin/filament-peek/resources/views/preview-modal.blade.php ENDPATH**/ ?>