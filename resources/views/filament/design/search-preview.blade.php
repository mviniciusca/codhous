{{-- Prévia do resultado no Google, atualizada enquanto se digita (lê o estado do formulário no cliente) --}}
@php
    $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'seusite.com.br';
@endphp

<div
    class="ds-serp"
    x-data="{
        host: @js($host),
        get site() { return $wire.data?.settings?.website ?? {} },
        get fullTitle() {
            const name = (this.site.name || '').trim();
            const title = (this.site.title || '').trim();
            return [name, title].filter(Boolean).join(' | ') || 'Nome do site';
        },
        get description() { return (this.site.description || '').trim() },
        tone(length, ideal) { return length === 0 ? 'is-off' : (length > ideal ? 'is-warn' : 'is-ok') },
    }"
>
    <div class="ds-serp-head">
        <span>Prévia na busca</span>
        <span class="ds-serp-counts">
            <span x-bind:class="tone(fullTitle.length, 60)">Título <b x-text="fullTitle.length"></b>/60</span>
            <span x-bind:class="tone(description.length, 160)">Descrição <b x-text="description.length"></b>/160</span>
        </span>
    </div>

    <div class="ds-serp-card" aria-hidden="true">
        <div class="ds-serp-site">
            <span class="ds-serp-favicon" x-text="(site.name || '?').trim().charAt(0).toUpperCase()"></span>
            <span>
                <span class="ds-serp-name" x-text="site.name || 'Nome do site'"></span>
                <span class="ds-serp-url" x-text="'https://' + host"></span>
            </span>
        </div>
        <p class="ds-serp-title" x-text="fullTitle"></p>
        <p class="ds-serp-desc" x-bind:class="{ 'is-empty': ! description }" x-text="description || 'Sem descrição: o Google vai escolher um trecho da página.'"></p>
    </div>
</div>
