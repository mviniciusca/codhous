<?php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Facades\FilamentView;

    $id = $getId();
    $imageCropAspectRatio = $getImageCropAspectRatio();
    $imageResizeTargetHeight = $getImageResizeTargetHeight();
    $imageResizeTargetWidth = $getImageResizeTargetWidth();
    $isAvatar = $isAvatar();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $hasImageEditor = $hasImageEditor();
    $hasCircleCropper = $hasCircleCropper();

    $alignment = $getAlignment() ?? Alignment::Start;

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }
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
<?php $component->withAttributes(['field' => $field,'label-tag' => 'div']); ?>
    <div
        <?php if(FilamentView::hasSpaMode()): ?>
            x-load="visible || event (ax-modal-opened)"
        <?php else: ?>
            x-load
        <?php endif; ?>
        x-load-src="<?php echo e(\Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('file-upload', 'filament/forms')); ?>"
        x-data="fileUploadFormComponent({
                    acceptedFileTypes: <?php echo \Illuminate\Support\Js::from($getAcceptedFileTypes())->toHtml() ?>,
                    imageEditorEmptyFillColor: <?php echo \Illuminate\Support\Js::from($getImageEditorEmptyFillColor())->toHtml() ?>,
                    imageEditorMode: <?php echo \Illuminate\Support\Js::from($getImageEditorMode())->toHtml() ?>,
                    imageEditorViewportHeight: <?php echo \Illuminate\Support\Js::from($getImageEditorViewportHeight())->toHtml() ?>,
                    imageEditorViewportWidth: <?php echo \Illuminate\Support\Js::from($getImageEditorViewportWidth())->toHtml() ?>,
                    deleteUploadedFileUsing: async (fileKey) => {
                        return await $wire.deleteUploadedFile(<?php echo \Illuminate\Support\Js::from($statePath)->toHtml() ?>, fileKey)
                    },
                    getUploadedFilesUsing: async () => {
                        return await $wire.getFormUploadedFiles(<?php echo \Illuminate\Support\Js::from($statePath)->toHtml() ?>)
                    },
                    hasImageEditor: <?php echo \Illuminate\Support\Js::from($hasImageEditor)->toHtml() ?>,
                    hasCircleCropper: <?php echo \Illuminate\Support\Js::from($hasCircleCropper)->toHtml() ?>,
                    canEditSvgs: <?php echo \Illuminate\Support\Js::from($canEditSvgs())->toHtml() ?>,
                    isSvgEditingConfirmed: <?php echo \Illuminate\Support\Js::from($isSvgEditingConfirmed())->toHtml() ?>,
                    confirmSvgEditingMessage: <?php echo \Illuminate\Support\Js::from(__('filament-forms::components.file_upload.editor.svg.messages.confirmation'))->toHtml() ?>,
                    disabledSvgEditingMessage: <?php echo \Illuminate\Support\Js::from(__('filament-forms::components.file_upload.editor.svg.messages.disabled'))->toHtml() ?>,
                    imageCropAspectRatio: <?php echo \Illuminate\Support\Js::from($imageCropAspectRatio)->toHtml() ?>,
                    imagePreviewHeight: <?php echo \Illuminate\Support\Js::from($getImagePreviewHeight())->toHtml() ?>,
                    imageResizeMode: <?php echo \Illuminate\Support\Js::from($getImageResizeMode())->toHtml() ?>,
                    imageResizeTargetHeight: <?php echo \Illuminate\Support\Js::from($imageResizeTargetHeight)->toHtml() ?>,
                    imageResizeTargetWidth: <?php echo \Illuminate\Support\Js::from($imageResizeTargetWidth)->toHtml() ?>,
                    imageResizeUpscale: <?php echo \Illuminate\Support\Js::from($getImageResizeUpscale())->toHtml() ?>,
                    isAvatar: <?php echo \Illuminate\Support\Js::from($isAvatar)->toHtml() ?>,
                    isDeletable: <?php echo \Illuminate\Support\Js::from($isDeletable())->toHtml() ?>,
                    isDisabled: <?php echo \Illuminate\Support\Js::from($isDisabled)->toHtml() ?>,
                    isDownloadable: <?php echo \Illuminate\Support\Js::from($isDownloadable())->toHtml() ?>,
                    isMultiple: <?php echo \Illuminate\Support\Js::from($isMultiple())->toHtml() ?>,
                    isOpenable: <?php echo \Illuminate\Support\Js::from($isOpenable())->toHtml() ?>,
                    isPasteable: <?php echo \Illuminate\Support\Js::from($isPasteable())->toHtml() ?>,
                    isPreviewable: <?php echo \Illuminate\Support\Js::from($isPreviewable())->toHtml() ?>,
                    isReorderable: <?php echo \Illuminate\Support\Js::from($isReorderable())->toHtml() ?>,
                    itemPanelAspectRatio: <?php echo \Illuminate\Support\Js::from($getItemPanelAspectRatio())->toHtml() ?>,
                    loadingIndicatorPosition: <?php echo \Illuminate\Support\Js::from($getLoadingIndicatorPosition())->toHtml() ?>,
                    locale: <?php echo \Illuminate\Support\Js::from(app()->getLocale())->toHtml() ?>,
                    panelAspectRatio: <?php echo \Illuminate\Support\Js::from($getPanelAspectRatio())->toHtml() ?>,
                    panelLayout: <?php echo \Illuminate\Support\Js::from($getPanelLayout())->toHtml() ?>,
                    placeholder: <?php echo \Illuminate\Support\Js::from($getPlaceholder())->toHtml() ?>,
                    maxFiles: <?php echo \Illuminate\Support\Js::from($getMaxFiles())->toHtml() ?>,
                    maxSize: <?php echo \Illuminate\Support\Js::from(($size = $getMaxSize()) ? "{$size}KB" : null)->toHtml() ?>,
                    minSize: <?php echo \Illuminate\Support\Js::from(($size = $getMinSize()) ? "{$size}KB" : null)->toHtml() ?>,
                    mimeTypeMap: <?php echo \Illuminate\Support\Js::from($getMimeTypeMap())->toHtml() ?>,
                    maxParallelUploads: <?php echo \Illuminate\Support\Js::from($getMaxParallelUploads())->toHtml() ?>,
                    removeUploadedFileUsing: async (fileKey) => {
                        return await $wire.removeFormUploadedFile(<?php echo \Illuminate\Support\Js::from($statePath)->toHtml() ?>, fileKey)
                    },
                    removeUploadedFileButtonPosition: <?php echo \Illuminate\Support\Js::from($getRemoveUploadedFileButtonPosition())->toHtml() ?>,
                    reorderUploadedFilesUsing: async (files) => {
                        return await $wire.reorderFormUploadedFiles(<?php echo \Illuminate\Support\Js::from($statePath)->toHtml() ?>, files)
                    },
                    shouldAppendFiles: <?php echo \Illuminate\Support\Js::from($shouldAppendFiles())->toHtml() ?>,
                    shouldOrientImageFromExif: <?php echo \Illuminate\Support\Js::from($shouldOrientImagesFromExif())->toHtml() ?>,
                    shouldTransformImage: <?php echo \Illuminate\Support\Js::from($imageCropAspectRatio || $imageResizeTargetHeight || $imageResizeTargetWidth)->toHtml() ?>,
                    state: $wire.<?php echo e($applyStateBindingModifiers("\$entangle('{$statePath}')")); ?>,
                    uploadButtonPosition: <?php echo \Illuminate\Support\Js::from($getUploadButtonPosition())->toHtml() ?>,
                    uploadingMessage: <?php echo \Illuminate\Support\Js::from($getUploadingMessage())->toHtml() ?>,
                    uploadProgressIndicatorPosition: <?php echo \Illuminate\Support\Js::from($getUploadProgressIndicatorPosition())->toHtml() ?>,
                    uploadUsing: (fileKey, file, success, error, progress) => {
                        $wire.upload(
                            `<?php echo e($statePath); ?>.${fileKey}`,
                            file,
                            () => {
                                success(fileKey)
                            },
                            error,
                            (progressEvent) => {
                                progress(true, progressEvent.detail.progress, 100)
                            },
                        )
                    },
                })"
        wire:ignore
        wire:key="<?php echo e($this->getId()); ?>.<?php echo e($statePath); ?>.<?php echo e($field::class); ?>.<?php echo e(substr(md5(serialize([
                $isDisabled,
            ])), 0, 64)); ?>"
        <?php echo e($attributes
                ->merge([
                    'aria-labelledby' => "{$id}-label",
                    'id' => $id,
                    'role' => 'group',
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'fi-fo-file-upload flex flex-col gap-y-2 [&_.filepond--root]:font-sans',
                    match ($alignment) {
                        Alignment::Start, Alignment::Left => 'items-start',
                        Alignment::Center => 'items-center',
                        Alignment::End, Alignment::Right => 'items-end',
                        default => $alignment,
                    },
                ])); ?>

    >
        <div
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'h-full',
                'w-32' => $isAvatar,
                'w-full' => ! $isAvatar,
            ]); ?>"
        >
            <input
                x-ref="input"
                <?php echo e($getExtraInputAttributeBag()
                        ->merge([
                            'aria-labelledby' => "{$id}-label",
                            'disabled' => $isDisabled,
                            'multiple' => $isMultiple(),
                            'type' => 'file',
                        ], escape: false)); ?>

            />
        </div>

        <div
            x-show="error"
            x-text="error"
            x-cloak
            class="text-sm text-danger-600 dark:text-danger-400"
        ></div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasImageEditor && (! $isDisabled)): ?>
            <div
                x-show="isEditorOpen"
                x-cloak
                x-on:click.stop=""
                x-trap.noscroll="isEditorOpen"
                x-on:keydown.escape.window="closeEditor"
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'fixed inset-0 isolate z-50 h-[100dvh] w-screen p-2 sm:p-10 md:p-20',
                    'fi-fo-file-upload-circle-cropper' => $hasCircleCropper,
                ]); ?>"
            >
                <div
                    aria-hidden="true"
                    class="fixed inset-0 h-full w-full cursor-pointer bg-black/50"
                    style="will-change: transform"
                ></div>

                <div
                    class="isolate z-10 flex h-full w-full items-center justify-center"
                >
                    <div
                        class="mx-auto flex h-full w-full flex-col overflow-hidden rounded-xl bg-white ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10 lg:flex-row"
                    >
                        <div class="w-full flex-1 overflow-auto p-4 lg:h-full">
                            <div class="h-full w-full">
                                <img x-ref="editor" class="h-full w-auto" />
                            </div>
                        </div>

                        <div
                            class="shadow-top z-[1] flex h-96 w-full flex-col overflow-auto bg-gray-50 dark:bg-gray-900/30 lg:h-full lg:max-w-xs lg:shadow-none"
                        >
                            <div class="flex-1 overflow-hidden">
                                <div
                                    class="flex h-full flex-col overflow-y-auto"
                                >
                                    <div class="flex-1 overflow-auto">
                                        <div class="space-y-6 p-4">
                                            <div class="w-full space-y-3">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                                                    [
                                                        'label' => __('filament-forms::components.file_upload.editor.fields.x_position.label'),
                                                        'ref' => 'xPositionInput',
                                                        'unit' => __('filament-forms::components.file_upload.editor.fields.x_position.unit'),
                                                        'alpineSaveHandler' => 'editor.setData({...editor.getData(true), x: +$el.value})',
                                                    ],
                                                    [
                                                        'label' => __('filament-forms::components.file_upload.editor.fields.y_position.label'),
                                                        'ref' => 'yPositionInput',
                                                        'unit' => __('filament-forms::components.file_upload.editor.fields.y_position.unit'),
                                                        'alpineSaveHandler' => 'editor.setData({...editor.getData(true), y: +$el.value})',
                                                    ],
                                                    [
                                                        'label' => __('filament-forms::components.file_upload.editor.fields.width.label'),
                                                        'ref' => 'widthInput',
                                                        'unit' => __('filament-forms::components.file_upload.editor.fields.width.unit'),
                                                        'alpineSaveHandler' => 'editor.setData({...editor.getData(true), width: +$el.value})',
                                                    ],
                                                    [
                                                        'label' => __('filament-forms::components.file_upload.editor.fields.height.label'),
                                                        'ref' => 'heightInput',
                                                        'unit' => __('filament-forms::components.file_upload.editor.fields.height.unit'),
                                                        'alpineSaveHandler' => 'editor.setData({...editor.getData(true), height: +$el.value})',
                                                    ],
                                                    [
                                                        'label' => __('filament-forms::components.file_upload.editor.fields.rotation.label'),
                                                        'ref' => 'rotationInput',
                                                        'unit' => __('filament-forms::components.file_upload.editor.fields.rotation.unit'),
                                                        'alpineSaveHandler' => 'editor.rotateTo(+$el.value)',
                                                    ],
                                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $input): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <label
                                                        class="flex w-full items-center rounded-lg border border-gray-300 bg-gray-100 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-800"
                                                    >
                                                        <span
                                                            class="flex w-20 shrink-0 items-center justify-center self-stretch border-e border-gray-300 px-2 dark:border-gray-700"
                                                        >
                                                            <?php echo e($input['label']); ?>

                                                        </span>

                                                        <input
                                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                                'block w-full border-none text-sm transition duration-75 focus-visible:border-primary-500 focus-visible:ring-1 focus-visible:ring-inset focus-visible:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus-visible:border-primary-500',
                                                            ]); ?>"
                                                            x-on:keyup.enter.stop.prevent="<?php echo e($input['alpineSaveHandler']); ?>"
                                                            x-on:blur="<?php echo e($input['alpineSaveHandler']); ?>"
                                                            x-ref="<?php echo e($input['ref']); ?>"
                                                            x-on:keydown.enter.prevent
                                                            type="text"
                                                        />

                                                        <span
                                                            class="flex w-16 items-center justify-center self-stretch border-s border-gray-300 px-2 dark:border-gray-700"
                                                        >
                                                            <?php echo e($input['unit']); ?>

                                                        </span>
                                                    </label>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            <div class="space-y-3">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $getImageEditorActions(iconSizeClasses: 'h-5 w-5 mx-auto'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupedActions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if (isset($component)) { $__componentOriginalfdab887de9877b48a57ef00a75a2effb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfdab887de9877b48a57ef00a75a2effb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.group','data' => ['class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full']); ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupedActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['color' => 'gray','grouped' => true,'icon' => new \Illuminate\Support\HtmlString($action['iconHtml']),'labelSrOnly' => true,'xOn:click.stop.prevent' => ''.e($action['alpineClickHandler']).'','xTooltip' => '{ content: ' . \Illuminate\Support\Js::from($action['label']) . ', theme: $store.theme }']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','grouped' => true,'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(new \Illuminate\Support\HtmlString($action['iconHtml'])),'label-sr-only' => true,'x-on:click.stop.prevent' => ''.e($action['alpineClickHandler']).'','x-tooltip' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('{ content: ' . \Illuminate\Support\Js::from($action['label']) . ', theme: $store.theme }')]); ?>
                                                                <?php echo e($action['label']); ?>

                                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfdab887de9877b48a57ef00a75a2effb)): ?>
