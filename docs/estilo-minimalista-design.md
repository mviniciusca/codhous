# Estilo Minimalista de Design (Guia de Referência)

Este documento serve como referência de design para a criação de componentes e layouts dentro da aplicação, seguindo a estética minimalista e clean aprovada para o projeto.

## Princípios Gerais

1.  **Uso Eficiente do Espaço Em Branco (White Space):** Elementos devem respirar. Margens internas (paddings) generosas como `p-8` a `p-10` são encorajadas para blocos principais e cartões.
2.  **Cores e Contraste Discreto:** Uso predominante de fundos sólidos claros (como branco ou `bg-card`) com variações e opacidades da cor **primária** para dar destaque, evitando cores berrantes ou excesso de gradientes pesados. Textos de apoio devem usar tons mais claros (`text-muted-foreground`).
3.  **Sombras e Bordas Suaves:** As bordas e sombras servem apenas para dar volume, sem pesar.
    *   Bordas super leves: `border border-border/30` ou `border-gray-100`.
    *   Sombras suaves de base: `shadow-sm` ou `shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]`.
    *   Sombra de hover (Hover states): Aumentar a sombra suavemente em interações, `hover:shadow-md`.
4.  **Arredondamento Moderno:** Cantos mais suaves e modernos, utilizando `rounded-2xl` ou `rounded-[24px]` para containers principais e `rounded-xl` para elementos internos menores, como ícones.

## Estrutura de Cartões (Cards)

Um cartão padrão de conteúdo (ex: Serviço, Produto, Diferencial) deve ser estruturado na seguinte hierarquia:

*   **Cabeçalho do Card:**
    *   Ícone em destaque alinhado à esquerda dentro de um quadrado suavizado com a cor primária translúcida (ex: `bg-primary/10 text-primary`).
    *   *(Opcional)* Numeração minimalista ou índice alinhado à direita no topo (ex: `01`, `02`), usando fonte `font-mono`, pequena e bem apagada (`text-muted-foreground/30`).
*   **Hierarquia de Títulos:**
    *   **Subtítulo (Kicker):** Texto em letras maiúsculas, bem pequeno (`text-[10px]`), com tracking (espaçamento entre letras) espaçado (`tracking-[0.2em]`) e peso em negrito (`font-bold`).
    *   **Título Principal:** Fonte com forte legibilidade (`text-xl font-bold text-foreground`), geralmente abaixo do subtítulo.
*   **Corpo e Textos de Apoio:** 
    *   Parágrafos com espaçamento de linha agradável (`leading-relaxed`) e cor suavizada (`text-muted-foreground`).
*   **Listas (Bullets):**
    *   Pequenos ícones de *check* devem possuir um fundo circular discreto, utilizando a mesma paleta da cor primária (`bg-primary/10 text-primary`). A fonte da lista acompanha a cor do parágrafo, podendo ser ligeiramente menor (`text-sm`).
*   **Rodapé (Call to Action / Link):**
    *   O link de rodapé (como "Solicitar Orçamento") deve se distanciar do conteúdo com espaçamento e uma linha separadora muito discreta (ex: `border-t border-border/30`).
    *   O estilo do botão/link acompanha um ícone, geralmente uma seta (`arrow-right`), com interação visual ao passar o mouse (`group-hover:translate-x-1`).

## Exemplo Prático (Tailwind CSS)

```html
<div class="flex flex-col rounded-[24px] bg-card border border-border/40 p-8 shadow-sm hover:shadow-md transition-all">
    <div class="flex items-start justify-between mb-8">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
            <i data-lucide="icon-name" class="h-6 w-6"></i>
        </div>
        <span class="font-mono text-sm font-medium text-muted-foreground/30">01</span>
    </div>
    
    <div class="mb-2 text-[10px] font-bold tracking-[0.2em] uppercase text-muted-foreground">SUBTÍTULO</div>
    <h3 class="mb-3 text-xl font-bold text-foreground">Título Principal</h3>
    <p class="mb-6 text-sm leading-relaxed text-muted-foreground">Descrição suave...</p>
    
    <div class="mt-auto pt-6 border-t border-border/30">
        <a href="#" class="group flex items-center justify-between text-sm font-bold text-primary">
            Ação
            <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
        </a>
    </div>
</div>
```

*Este documento pode e deve ser consultado pelo LLM para garantir que futuros blocos e componentes mantenham o mesmo padrão e nível estético.*
