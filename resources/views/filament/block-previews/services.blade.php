@php
    $id = 'preview_' . uniqid();
    $html = view('filament.block-previews.iframe-wrapper', [
        'type' => 'services', 
        'data' => get_defined_vars(), 
        'id' => $id
    ])->render();
@endphp
<div class="w-full relative">
    <!-- Overlay invisível para evitar qualquer interação com o iframe -->
    <div class="absolute inset-0 z-50 cursor-grab active:cursor-grabbing"></div>
    <iframe id="{{ $id }}" class="w-full border-0 rounded-2xl shadow-sm bg-white" scrolling="no" srcdoc="{{ $html }}" style="min-height: 100px;"></iframe>
</div>

<script>
    if (!window.hasPreviewListener) {
        window.addEventListener('message', function(event) {
            if (event.data.type === 'resize' && event.data.id) {
                const iframe = document.getElementById(event.data.id);
                if (iframe) {
                    iframe.style.height = event.data.height + 'px';
                }
            }
        });
        window.hasPreviewListener = true;
    }
</script>