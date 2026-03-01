<?php
    $website = \App\Models\Setting::get('website', []);
    $features = data_get($website, 'features', []);
    $whatsapp = data_get($features, 'whatsapp_widget', []);
    $enabled = data_get($whatsapp, 'enabled', false);
    $number = data_get($whatsapp, 'number');
    $message = data_get($whatsapp, 'message', 'Olá, gostaria de solicitar um orçamento.');
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($enabled && $number): ?>
<a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $number)); ?>?text=<?php echo e(urlencode($message)); ?>" 
   target="_blank"
   class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition-transform hover:scale-110 active:scale-95"
   aria-label="WhatsApp">
    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-4.821 4.754a8.117 8.117 0 01-3.831-.963l-.274-.163-2.845.746.758-2.774-.179-.285a8.117 8.117 0 01-1.248-4.321c0-4.482 3.646-8.129 8.133-8.129 2.173 0 4.215.846 5.752 2.384s2.385 3.578 2.385 5.751c0 4.483-3.647 8.13-8.128 8.13m0-17.61C7.458 1.526 3.1 5.884 3.1 11.27c0 1.725.45 3.41 1.304 4.897L2.4 22.426l6.305-1.654a11.18 11.18 0 005.184 1.258h.005c5.385 0 9.742-4.358 9.742-9.744 0-2.61-1.015-5.064-2.86-6.91A9.68 9.68 0 0012.651 1.526z"/>
    </svg>
    <span class="absolute right-full mr-3 hidden whitespace-nowrap rounded-md bg-foreground px-3 py-1.5 text-xs font-medium text-background group-hover:block">Fale Conosco</span>
</a>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/components/site-whatsapp.blade.php ENDPATH**/ ?>