{{--
    Alertas do site em três formatos (modal, toast e banner) e seis estilos.
    A aparência de cada estilo (cores, ícone, rótulos) vem de App\Support\AlertAppearance;
    o CSS (prefixo sa-) fica em site-alerts/styles.

    Sem props, mostra os alertas ativos. A prévia do painel passa :alerts com o alerta ainda
    não salvo e :preview="true" (sempre visível, sem cookie e sem animação a cada atualização).
--}}
@props([
    'alerts' => null,
    'preview' => false,
])
@php
    use App\Models\Alert;
    use App\Support\AlertAppearance;

    $alerts ??= Alert::getActiveForFrontend();
@endphp
@if($alerts->isNotEmpty())
<x-site-alerts.styles />
<div id="site-alerts" class="sa-root" aria-live="polite">
    @foreach($alerts as $alert)
        @php
            $look = AlertAppearance::for($alert->style);
            $type = in_array($alert->type, [Alert::TYPE_MODAL, Alert::TYPE_TOAST, Alert::TYPE_BANNER], true) ? $alert->type : Alert::TYPE_BANNER;
            // Posição compatível com o tipo (modal no centro, banner em cima/embaixo, toast nos cantos)
            $position = $alert->resolvedPosition();
            $ctaUrl = $alert->hasCta() ? $alert->safeCtaUrl() : null;
            $newTab = $ctaUrl && $alert->ctaOpensInNewTab();
            $dismissible = (bool) $alert->is_dismissible;
            // Consentimento: o link (ex.: política de privacidade) vai no texto e o botão principal é "Aceitar"
            $inlineLink = $alert->style === Alert::STYLE_CONSENT;
            $titleId = 'sa-title-'.($alert->id ?: 'preview').'-'.$loop->index;

            $slideFrom = match (true) {
                $type === Alert::TYPE_MODAL => 'scale-95',
                str_contains($position, 'right') => 'translate-x-8',
                str_contains($position, 'left') => '-translate-x-8',
                str_contains($position, 'top') => '-translate-y-4',
                default => 'translate-y-4',
            };
            $slideTo = $type === Alert::TYPE_MODAL ? 'scale-100' : 'translate-x-0 translate-y-0';
        @endphp
        <div
            x-data="alertWidget({
                cookieKey: @js($alert->getCookieKey()),
                useCookie: @js($preview ? false : $alert->use_cookie),
                cookieDays: @js($alert->getCookieDurationDays()),
                dismissible: @js($dismissible),
            })"
            x-show="visible"
            @unless($preview)
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 {{ $slideFrom }}"
                x-transition:enter-end="opacity-100 {{ $slideTo }}"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 {{ $slideTo }}"
                x-transition:leave-end="opacity-0 {{ $slideFrom }}"
            @endunless
            class="sa sa--{{ $type }} sa--{{ $position }}"
            style="{{ AlertAppearance::cssVariables($alert->style) }} display: none;"
            data-alert-style="{{ $alert->style }}"
        >
            @if($type === Alert::TYPE_MODAL)
                <div class="sa-overlay" @click="if (dismissible) dismiss()" aria-hidden="true"></div>
                <div class="sa-modal" role="dialog" aria-modal="true" @if($alert->title) aria-labelledby="{{ $titleId }}" @else aria-label="Aviso" @endif>
                    <span class="sa-icon">{!! AlertAppearance::icon($alert->style) !!}</span>
                    <div class="sa-body">
                        @if($alert->title)
                            <p class="sa-title" id="{{ $titleId }}">{{ $alert->title }}</p>
                        @endif
                        <p class="sa-msg">{{ $alert->message }}</p>
                        @if($ctaUrl && $inlineLink)
                            <a href="{{ $ctaUrl }}" class="sa-inline-link" @if($newTab) target="_blank" rel="noopener noreferrer" @endif>{{ $alert->cta_label }}</a>
                        @endif

                        @if(($ctaUrl && ! $inlineLink) || $dismissible)
                            <div class="sa-actions">
                                @if($ctaUrl && ! $inlineLink)
                                    <a href="{{ $ctaUrl }}" class="sa-btn sa-btn--primary" @if($newTab) target="_blank" rel="noopener noreferrer" @endif>{{ $alert->cta_label }}</a>
                                @endif
                                @if($dismissible)
                                    @php $alone = ! $ctaUrl || $inlineLink; @endphp
                                    <button type="button" @class(['sa-btn', 'sa-btn--primary' => $alone, 'sa-btn--dark' => ! $alone]) @click="dismiss()" data-sa-dismiss>
                                        {{ $alone ? $look['dismissAlone'] : $look['dismiss'] }}
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                    @if($dismissible)
                        <button type="button" class="sa-x" @click="dismiss()" aria-label="Fechar" data-sa-dismiss>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            @elseif($type === Alert::TYPE_TOAST)
                <div class="sa-toast" role="status">
                    <span class="sa-icon">{!! AlertAppearance::icon($alert->style) !!}</span>
                    <div class="sa-body">
                        @if($alert->title)
                            <p class="sa-title">{{ $alert->title }}</p>
                        @endif
                        <p class="sa-msg">{{ $alert->message }}</p>
                        @if($ctaUrl)
                            <a href="{{ $ctaUrl }}" class="sa-toast-link" @if($newTab) target="_blank" rel="noopener noreferrer" @endif>
                                {{ $alert->cta_label }}
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endif
                    </div>
                    @if($dismissible)
                        <button type="button" class="sa-x" @click="dismiss()" aria-label="Fechar" data-sa-dismiss>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    @endif
                    <span class="sa-line" aria-hidden="true"></span>
                </div>
            @else
                <div class="sa-banner" role="region" aria-label="{{ $alert->title ?: 'Aviso' }}">
                    <div class="sa-banner-in">
                        <span class="sa-icon">{!! AlertAppearance::icon($alert->style) !!}</span>
                        <p @class(['sa-banner-text', 'has-title' => filled($alert->title)])>
                            @if($alert->title)
                                <strong>{{ $alert->title }}</strong>
                            @endif
                            <span>{{ $alert->message }}</span>
                        </p>
                        @if($ctaUrl)
                            <a href="{{ $ctaUrl }}" class="sa-btn sa-btn--light" @if($newTab) target="_blank" rel="noopener noreferrer" @endif>
                                {{ $alert->cta_label }}
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endif
                        @if($dismissible)
                            <button type="button" class="sa-x" @click="dismiss()" aria-label="Fechar" data-sa-dismiss>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
<x-site-alerts.script />
@endif
