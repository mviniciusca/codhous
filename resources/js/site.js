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

