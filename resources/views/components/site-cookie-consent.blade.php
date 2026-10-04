@php
    $cookieConsentEnabled = \App\Models\Setting::get('security.cookie_consent_enabled', true);
@endphp

@if($cookieConsentEnabled)
<div id="cookie-consent-banner" class="fixed bottom-0 left-0 right-0 z-50 transform translate-y-full transition-transform duration-500 ease-in-out bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 shadow-[0_-10px_40px_rgba(0,0,0,0.1)] py-4 px-6 sm:px-10 lg:px-20" style="display: none;">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700 dark:text-gray-300">
            <strong>🍪 Valorizamos sua privacidade.</strong> Utilizamos cookies e tecnologias semelhantes para melhorar a sua experiência em nosso site, analisar o tráfego e personalizar o conteúdo. Ao continuar navegando, você concorda com a nossa política de privacidade.
        </div>
        <div class="flex-shrink-0 flex gap-2">
            <button id="btn-accept-cookies" class="whitespace-nowrap px-6 py-2.5 bg-primary hover:bg-primary/90 text-primary-foreground font-medium rounded-lg transition-colors shadow-lg shadow-primary/30">
                Aceitar e Fechar
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const consentBanner = document.getElementById('cookie-consent-banner');
        const acceptBtn = document.getElementById('btn-accept-cookies');
        const cookieName = 'codhous_cookie_consent';
        const consentDurationDays = 7;

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/; SameSite=Lax";
        }

        if (!getCookie(cookieName)) {
            // Show banner
            consentBanner.style.display = 'block';
            // Slight delay to allow display:block to apply before animating translate
            setTimeout(() => {
                consentBanner.classList.remove('translate-y-full');
            }, 100);
        }

        acceptBtn.addEventListener('click', function() {
            setCookie(cookieName, 'accepted', consentDurationDays);
            consentBanner.classList.add('translate-y-full');
            setTimeout(() => {
                consentBanner.style.display = 'none';
            }, 500); // Wait for transition to finish
        });
    });
</script>
@endif
