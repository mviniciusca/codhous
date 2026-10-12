{{-- Comportamento dos alertas do site (Alpine). Separado para a prévia do painel, que injeta o HTML depois do carregamento --}}
@once
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('alertWidget', (config) => ({
        visible: false,
        dismissible: config.dismissible ?? true,
        init() {
            if (config.useCookie && this.getCookie(config.cookieKey)) {
                this.visible = false;
                return;
            }
            this.visible = true;
        },
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            return parts.length === 2 ? parts.pop().split(';').shift() : null;
        },
        setCookie(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
            document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/;SameSite=Lax`;
        },
        dismiss() {
            if (config.useCookie) {
                this.setCookie(config.cookieKey, '1', config.cookieDays);
            }
            this.visible = false;
        },
    }));
});
</script>
@endonce
