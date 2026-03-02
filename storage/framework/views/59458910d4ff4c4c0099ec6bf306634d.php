<div>
    <div class="fixed top-8 right-8 z-50 pointer-events-auto">
        <div 
            x-data="{ count: <?php if ((object) ('count') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('count'->value()); ?>')<?php echo e('count'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('count'); ?>')<?php endif; ?> }"
            class="relative group"
        >
            <button 
                type="button"
                class="relative p-4 text-white transition-all duration-300 transform bg-black dark:bg-white dark:text-black rounded-2xl shadow-xl border border-white/10 hover:scale-110 active:scale-95 group"
                aria-label="<?php echo e(__('Shopping Bag')); ?>"
            >
                <!-- Bag Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 transition-transform duration-500 group-hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.8)]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>

                <!-- Badge -->
                <span 
                    x-show="count > 0"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-50"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="absolute -top-2 -right-2 flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-xs font-black shadow-lg ring-4 ring-black/5 animate-pulse-subtle"
                >
                    <span x-text="count"></span>
                </span>
            </button>

            <!-- Tooltip/Hover effect -->
            <div class="absolute right-0 mt-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-y-2 group-hover:translate-y-0">
                <div class="bg-black text-white dark:bg-white dark:text-black backdrop-blur-md px-4 py-2 rounded-xl border border-white/10 shadow-xl text-sm font-medium whitespace-nowrap">
                    <span x-text="count"></span> <?php echo e(__('Items in your bag')); ?>

                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse-subtle {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .animate-pulse-subtle {
            animation: pulse-subtle 2s infinite ease-in-out;
        }
    </style>
</div>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/livewire/cart-bag.blade.php ENDPATH**/ ?>