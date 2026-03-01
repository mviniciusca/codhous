<!-- Professional Invoice Header -->
<div class="pb-8 mb-12">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <?php echo e(__('Budget')); ?>

                </h1>
                <?php if (isset($component)) { $__componentOriginal986dce9114ddce94a270ab00ce6c273d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal986dce9114ddce94a270ab00ce6c273d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.badge','data' => ['color' => match($getRecord()->status) {
                    'pending' => 'warning',
                    'on going' => 'info',
                    'done' => 'success',
                    'ignored' => 'danger',
                    'default' => 'gray',
                },'size' => 'lg','class' => 'text-xs font-semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(match($getRecord()->status) {
                    'pending' => 'warning',
                    'on going' => 'info',
                    'done' => 'success',
                    'ignored' => 'danger',
                    'default' => 'gray',
                }),'size' => 'lg','class' => 'text-xs font-semibold']); ?>
                    <?php echo e(__(ucfirst($getRecord()->status))); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $attributes = $__attributesOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__attributesOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal986dce9114ddce94a270ab00ce6c273d)): ?>
<?php $component = $__componentOriginal986dce9114ddce94a270ab00ce6c273d; ?>
<?php unset($__componentOriginal986dce9114ddce94a270ab00ce6c273d); ?>
<?php endif; ?>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">
                #<?php echo e($getRecord()->code); ?>

            </p>
        </div>
        <div class="text-right">
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-1"><?php echo e(__('Issue Date')); ?></p>
            <p class="text-gray-900 dark:text-white text-lg font-semibold">
                <?php echo e(\Carbon\Carbon::parse($getRecord()->created_at)->format('d/m/Y')); ?>

            </p>
            <p class="text-gray-500 dark:text-gray-500 text-xs">
                <?php echo e(\Carbon\Carbon::parse($getRecord()->created_at)->format('H:i')); ?>

            </p>
        </div>
    </div>
</div>

<!-- Client and Delivery Information Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
    <!-- Customer Card -->
    <div class="rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                <?php echo e(__('Customer Information')); ?>

            </h3>
        </div>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('Name')); ?></p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(data_get($getRecord(),
                        'content.customer_name')); ?></p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('Email')); ?></p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e(data_get($getRecord(),
                        'content.customer_email')); ?></p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo e(__('Phone')); ?></p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e(data_get($getRecord(),
                        'content.customer_phone')); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Address Card -->
    <div class="rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                <?php echo e(__('Delivery Address')); ?>

            </h3>
        </div>
        <div class="space-y-2">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                <?php echo e(data_get($getRecord(), 'content.street')); ?>, <?php echo e(data_get($getRecord(), 'content.number', 'S/N')); ?>

            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <?php echo e(data_get($getRecord(), 'content.neighborhood')); ?>

            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <?php echo e(data_get($getRecord(), 'content.city')); ?> - <?php echo e(data_get($getRecord(), 'content.state')); ?>

            </p>
            <div class="flex items-center gap-2 pt-2">
                <span
                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium text-gray-900 dark:text-white">
                    CEP: <?php echo e(data_get($getRecord(), 'content.postcode')); ?>

                </span>
            </div>
        </div>
    </div>
</div>

