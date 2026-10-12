// Entrada Vite do Estúdio de Posts (carregada só nas páginas do estúdio).
import './studio.css';
import { posterStudio } from './studio';
import { renderQueued } from './preview';

/** Miniatura renderizada no navegador a partir de um documento (galeria de templates). */
function posterPreview(doc, width = 260) {
    return {
        src: null,
        failed: false,
        init() {
            const observer = new IntersectionObserver((entries) => {
                if (!entries.some((e) => e.isIntersecting)) return;
                observer.disconnect();
                renderQueued(doc, { width }).then((url) => { this.src = url; }).catch(() => { this.failed = true; });
            });
            observer.observe(this.$el);
        },
    };
}

const register = () => {
    window.Alpine.data('posterStudio', posterStudio);
    window.Alpine.data('posterPreview', posterPreview);
};

if (window.Alpine) {
    register();
} else {
    document.addEventListener('alpine:init', register);
}
