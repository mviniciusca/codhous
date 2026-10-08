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
            
            $isModal = $alert->type === 'modal';
            $isToast = $alert->type === 'toast';
            $isBanner = $alert->type === 'banner';
            
            // Override position logically based on type
            $position = $alert->position;
            if ($isModal) {
                $position = 'center';
            } elseif ($isBanner && !in_array($position, ['top', 'bottom'])) {
                $position = 'top';
            } elseif ($isToast && in_array($position, ['top', 'bottom', 'center'])) {
                $position = 'top-right';
            }
            
            $isFullWidth = in_array($position, ['top', 'bottom']);
            
            // Design premium colors and glassmorphism
            $styleClasses = match($alert->style) {
                'promo' => 'bg-gradient-to-r from-primary to-primary/80 text-primary-foreground border-primary/20',
                'announcement' => 'bg-card/90 backdrop-blur-md text-card-foreground border-primary/30',
                'consent' => 'bg-foreground/95 backdrop-blur-md text-background border-border',
                'warning' => 'bg-amber-500/10 backdrop-blur-md text-amber-600 border-amber-500/20 dark:text-amber-400',
                'success' => 'bg-emerald-500/10 backdrop-blur-md text-emerald-600 border-emerald-500/20 dark:text-emerald-400',
                default => 'bg-background/95 backdrop-blur-xl text-foreground border-border/50',
            };
            
            $wrapperClass = match($position) {
                'top' => 'absolute top-0 left-0 right-0 border-b ' . $styleClasses,
                'bottom' => 'absolute bottom-0 left-0 right-0 border-t shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)] ' . $styleClasses,
                'top-left' => 'absolute top-6 left-6',
                'top-right' => 'absolute top-6 right-6',
                'bottom-left' => 'absolute bottom-6 left-6',
                'bottom-right' => 'absolute bottom-6 right-6',
                'center' => 'fixed inset-0 flex items-center justify-center p-4 z-[110]',
                default => 'absolute top-0 left-0 right-0 border-b ' . $styleClasses,
            };
            
            $cardClass = match($position) {
                'top-left', 'bottom-left', 'top-right', 'bottom-right' => 'w-full max-w-sm rounded-xl border shadow-2xl shadow-black/10 ring-1 ring-black/5',
                'center' => 'w-full max-w-lg rounded-2xl border shadow-2xl shadow-black/20 ring-1 ring-black/5 transform transition-all',
                default => 'w-full',
            };
            
            $linkClasses = match($alert->style) {
                'promo' => 'mt-3 inline-flex items-center text-sm font-semibold text-primary-foreground hover:text-white transition-all hover:translate-x-1',
                'consent' => 'mt-3 inline-flex items-center text-sm font-semibold text-background hover:opacity-80 transition-all hover:translate-x-1',
                default => 'mt-3 inline-flex items-center text-sm font-semibold text-primary hover:text-primary/80 transition-all hover:translate-x-1',
            };
            
            $icon = match($alert->style) {
                'success' => '<svg class="h-5 w-5 mt-0.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'warning' => '<svg class="h-5 w-5 mt-0.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
                'promo' => '<svg class="h-5 w-5 mt-0.5 opacity-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>',
                default => '<svg class="h-5 w-5 mt-0.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
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
            x-transition:enter="transition ease-out duration-400"
            x-transition:enter-start="opacity-0 {{ $isModal ? 'scale-95' : (str_contains($position, 'right') ? 'translate-x-8' : (str_contains($position, 'left') ? '-translate-x-8' : (str_contains($position, 'top') ? '-translate-y-4' : 'translate-y-4'))) }}"
            x-transition:enter-end="opacity-100 {{ $isModal ? 'scale-100' : 'translate-x-0 translate-y-0' }}"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 {{ $isModal ? 'scale-100' : 'translate-x-0 translate-y-0' }}"
            x-transition:leave-end="opacity-0 {{ $isModal ? 'scale-95' : (str_contains($position, 'right') ? 'translate-x-8' : (str_contains($position, 'left') ? '-translate-x-8' : (str_contains($position, 'top') ? '-translate-y-4' : 'translate-y-4'))) }}"
            class="pointer-events-auto {{ $wrapperClass }} {{ $isModal ? 'bg-black/60 backdrop-blur-sm' : '' }} {{ !$isFullWidth ? 'p-2 sm:p-4' : '' }}"
            style="display: none;"
        >
            @if($isModal)
            <div class="absolute inset-0" @click="if (dismissible) dismiss()" aria-hidden="true"></div>
            @endif
            @if($isFullWidth)
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 lg:px-8 {{ $isModal ? 'relative z-10' : '' }}" @if($isModal) @click.stop @endif>
            @else
            <div
                class="relative {{ $cardClass }} {{ $styleClasses }} p-5 sm:p-6 {{ $isModal ? 'z-10' : '' }}"
                @if($isModal) @click.stop @endif
            >
            @endif
                <div class="flex min-w-0 flex-1 items-start gap-4 {{ $isFullWidth ? 'items-center' : '' }}">
                    @if(!$isFullWidth)
                        <div class="shrink-0 rounded-full p-1">
                            {!! $icon !!}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        @if($alert->title)
                            <p class="font-sans text-base font-semibold tracking-tight {{ $isFullWidth ? 'text-base' : 'mb-1' }}">{{ $alert->title }}</p>
                        @endif
                        <p class="text-sm leading-relaxed {{ $alert->title ? 'mt-1' : '' }} {{ in_array($alert->style, ['promo', 'consent']) ? 'opacity-90' : 'opacity-80' }}">{{ $alert->message }}</p>
                        @if($alert->cta_label && $alert->cta_url)
                            <a href="{{ $alert->cta_url }}" target="_blank" rel="noopener" class="{{ $linkClasses }}">
                                {{ $alert->cta_label }}
                                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </a>
                        @endif
                    </div>
                    @if($alert->is_dismissible)
                        <button
                            type="button"
                            @click="dismiss()"
                            class="shrink-0 rounded-full p-2 opacity-60 transition-all hover:bg-black/5 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-primary dark:hover:bg-white/10"
                            aria-label="Fechar"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
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
