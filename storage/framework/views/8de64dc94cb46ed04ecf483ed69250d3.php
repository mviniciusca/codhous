<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'activeLocale' => null,
    'activeManager',
    'content' => null,
    'contentTabLabel' => null,
    'contentTabIcon' => null,
    'contentTabPosition' => null,
    'managers',
    'ownerRecord',
    'pageClass',
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
    'activeLocale' => null,
    'activeManager',
    'content' => null,
    'contentTabLabel' => null,
    'contentTabIcon' => null,
    'contentTabPosition' => null,
    'managers',
    'ownerRecord',
    'pageClass',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="fi-resource-relation-managers flex flex-col gap-y-6">
    <?php
        $activeManager = strval($activeManager);
        $normalizeRelationManagerClass = function (string | Filament\Resources\RelationManagers\RelationManagerConfiguration $manager): string {
            if ($manager instanceof \Filament\Resources\RelationManagers\RelationManagerConfiguration) {
                return $manager->relationManager;
            }

            return $manager;
        };
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((count($managers) > 1) || $content): ?>
        <?php if (isset($component)) { $__componentOriginal447636fe67a19f9c79619fb5a3c0c28d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal447636fe67a19f9c79619fb5a3c0c28d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.tabs.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <?php
                $tabs = $managers;

                if ($content) {
                    match ($contentTabPosition) {
                        \Filament\Resources\Pages\ContentTabPosition::After => $tabs = array_merge($tabs, [null => null]),
                        default => $tabs = array_replace([null => null], $tabs),
                    };
                }
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $manager): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $tabKey = strval($tabKey);
                    $isGroup = $manager instanceof \Filament\Resources\RelationManagers\RelationGroup;

                    if ($isGroup) {
                        $manager->ownerRecord($ownerRecord);
                        $manager->pageClass($pageClass);
                    } elseif (filled($tabKey)) {
                        $manager = $normalizeRelationManagerClass($manager);
                    }
                ?>

                <?php if (isset($component)) { $__componentOriginal35d4caf141547fb7d125e4ebd3c1b66f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35d4caf141547fb7d125e4ebd3c1b66f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.tabs.item','data' => ['active' => $activeManager === $tabKey,'badge' => filled($tabKey) ? ($isGroup ? $manager->getBadge() : $manager::getBadge($ownerRecord, $pageClass)) : null,'badgeColor' => filled($tabKey) ? ($isGroup ? $manager->getBadgeColor() : $manager::getBadgeColor($ownerRecord, $pageClass)) : null,'badgeTooltip' => filled($tabKey) ? ($isGroup ? $manager->getBadgeTooltip() : $manager::getBadgeTooltip($ownerRecord, $pageClass)) : null,'icon' => filled($tabKey) ? ($isGroup ? $manager->getIcon() : $manager::getIcon($ownerRecord, $pageClass)) : ($contentTabIcon ?? null),'iconPosition' => filled($tabKey) ? ($isGroup ? $manager->getIconPosition() : $manager::getIconPosition($ownerRecord, $pageClass)) : ($contentTabIconPosition ?? null),'wire:click' => '$set(\'activeRelationManager\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::tabs.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeManager === $tabKey),'badge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($tabKey) ? ($isGroup ? $manager->getBadge() : $manager::getBadge($ownerRecord, $pageClass)) : null),'badge-color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($tabKey) ? ($isGroup ? $manager->getBadgeColor() : $manager::getBadgeColor($ownerRecord, $pageClass)) : null),'badge-tooltip' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($tabKey) ? ($isGroup ? $manager->getBadgeTooltip() : $manager::getBadgeTooltip($ownerRecord, $pageClass)) : null),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($tabKey) ? ($isGroup ? $manager->getIcon() : $manager::getIcon($ownerRecord, $pageClass)) : ($contentTabIcon ?? null)),'icon-position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filled($tabKey) ? ($isGroup ? $manager->getIconPosition() : $manager::getIconPosition($ownerRecord, $pageClass)) : ($contentTabIconPosition ?? null)),'wire:click' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('$set(\'activeRelationManager\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')')]); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($tabKey)): ?>
                        <?php echo e($isGroup ? $manager->getLabel() : $manager::getTitle($ownerRecord, $pageClass)); ?>

                    <?php elseif($content): ?>
                        <?php echo e($contentTabLabel); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal35d4caf141547fb7d125e4ebd3c1b66f)): ?>
<?php $attributes = $__attributesOriginal35d4caf141547fb7d125e4ebd3c1b66f; ?>
<?php unset($__attributesOriginal35d4caf141547fb7d125e4ebd3c1b66f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal35d4caf141547fb7d125e4ebd3c1b66f)): ?>
<?php $component = $__componentOriginal35d4caf141547fb7d125e4ebd3c1b66f; ?>
<?php unset($__componentOriginal35d4caf141547fb7d125e4ebd3c1b66f); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal447636fe67a19f9c79619fb5a3c0c28d)): ?>
<?php $attributes = $__attributesOriginal447636fe67a19f9c79619fb5a3c0c28d; ?>
<?php unset($__attributesOriginal447636fe67a19f9c79619fb5a3c0c28d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal447636fe67a19f9c79619fb5a3c0c28d)): ?>
<?php $component = $__componentOriginal447636fe67a19f9c79619fb5a3c0c28d; ?>
<?php unset($__componentOriginal447636fe67a19f9c79619fb5a3c0c28d); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($activeManager) && isset($managers[$activeManager])): ?>
        <div
            <?php if(count($managers) > 1): ?>
                id="relationManager<?php echo e(ucfirst($activeManager)); ?>"
                role="tabpanel"
            <?php endif; ?>
            wire:key="<?php echo e($this->getId()); ?>.relation-managers.active"
            class="flex flex-col gap-y-4"
        >
            <?php
                $managerLivewireProperties = ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass];

                if (filled($activeLocale)) {
                    $managerLivewireProperties['activeLocale'] = $activeLocale;
                }
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($managers[$activeManager] instanceof \Filament\Resources\RelationManagers\RelationGroup): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $managers[$activeManager]->ownerRecord($ownerRecord)->pageClass($pageClass)->getManagers(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupedManagerKey => $groupedManager): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $normalizedGroupedManagerClass = $normalizeRelationManagerClass($groupedManager);
                    ?>

                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split($normalizedGroupedManagerClass,
                        [...$managerLivewireProperties, ...(($groupedManager instanceof \Filament\Resources\RelationManagers\RelationManagerConfiguration) ? [...$groupedManager->relationManager::getDefaultProperties(), ...$groupedManager->getProperties()] : $groupedManager::getDefaultProperties())],);

$__key = "{$normalizedGroupedManagerClass}-{$groupedManagerKey}";

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2793319913-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <?php
                    $manager = $managers[$activeManager];
                    $normalizedManagerClass = $normalizeRelationManagerClass($manager);
                ?>

                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split($normalizedManagerClass,
                    [...$managerLivewireProperties, ...(($manager instanceof \Filament\Resources\RelationManagers\RelationManagerConfiguration) ? [...$manager->relationManager::getDefaultProperties(), ...$manager->getProperties()] : $manager::getDefaultProperties())],);

$__key = $normalizedManagerClass;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2793319913-1', $__key);

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
        </div>
    <?php elseif($content): ?>
        <?php echo e($content); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /home/marvincoelho/projects/codhous/vendor/filament/filament/resources/views/components/resources/relation-managers.blade.php ENDPATH**/ ?>