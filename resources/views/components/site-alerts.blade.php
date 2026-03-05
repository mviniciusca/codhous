@php
    use App\Models\Alert;
    $alerts = Alert::getActiveForFrontend();
@endphp
@if($alerts->isNotEmpty())
<div id="site-alerts" class="fixed inset-0 pointer-events-none z-[100]" aria-live="polite">
    @foreach($alerts as $alert)
        @php
            $cookieKey = $alert->getCookieKey();
            $cookieDays = $alert->getCookieDurationDays();
            $isFullWidth = in_array($alert->position, ['top', 'bottom']);
            $styleClasses = match($alert->style) {
                'promo' => 'bg-primary text-primary-foreground',
                'announcement' => 'bg-card text-card-foreground border-primary/30',
                'consent' => 'bg-foreground text-background',
                'warning' => 'bg-muted text-foreground border-border',
                'success' => 'bg-muted text-foreground border-border',
                default => 'bg-card text-card-foreground border-border',
            };
            $wrapperClass = match($alert->position) {
                'top' => 'absolute top-0 left-0 right-0 border-b border-border ' . $styleClasses,
                'bottom' => 'absolute bottom-0 left-0 right-0 border-t border-border ' . $styleClasses,
                'top-left' => 'absolute top-4 left-4',
                'top-right' => 'absolute top-4 right-4',
                'bottom-left' => 'absolute bottom-4 left-4',
                'bottom-right' => 'absolute bottom-4 right-4',
                'center' => 'absolute inset-0 flex items-center justify-center p-4',
                default => 'absolute top-0 left-0 right-0 border-b border-border ' . $styleClasses,
            };
            $cardClass = match($alert->position) {
                'top-left', 'bottom-left', 'top-right', 'bottom-right' => 'w-full max-w-sm rounded-lg border border-border shadow-lg',
                'center' => 'w-full max-w-md rounded-2xl border border-border shadow-2xl',
                default => 'w-full',
            };
            $isModal = $alert->type === 'modal';
            $linkClasses = match($alert->style) {
                'promo' => 'mt-3 inline-block text-sm font-medium text-primary-foreground/90 underline underline-offset-2 hover:text-primary-foreground',
                'consent' => 'mt-3 inline-block text-sm font-medium text-background/90 underline underline-offset-2 hover:text-background',
                default => 'mt-3 inline-block text-sm font-medium text-primary underline underline-offset-2 transition-colors hover:text-primary/80',
            };
        @endphp
        <div
            x-data="alertWidget({
                cookieKey: @js($cookieKey),
                useCookie: @js($alert->use_cookie),
                cookieDays: @js($cookieDays),
                dismissible: @js($alert->is_dismissible),
            })"
            x-show="visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto {{ $wrapperClass }} {{ $isModal ? 'bg-foreground/40' : '' }} {{ !$isFullWidth ? 'p-2 sm:p-4' : '' }}"
            style="display: none;"
        >
            @if($isModal)
            <div class="absolute inset-0" @click="if (dismissible) dismiss()" aria-hidden="true"></div>
            @endif
            @if($isFullWidth)
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 lg:px-8 {{ $isModal ? 'relative z-10' : '' }}" @if($isModal) @click.stop @endif>
            @else
            <div
                class="relative {{ $cardClass }} {{ $styleClasses }} p-4 border {{ $isModal ? 'z-10' : '' }}"
                @if($isModal) @click.stop @endif
            >
            @endif
                <div class="flex min-w-0 flex-1 items-start gap-3 {{ $isFullWidth ? 'items-center' : '' }}">
                    <div class="min-w-0 flex-1">
                        @if($alert->title)
                            <p class="font-mono text-sm font-bold tracking-tight {{ $isFullWidth ? 'text-base' : '' }}">{{ $alert->title }}</p>
                        @endif
                        <p class="text-sm {{ $alert->title ? 'mt-0.5' : '' }} {{ in_array($alert->style, ['promo', 'consent']) ? 'opacity-90' : 'text-muted-foreground' }}">{{ $alert->message }}</p>
                        @if($alert->cta_label && $alert->cta_url)
                            <a href="{{ $alert->cta_url }}" target="_blank" rel="noopener" class="{{ $linkClasses }}">
                                {{ $alert->cta_label }}
                            </a>
                        @endif
                    </div>
                    @if($alert->is_dismissible)
                        <button
                            type="button"
                            @click="dismiss()"
                            class="shrink-0 rounded-md p-1.5 opacity-70 transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            aria-label="Fechar"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
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
@endif
