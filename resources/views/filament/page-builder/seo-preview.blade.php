{{-- Prévia de SEO da aba "Configurações e SEO": resultado do Google, cartão de compartilhamento e checklist --}}
@php
    $ogState = data_get($this->data ?? [], 'meta.og_image');
    $ogFile = is_array($ogState) ? (array_values(array_filter($ogState))[0] ?? null) : $ogState;
    $ogUrl = null;

    if ($ogFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
        try {
            $ogUrl = $ogFile->temporaryUrl();
        } catch (\Throwable) {
            $ogUrl = null;
        }
    } elseif (is_string($ogFile) && filled($ogFile)) {
        $ogUrl = str_starts_with($ogFile, 'http') ? $ogFile : \Illuminate\Support\Facades\Storage::url($ogFile);
    }
@endphp

<div class="pe-settings-aside">
    <style>
        .pe-seo { --pe-seo-border: rgba(15, 23, 42, .08); --pe-seo-muted: rgb(var(--gray-500)); --pe-seo-text: rgb(var(--gray-950)); --pe-seo-surface: #fff; --pe-seo-soft: rgb(var(--gray-50)); display: flex; flex-direction: column; gap: 16px; }
        .dark .pe-seo { --pe-seo-border: rgba(255, 255, 255, .08); --pe-seo-muted: rgb(var(--gray-400)); --pe-seo-text: #fff; --pe-seo-surface: rgba(255, 255, 255, .03); --pe-seo-soft: rgba(255, 255, 255, .04); }
        .pe-seo-card { padding: 16px; border-radius: 12px; background: var(--pe-seo-surface); border: 1px solid var(--pe-seo-border); box-shadow: 0 1px 2px rgba(15, 23, 42, .04); }
        .pe-seo-label { display: flex; align-items: center; gap: 6px; margin-bottom: 12px; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--pe-seo-muted); }
        .pe-seo-label svg { width: 14px; height: 14px; }

        .pe-google { font-family: Arial, sans-serif; }
        .pe-google-site { display: flex; align-items: center; gap: 8px; }
        .pe-google-favicon { display: grid; place-items: center; width: 26px; height: 26px; border-radius: 999px; font-size: 12px; font-weight: 700; color: rgb(var(--primary-600)); background: rgba(var(--primary-500), .12); }
        .pe-google-name { font-size: 13px; color: var(--pe-seo-text); line-height: 1.2; }
        .pe-google-url { font-size: 12px; color: var(--pe-seo-muted); line-height: 1.3; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .pe-google-title { display: block; margin-top: 8px; font-size: 18px; line-height: 1.3; color: #1a0dab; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .dark .pe-google-title { color: #8ab4f8; }
        .pe-google-desc { margin-top: 4px; font-size: 13px; line-height: 1.55; color: var(--pe-seo-muted); overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; }
        .pe-google-desc.is-empty { font-style: italic; opacity: .7; }

        .pe-share { overflow: hidden; border-radius: 10px; border: 1px solid var(--pe-seo-border); background: var(--pe-seo-soft); }
        .pe-share-image { aspect-ratio: 1.91 / 1; width: 100%; object-fit: cover; display: block; background: var(--pe-seo-soft); }
        .pe-share-placeholder { aspect-ratio: 1.91 / 1; display: grid; place-items: center; align-content: center; gap: 6px; font-size: 12px; color: var(--pe-seo-muted); }
        .pe-share-placeholder svg { width: 28px; height: 28px; opacity: .6; }
        .pe-share-body { padding: 10px 12px; }
        .pe-share-host { font-size: 11px; text-transform: uppercase; color: var(--pe-seo-muted); }
        .pe-share-title { margin-top: 2px; font-size: 14px; font-weight: 700; color: var(--pe-seo-text); overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }

        .pe-check { display: flex; flex-direction: column; gap: 10px; }
        .pe-check-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: var(--pe-seo-text); }
        .pe-check-icon { display: grid; place-items: center; flex-shrink: 0; width: 20px; height: 20px; margin-top: 1px; border-radius: 999px; font-size: 12px; font-weight: 700; color: #fff; }
        .pe-check-icon.is-ok { background: rgb(var(--success-500)); }
        .pe-check-icon.is-warn { background: rgb(var(--warning-500)); }
        .pe-check-icon.is-off { background: rgb(var(--gray-300)); }
        .dark .pe-check-icon.is-off { background: rgb(var(--gray-600)); }
        .pe-check-hint { display: block; font-size: 12px; color: var(--pe-seo-muted); }
    </style>

    <div
        class="pe-seo"
        x-data="{
            siteName: @js($siteName),
            host: @js($host),
            get data() { return $wire.data || {} },
            get meta() { return this.data.meta || {} },
            get hasImage() {
                const value = this.meta.og_image;
                return !!value && (typeof value === 'string' || Object.values(value).some(Boolean));
            },
            get pageTitle() { return (this.data.title || '').trim() },
            get seoTitle() { return (this.meta.title || '').trim() || this.pageTitle },
            get fullTitle() { return this.seoTitle ? this.seoTitle + ' | ' + this.siteName : this.siteName },
            get description() { return (this.meta.description || '').trim() },
            get path() {
                const slug = (this.data.slug || '').replace(/^\/+/, '');
                return slug ? ' › ' + slug.split('/').join(' › ') : '';
            },
            get titleState() {
                const n = this.fullTitle.length;
                if (!this.seoTitle) return { status: 'off', hint: 'Dê um nome para a página.' };
                if (n > 60) return { status: 'warn', hint: n + ' caracteres com o nome do site: o Google deve cortar o final.' };
                return { status: 'ok', hint: n + ' de 60 caracteres.' };
            },
            get descriptionState() {
                const n = this.description.length;
                if (!n) return { status: 'off', hint: 'Sem descrição, o Google escolhe um trecho da página.' };
                if (n < 70) return { status: 'warn', hint: n + ' caracteres: escreva um pouco mais (ideal 120 a 160).' };
                if (n > 160) return { status: 'warn', hint: n + ' caracteres: o Google deve cortar depois de 160.' };
                return { status: 'ok', hint: n + ' de 160 caracteres.' };
            },
        }"
    >
        <div class="pe-seo-card">
            <div class="pe-seo-label"><x-filament::icon icon="heroicon-m-magnifying-glass" /> Prévia no Google</div>

            <div class="pe-google">
                <div class="pe-google-site">
                    <span class="pe-google-favicon" x-text="siteName.charAt(0).toUpperCase()"></span>
                    <div style="min-width: 0">
                        <div class="pe-google-name" x-text="siteName"></div>
                        <div class="pe-google-url" x-text="'https://' + host + path"></div>
                    </div>
                </div>
                <span class="pe-google-title" x-text="fullTitle"></span>
                <p class="pe-google-desc" x-bind:class="{ 'is-empty': !description }" x-text="description || 'Escreva uma descrição para controlar o texto que aparece aqui.'"></p>
            </div>
        </div>

        <div class="pe-seo-card">
            <div class="pe-seo-label"><x-filament::icon icon="heroicon-m-share" /> Ao compartilhar o link</div>

            <div class="pe-share">
                @if ($ogUrl)
                    <img src="{{ $ogUrl }}" alt="" class="pe-share-image">
                @else
                    <div class="pe-share-placeholder">
                        <x-filament::icon icon="heroicon-o-photo" />
                        <span>Sem imagem de compartilhamento</span>
                    </div>
                @endif
                <div class="pe-share-body">
                    <div class="pe-share-host" x-text="host"></div>
                    <div class="pe-share-title" x-text="fullTitle"></div>
                </div>
            </div>
        </div>

        <div class="pe-seo-card">
            <div class="pe-seo-label"><x-filament::icon icon="heroicon-m-check-badge" /> Checklist</div>

            <div class="pe-check">
                <div class="pe-check-item">
                    <span class="pe-check-icon" x-bind:class="'is-' + titleState.status" x-text="titleState.status === 'ok' ? '✓' : '!'"></span>
                    <span>Título na busca <span class="pe-check-hint" x-text="titleState.hint"></span></span>
                </div>
                <div class="pe-check-item">
                    <span class="pe-check-icon" x-bind:class="'is-' + descriptionState.status" x-text="descriptionState.status === 'ok' ? '✓' : '!'"></span>
                    <span>Descrição na busca <span class="pe-check-hint" x-text="descriptionState.hint"></span></span>
                </div>
                <div class="pe-check-item">
                    <span class="pe-check-icon" x-bind:class="hasImage ? 'is-ok' : 'is-off'" x-text="hasImage ? '✓' : '!'"></span>
                    <span>Imagem ao compartilhar <span class="pe-check-hint" x-text="hasImage ? 'Tudo certo.' : 'Opcional, mas deixa o link bem mais atrativo.'"></span></span>
                </div>
            </div>
        </div>
    </div>
</div>
