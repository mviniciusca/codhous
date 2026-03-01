import './bootstrap';
import './site';

document.addEventListener('DOMContentLoaded', function () {
    window.addEventListener('copyShareLink', (event) => {
        const text = event.detail.text;
        if (text) {
            navigator.clipboard.writeText(text).then(() => {
                // Using Filament's notification system
                window.dispatchEvent(
                    new CustomEvent('notify', {
                        detail: {
                            message: 'Link copied to clipboard!',
                            icon: 'heroicon-o-clipboard-check',
                            iconColor: 'success',
                            timeout: 3000
                        }
                    })
                );
            }).catch((err) => {
                console.error('Failed to copy text: ', err);
                window.dispatchEvent(
                    new CustomEvent('notify', {
                        detail: {
                            message: 'Failed to copy link',
                            icon: 'heroicon-o-x-circle',
                            iconColor: 'danger',
                            timeout: 3000
                        }
                    })
                );
            });
        }
    });
});