<?php $attributes = $__attributesOriginalfdab887de9877b48a57ef00a75a2effb; ?>
<?php unset($__attributesOriginalfdab887de9877b48a57ef00a75a2effb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfdab887de9877b48a57ef00a75a2effb)): ?>
<?php $component = $__componentOriginalfdab887de9877b48a57ef00a75a2effb; ?>
<?php unset($__componentOriginalfdab887de9877b48a57ef00a75a2effb); ?>
<?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($aspectRatios = $getImageEditorAspectRatiosForJs())): ?>
                                                <div class="space-y-3">
                                                    <div
                                                        class="text-xs text-gray-950 dark:text-white"
                                                    >
                                                        <?php echo e(__('filament-forms::components.file_upload.editor.aspect_ratios.label')); ?>

                                                    </div>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = collect($aspectRatios)->chunk(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ratiosChunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if (isset($component)) { $__componentOriginalfdab887de9877b48a57ef00a75a2effb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfdab887de9877b48a57ef00a75a2effb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.group','data' => ['class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full']); ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ratiosChunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $ratio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['xTooltip' => '{ content: ' . \Illuminate\Support\Js::from(__('filament-forms::components.file_upload.editor.actions.set_aspect_ratio.label', ['ratio' => $label])) . ', theme: $store.theme }','xOn:click.stop.prevent' => 'currentRatio = \''.e($label).'\'; editor.setAspectRatio('.e($ratio).')','color' => 'gray','xBind:class' => '{\'!bg-gray-50 dark:!bg-gray-700\': currentRatio === \''.e($label).'\'}','grouped' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-tooltip' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('{ content: ' . \Illuminate\Support\Js::from(__('filament-forms::components.file_upload.editor.actions.set_aspect_ratio.label', ['ratio' => $label])) . ', theme: $store.theme }'),'x-on:click.stop.prevent' => 'currentRatio = \''.e($label).'\'; editor.setAspectRatio('.e($ratio).')','color' => 'gray','x-bind:class' => '{\'!bg-gray-50 dark:!bg-gray-700\': currentRatio === \''.e($label).'\'}','grouped' => true]); ?>
                                                                    <?php echo e($label); ?>

                                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfdab887de9877b48a57ef00a75a2effb)): ?>
