// Lucide Icons Initialization
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

// Mobile Menu Logic
window.toggleMobileMenu = function () {
    const menu = document.getElementById('mobile-menu');
    const openIcon = document.getElementById('menu-icon-open');
    const closeIcon = document.getElementById('menu-icon-close');

    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        openIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
        openIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    }
}

window.closeMobileMenu = function () {
    const menu = document.getElementById('mobile-menu');
    const openIcon = document.getElementById('menu-icon-open');
    const closeIcon = document.getElementById('menu-icon-close');

    menu.classList.add('hidden');
    openIcon.classList.remove('hidden');
    closeIcon.classList.add('hidden');
}

// Quote Form Logic - Simplified for modern CSS approach
window.toggleFormFields = function (type) {
    const concretoFields = document.getElementById('concreto-fields');
    if (type === 'concreto') {
        concretoFields.classList.remove('hidden');
    } else {
        concretoFields.classList.add('hidden');
    }
}

// Global scroll effect removed to maintain static header layout as requested.


// ─── Interações das seções ──────────────────────────────────────────────
// Tudo é progressivo: sem JS (ou na prévia do painel, dentro de iframe) o
// conteúdo aparece normalmente, só sem animação.

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const isEmbedded = window.self !== window.top;

// Revelar ao rolar: [data-reveal] (atraso opcional via style="--reveal-delay: 120ms")
function initReveal() {
    if (prefersReducedMotion || isEmbedded || !('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    const watch = (root) => root.querySelectorAll?.('[data-reveal]:not(.is-visible)').forEach((el) => observer.observe(el));

    document.documentElement.classList.add('reveal-on');
    watch(document);

    // Conteúdo inserido depois (Livewire, paginação) também entra no observador
    new MutationObserver((mutations) => mutations.forEach((m) => m.addedNodes.forEach((node) => {
        if (node.nodeType !== 1) return;
        if (node.matches('[data-reveal]')) observer.observe(node);
        watch(node);
    }))).observe(document.body, { childList: true, subtree: true });
}

// Brilho que segue o cursor nos cartões [data-spotlight]
function initSpotlight() {
    document.addEventListener('pointermove', (event) => {
        const card = event.target.closest?.('[data-spotlight]');
        if (!card) return;
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--spot-x', `${event.clientX - rect.left}px`);
        card.style.setProperty('--spot-y', `${event.clientY - rect.top}px`);
    }, { passive: true });
}

// Contador animado: [data-count] com texto como "12+", "98%", "1.500 m³"
function initCounters() {
    if (prefersReducedMotion || !('IntersectionObserver' in window)) return;

    const animate = (el) => {
        const match = el.textContent.trim().match(/^(\D*?)(\d[\d.,]*)(.*)$/s);
        if (!match) return;

        const [, prefix, raw, suffix] = match;
        const decimals = raw.includes(',') ? raw.split(',')[1].length : 0;
        const target = parseFloat(raw.replace(/\./g, '').replace(',', '.'));
        const format = (n) => n.toLocaleString('pt-BR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
            useGrouping: raw.includes('.'),
        });
        const duration = 1400;
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - progress, 4);
            el.textContent = prefix + format(target * eased) + suffix;
            if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    };

    const observer = new IntersectionObserver((entries) => entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        observer.unobserve(entry.target);
        animate(entry.target);
    }), { threshold: 0.6 });

    document.querySelectorAll('[data-count]').forEach((el) => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', () => {
    initReveal();
    initSpotlight();
    initCounters();
});
