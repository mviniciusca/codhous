<x-filament-widgets::widget>
    {{ $this->form }}

    {{-- Script para processar notificações --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('notify', ({ style, message }) => {
                window.dispatchEvent(
                    new CustomEvent('notification', {
                        detail: {
                            message: message,
                            color: style === 'success' ? 'success' : 'danger',
                            icon: style === 'success' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle',
                            timeout: 5000 // 5 segundos
                        }
                    })
                );
            });
        });
    </script>
</x-filament-widgets::widget>