<?php $attributes = $__attributesOriginalfdab887de9877b48a57ef00a75a2effb; ?>
<?php unset($__attributesOriginalfdab887de9877b48a57ef00a75a2effb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfdab887de9877b48a57ef00a75a2effb)): ?>
<?php $component = $__componentOriginalfdab887de9877b48a57ef00a75a2effb; ?>
<?php unset($__componentOriginalfdab887de9877b48a57ef00a75a2effb); ?>
<?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center gap-3 px-4 py-3"
                                    >
                                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['color' => 'gray','xOn:click.prevent' => 'pond.imageEditEditor.oncancel']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'gray','x-on:click.prevent' => 'pond.imageEditEditor.oncancel']); ?>
                                            <?php echo e(__('filament-forms::components.file_upload.editor.actions.cancel.label')); ?>

                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

                                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['color' => 'warning','xOn:click.stop.prevent' => 'editor.reset()','class' => 'ml-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'warning','x-on:click.stop.prevent' => 'editor.reset()','class' => 'ml-auto']); ?>
                                            <?php echo e(__('filament-forms::components.file_upload.editor.actions.reset.label')); ?>

                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

                                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['color' => 'success','xOn:click.prevent' => 'saveEditor']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'success','x-on:click.prevent' => 'saveEditor']); ?>
                                            <?php echo e(__('filament-forms::components.file_upload.editor.actions.save.label')); ?>

                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
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
<?php /**PATH /home/marvincoelho/projects/codhous/vendor/filament/forms/resources/views/components/file-upload.blade.php ENDPATH**/ ?>