<!-- Products Section -->
<div class="mb-12 pb-12">
    <div class="flex items-center gap-2 mb-4 py-3">
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
            <?php echo e(__('Products and Services')); ?>

        </h3>
    </div>

    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="">
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                <?php echo e(__('Product / Service')); ?>

                            </span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                <?php echo e(__('Location')); ?>

                            </span>
                        </th>
                        <th class="px-6 py-3 text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                <?php echo e(__('Quantity')); ?>

                            </span>
                        </th>
                        <th class="px-6 py-3 text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                <?php echo e(__('Unit Price')); ?>

                            </span>
                        </th>
                        <th class="px-6 py-3 text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                <?php echo e(__('Subtotal')); ?>

                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php
                    $products = collect(data_get($getRecord(), 'content.products', []));
                    $subtotalSum = 0;
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                    $productModel = \App\Models\Product::find(data_get($product, 'product'));
                    $productOptionModel = \App\Models\ProductOption::find(data_get($product, 'product_option'));
                    $locationModel = \App\Models\Location::find(data_get($product, 'location'));
                    $subtotal = data_get($product, 'subtotal', 0);
                    $subtotalSum += $subtotal;
                    ?>

                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded flex items-center justify-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400"><?php echo e($index + 1); ?></span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                        <?php echo e($productModel?->name ?? 'Produto não encontrado'); ?>

                                    </p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productOptionModel): ?>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <?php echo e($productOptionModel->name); ?>

                                    </p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                <?php echo e($locationModel?->name ?? 'N/A'); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                <?php echo e(number_format(data_get($product, 'quantity', 0), 2, ',', '.')); ?> m³
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                <?php echo e(env('CURRENCY_SUFFIX')); ?> <?php echo e(number_format(data_get($product, 'price', 0), 2, ',',
                                '.')); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                <?php echo e(env('CURRENCY_SUFFIX')); ?> <?php echo e(number_format($subtotal, 2, ',', '.')); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12">
                            <div class="flex flex-col items-center justify-center text-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <?php echo e(__('No products found in this budget')); ?>

                                </p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isNotEmpty()): ?>
                <tfoot class="">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                                <?php echo e(__('Products Subtotal')); ?>:
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                <?php echo e(env('CURRENCY_SUFFIX')); ?> <?php echo e(number_format($subtotalSum, 2, ',', '.')); ?>

                            </span>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </table>
        </div>
    </div>
</div>

<!-- Financial Summary Section -->
<div class="mb-12 pb-12">
    <div class="rounded-lg p-6">
        <div class="flex items-center gap-2 mb-6 py-3">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                <?php echo e(__('Financial Summary')); ?>

            </h3>
        </div>

        <div class="flex flex-wrap gap-4">
            <!-- Total Quantity -->
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"><?php echo e(__('Total
                            Quantity')); ?></p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            <?php echo e(number_format(data_get($getRecord(), 'content.quantity', 0), 2, ',', '.')); ?> m³
                        </p>
                    </div>
                </div>
            </div>

            <!-- Shipping -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($getRecord(), 'content.shipping', 0) > 0): ?>
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"><?php echo e(__('Shipping')); ?></p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            <?php echo e(env('CURRENCY_SUFFIX')); ?> <?php echo e(number_format(data_get($getRecord(), 'content.shipping', 0),
                            2,
                            ',', '.')); ?>

                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Tax -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($getRecord(), 'content.tax', 0) > 0): ?>
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"><?php echo e(__('Taxes')); ?></p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            + <?php echo e(env('CURRENCY_SUFFIX')); ?> <?php echo e(number_format(data_get($getRecord(), 'content.tax', 0), 2,
                            ',', '.')); ?>

                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Discount -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($getRecord(), 'content.discount', 0) > 0): ?>
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"><?php echo e(__('Discount')); ?></p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            - <?php echo e(env('CURRENCY_SUFFIX')); ?> <?php echo e(number_format(data_get($getRecord(), 'content.discount',
                            0),
                            2, ',', '.')); ?>

                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Total -->
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-1"><?php echo e(__('Total Value')); ?></p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            <?php echo e(env('CURRENCY_SUFFIX')); ?> <?php echo e(number_format(data_get($getRecord(), 'content.total', 0), 2,
                            ',', '.')); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Notes -->
<div class="rounded-lg p-6">
    <div class="flex items-start gap-3 py-3">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2 uppercase tracking-wide">
                <?php echo e(__('Important Notes')); ?>

            </h4>
            <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                <li class="flex items-start gap-2">
                    <span class="text-gray-400 mt-0.5">•</span>
                    <span><?php echo e(__('This budget is valid for 30 days from the date of issue')); ?></span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-gray-400 mt-0.5">•</span>
                    <span><?php echo e(__('The values presented may change without prior notice')); ?></span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-gray-400 mt-0.5">•</span>
                    <span><?php echo e(__('To confirm this budget, contact us through our official channels')); ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/filament/forms/components/invoice.blade.php ENDPATH**/ ?>