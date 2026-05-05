@props([
    'badge'       => null,
    'title'       => null,
    'description' => null,
])

{{--
    Template de Cabeçalho Padrão das Páginas Internas
    Fiel ao padrão visual das seções da homepage (section-services):
      - Fundo bg-background (branco)
      - Badge: text-xs font-semibold uppercase tracking-widest text-primary (font-sans)
      - Título: font-mono text-3xl md:text-4xl font-bold tracking-tight text-foreground
      - Descrição: text-lg text-muted-foreground leading-relaxed
      - Container: max-w-7xl px-4 lg:px-8 mx-auto
      - Espaçamento top: padding-top que respeita a altura do header fixo (header = ~64px → pt-24 lg:pt-28)
--}}
<section class="bg-background pt-24 pb-12 lg:pt-28 lg:pb-16">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="max-w-2xl">
            @if($badge)
                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">
                    {{ $badge }}
                </span>
            @endif

            @if($title)
                <h1 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                    {{ $title }}
                </h1>
            @endif

            @if($description)
                <p class="mt-4 text-lg leading-relaxed text-muted-foreground">
                    {{ $description }}
                </p>
            @endif
        </div>
    </div>
</section>
