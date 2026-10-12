{{-- Live preview de uma seção (ContentSection) no construtor de seções do painel --}}
<x-layouts.app :title="'Pré-visualização · '.$label" :builder-preview="true" :bare="! $chrome">
    <style>
        html { scroll-behavior: auto !important; }
        .sb-section { display: flow-root; }
        .sb-error {
            margin: 24px;
            padding: 16px 18px;
            border-radius: 12px;
            border: 1px dashed rgba(220, 38, 38, .45);
            background: rgba(254, 226, 226, .6);
            color: #991b1b;
            font: 13px/1.5 system-ui, sans-serif;
        }
        .sb-error strong { display: block; margin-bottom: 2px; }
    </style>

    @if ($error)
        <div class="sb-error" data-sb-error>
            <strong>Não foi possível renderizar "{{ $label }}"</strong>
            Complete os campos obrigatórios da seção. ({{ \Illuminate\Support\Str::limit($error, 160) }})
        </div>
    @endif

    <div class="sb-section" data-sb-section>{!! $html !!}</div>

    {{-- Informa ao painel a altura renderizada: seção vazia = mostra a estrutura (skeleton) --}}
    <script>
        (function () {
            const section = document.querySelector('[data-sb-section]');
            const report = () => {
                const height = section ? section.getBoundingClientRect().height : 0;
                window.parent.postMessage({
                    source: 'sb-preview',
                    type: 'rendered',
                    height: Math.round(height),
                    empty: height < 8,
                    error: {{ $error ? 'true' : 'false' }},
                }, window.location.origin);
            };

            window.addEventListener('load', report);
            if (section && 'ResizeObserver' in window) new ResizeObserver(report).observe(section);
        })();
    </script>
</x-layouts.app>
