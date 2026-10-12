# Padrão de UI: Construtor visual do painel (Builder)

Regras de design e implementação das telas de "construção" do painel admin: biblioteca de componentes, prévia ao vivo e inspetor. A referência implementada é a criação e edição de seções (`/admin/content-sections/create`).

Este guia vale para o **painel Filament**. A barra de título (seção 2.1) vale para **todas** as páginas do painel. Para os blocos do **site público** (Tailwind, cards, tipografia), siga o [estilo-minimalista-design.md](estilo-minimalista-design.md).

> Leitura obrigatória para quem (pessoa ou LLM) for criar ou alterar uma tela nesse formato. Se uma regra precisar mudar, atualize este documento junto com o código.

---

## 1. Quando usar este padrão

Use o construtor quando a pessoa **escolhe um tipo** de coisa e **configura o conteúdo vendo o resultado**. Exemplos: seções do site, blocos, templates, posts.

Não use para cadastros simples (cliente, produto) nem para listagens. Nesses casos, o formulário padrão do Filament basta.

---

## 2. Anatomia da tela

```
┌──────────────────────────────────────────────────────────────────────┐
│ Seções › Criar Seção (?)                                   [Excluir] │  ← barra de título (1 linha)
├───────────────┬──────────────────────────────────┬───────────────────┤
│ BIBLIOTECA    │ [Ao vivo|Estrutura] ● [▭ ▯ ▯] 72% │ (1)Comp (2)Cont (3)│
│ busca         │                                  ├───────────────────┤
│ categorias    │        prévia do componente      │ INSPETOR          │
│ ┌───────────┐ │       (iframe do site real)      │ campos da etapa   │
│ │ skeleton  │ │                                  │ (rola sozinho)    │
│ │ nome/desc │ │                                  ├───────────────────┤
│ └───────────┘ │                                  │ Cancelar  Continuar│
└───────────────┴──────────────────────────────────┴───────────────────┘
```

Cada coluna é um painel com rolagem própria. **A página em si não rola no desktop**: o construtor ocupa a altura da tela.

### 2.1 Barra de título (cabeçalho compacto) — vale para todo o painel

**Todas as páginas e subpáginas do painel** usam este cabeçalho, não só o construtor. O cabeçalho padrão do Filament (trilha, título grande e subtítulo em três linhas) foi substituído por uma linha única:

- **Esquerda:** trilha com os níveis anteriores clicáveis, separador `›` e o título da página (18px, peso 700).
  - O último nível da trilha ("Criar", "Editar", "Lista") **não aparece**, porque é a própria página.
  - Também saem os níveis que **apontam para a página atual** (ex.: o link "Alertas" na própria listagem de alertas) ou que **repetem o título**.
  - Resultado: listagens mostram só o título; subpáginas mostram `Alertas › Criar Alerta`, `Orçamentos › Quadro de orçamentos`.
- **Subtítulo:** vira a dica de um ícone `?` (`heroicon-m-question-mark-circle`) ao lado do título, com o texto também disponível para leitores de tela. Nunca vira uma linha de texto.
- **Direita:** as ações de cabeçalho da página (`getHeaderActions()`), mais os render hooks `PAGE_HEADER_ACTIONS_BEFORE/AFTER`.
- **Altura e espaçamento:** mínimo de 36px. As margens negativas (`margin: -20px 0 -18px`) compensam o `py-8 gap-y-8` da página do Filament.
- **Celular (< 640px):** a trilha some, ficam o título e o `?`; as ações quebram para a linha de baixo.

**Como funciona:** o componente do Filament é sobrescrito em `resources/views/vendor/filament-panels/components/header/index.blade.php`, e os estilos (`.cx-pagebar-*`) ficam em `filament/admin/theme.blade.php`. Toda página que usa `<x-filament-panels::page>` recebe o cabeçalho automaticamente; **não é preciso fazer nada** em páginas novas.

Regras para páginas novas:
- Defina título, subtítulo e ações pelos métodos do Filament (`getHeading()`, `getSubheading()`, `getHeaderActions()`). Não monte cabeçalho próprio dentro da view.
- Escreva o subtítulo como uma frase de ajuda, porque ele aparece como dica.
- Exceção: telas de edição em tela cheia com barra própria (ex.: editor de páginas, `filament.page-builder.editor`) devolvem `''` em `getHeading()` para ocultar este cabeçalho.
- Ao atualizar o Filament, compare o componente sobrescrito com o original (`vendor/filament/filament/resources/views/components/header/index.blade.php`).

As telas de construtor também usam `getMaxContentWidth(): MaxWidth::Full`.

### 2.2 Colunas e responsividade

O layout usa **container queries** (`container: sb`), não media queries: quem manda é a largura disponível ao lado do menu lateral, não a da janela.

| Largura do container | Layout |
|---|---|
| < 760px | Uma coluna: prévia (420px) → biblioteca (máx. 460px, rola) → inspetor |
| 760–1019px | Prévia em largura total no topo; biblioteca (300px) e inspetor lado a lado embaixo |
| ≥ 1020px | Três colunas `272px · 1fr · 368px`, altura `max(620px, 100dvh − 10.25rem)` |
| ≥ 1280px | `296px · 1fr · 400px` |
| ≥ 1560px | `320px · 1fr · 440px`; as etapas mostram também a descrição |

Regras:
- **A prévia é sempre a coluna flexível** (`minmax(0, 1fr)`). Biblioteca e inspetor têm largura fixa.
- **Um container não consulta a si mesmo.** A classe `container` vai num elemento pai (`.sb-host`), nunca no próprio grid.

---

## 3. Fundamentos visuais

### 3.1 Tokens

Toda tela do construtor fica dentro de `.cx-ui` e usa os tokens de `filament/admin/ui.blade.php`. **Não use cores fixas** para superfícies, bordas ou texto: o tema claro e o escuro vêm desses tokens.

| Token | Uso |
|---|---|
| `--cx-surface` / `--cx-surface-2` | Fundo dos painéis e áreas secundárias (rodapé do inspetor, ícones) |
| `--cx-border` / `--cx-border-strong` | Borda padrão / borda de hover, inputs e itens tracejados |
| `--cx-text` / `--cx-muted` / `--cx-subtle` | Texto principal / secundário / terciário |
| `--cx-hover` / `--cx-track` | Fundo de hover / fundo de contadores, chips e segmentos |
| `--cx-radius` (14px) / `--cx-shadow` | Raio dos painéis / sombra base |
| `rgb(var(--primary-600))` e `rgba(var(--primary-500), .1)` | Destaque (seleção, etapa ativa) e fundo suave de destaque |

### 3.2 Hierarquia e medidas

- **Raios:** painéis 14px · cards 12px · miniaturas e inputs 8–10px · chips e contadores 999px.
- **Tipografia** (fonte do painel, Jost):
  - títulos de painel: 15px / 600;
  - nome do card: 13.5px / 600;
  - descrições: 12–12.5px na cor `--cx-muted`;
  - rótulos de etapa: 12px / 600;
  - contadores: 10.5–11px / 600.
- **Ícones:** heroicons no painel (`heroicon-o-*` em cards e títulos, `heroicon-m-*` em botões e prefixos). Lucide é só para o site público.
- **Prefixos de CSS:** um por componente, sem colisão.
  - `sb-`: construtor (layout, prévia, inspetor);
  - `stp-`: biblioteca;
  - `sk-`: primitivas de skeleton;
  - `cs-`: listagem de seções (cards com o skeleton de cada tipo);
  - para uma tela nova, crie um prefixo próprio.
- **CSS puro em partials Blade** com `@once` (sem depender do build do Tailwind), como nas outras telas do painel.

### 3.3 Estados

| Estado | Como mostrar |
|---|---|
| Hover em card | Borda `--cx-border-strong` + fundo `--cx-hover`; o skeleton escurece um pouco |
| Selecionado | Borda em cor primária + anel de 1px + sombra azulada + selo de check (22px) no canto superior direito; ícone do card em azul |
| Foco (teclado) | Anel de 3px `rgba(primary-500, .25)`; nunca remova o foco sem substituto |
| Bloqueado (edição) | Mesmo card selecionado, selo com cadeado e texto explicando por quê |
| Vazio de busca | Caixa tracejada com ícone, frase curta e botão "Limpar busca" |
| Sem seleção | Prévia com estado vazio ("Escolha um componente") e inspetor com card tracejado |

Respeite `prefers-reduced-motion`: desligue transições e animações.

---

## 4. Biblioteca de componentes (coluna esquerda)

Implementação: `SectionTypePicker` (campo Filament) + `section-type-picker.blade.php`.

- **Cabeçalho fixo** ao rolar a coluna (`position: sticky`). Contém:
  - título "Biblioteca de componentes" e uma frase curta;
  - busca;
  - categorias em chips com contador.
- **Busca:**
  - ignora acentos ("orcamento" encontra "Orçamento");
  - procura em nome, descrição, palavras-chave e identificador;
  - os contadores das categorias acompanham a busca.
- **Categorias:** "Todas" + categorias definidas no modelo (`typeCategories()`).
  - Um tipo pode estar em mais de uma categoria.
  - Categoria sem resultado fica com 45% de opacidade, mas continua clicável.
- **Card:** miniatura skeleton (132px) → ícone em quadrado de 32px + nome + selo "N em uso" (quando houver) + descrição de uma linha.
- **Interação:**
  - clique seleciona;
  - **duplo clique seleciona e avança** para a próxima etapa;
  - setas ↑↓ navegam pelos cards e Enter/Espaço seleciona;
  - semântica `role="radiogroup"` / `role="radio"` com `aria-checked`.
- **Metadados vêm do modelo, não da view.** `ContentSection::typeCatalog()` guarda label, descrição, ícone, categorias e palavras-chave de cada tipo.

### 4.1 Miniaturas skeleton

Todas as miniaturas seguem **o mesmo padrão: esqueleto cinza**. Não use conteúdo real, fotos nem cores próprias.

- **Cada tipo tem uma estrutura própria**, fiel ao layout real (FAQ = acordeão com o primeiro item aberto; Timeline = círculos numerados ligados por uma linha tracejada). **Não repita skeletons genéricos.**
- **A cor primária aparece só em pontos de ação:**
  - botões (`.sk-btn.is-accent`);
  - item ativo;
  - ícones de card (`rgba(primary-500, .35–.55)`).
- **Use as primitivas** (`styles.blade.php` da biblioteca):

| Classe | Representa |
|---|---|
| `.sk-title` / `.sk-line` | Título (8px, mais forte) / linha de texto (5px). Largura via `--w` |
| `.sk-btn` (`.is-accent`) | Botão (destaque = primário) |
| `.sk-input` | Campo de formulário (só contorno) |
| `.sk-img` | Imagem (incluir `previews/_img`) |
| `.sk-box` | Card/caixa |
| `.sk-dot` | Avatar, marcador, seta de carrossel (tamanho via `--s`) |
| `.sk-row` / `.sk-col` / `.sk-grow` / `.sk-center` | Layout (espaçamento via `--g`) |

- **Arquivo:** `section-type-picker/previews/{tipo}.blade.php`. O conteúdo fica dentro de `.stp-pv` e é desenhado para uma área de ~300×158px. O mesmo arquivo é ampliado no modo **Estrutura** da prévia.
- **Fora da biblioteca**, envolva o skeleton em `.stp-tokens` para ter as variáveis de cor.

---

## 5. Prévia ao vivo (coluna central)

Implementação: `canvas.blade.php` + `script.blade.php` (Alpine `sectionBuilderPreview`) + `ContentSectionPreviewController`.

### 5.1 Barra de ferramentas

Da esquerda para a direita:

1. **Modo:** `Ao vivo | Estrutura` (segmentado).
2. **Status:** `Preparando… / Atualizando… / Atualizado / Erro`.
   - Não use "Ao vivo" no status, porque esse já é o nome do modo.
3. **Dispositivo:** desktop · tablet · celular, só com ícones e tooltip.
4. **Zoom:** `− 72% +`. Clicar no valor volta para "ajustar à tela".
5. **Ações:** contexto do site (cabeçalho e rodapé do site) · atualizar · abrir em nova aba · expandir (Esc fecha).

Com menos de 620px de largura, a barra mostra só ícones: some o texto dos segmentos e do status e somem os botões ±.

### 5.2 Regras da prévia

- **Renderize o componente real do site**, nunca uma imitação. A prévia usa o mesmo `x-render-block` do site, com o estado **não salvo** do formulário.
- **Dispositivos com proporção real:** 1440×900, 834×1112 e 390×844, ajustados pela largura **e** pela altura. Assim, seções em `vh` aparecem como numa tela de verdade. O conteúdo maior que a tela rola dentro do iframe.
- **Atualização:**
  - qualquer commit do Livewire do formulário (campo `live`, upload, repeater) atualiza a prévia após 200ms;
  - digitação no inspetor atualiza após 650ms;
  - as próprias chamadas de prévia não disparam nova atualização.
- **Troca sem piscar:** dois iframes, carrega no oculto e troca, mantendo o scroll.
- **Sem conteúdo ou com erro:** o iframe avisa a altura renderizada. Se a seção vier vazia, a prévia mostra o skeleton ampliado com um aviso:
  - "A seção ainda não tem conteúdo…", ou
  - "Complete os campos obrigatórios…".

  Nunca mostre página em branco nem erro de servidor.
- **Preferências** (modo, dispositivo, contexto do site) ficam no `localStorage`.

### 5.3 Segurança e robustez (obrigatório)

- **Snapshot:** fica em cache por 60 min, com token HMAC (sessão + usuário + página). A rota fica fora do Filament e por isso:
  - confere `canAccessArea(Website)`;
  - só aceita o **mesmo usuário** que gerou o snapshot (404 para os outros).
- **Cabeçalhos:** `X-Frame-Options: SAMEORIGIN`, `Cache-Control: no-store` e `X-Robots-Tag: noindex`.
- **Renderização:** renderize o bloco **no controller, antes do layout**, dentro de `try/catch`. Se falhar, chame `app('view')->flushState()`.
  - Renderizar dentro do layout faz uma exceção corromper a pilha de componentes do Blade e derrubar a página toda.
- **Estado do formulário:** gere com `dehydrateState()`, sem validar e sem salvar uploads. Converta uploads temporários em URL com `NormalizesPreviewState`.
- **`wire:ignore`:** o canvas usa `wire:ignore`, e o tipo selecionado chega por `$wire.$entangle(...)`.

---

## 6. Inspetor (coluna direita)

Implementação: `Wizard` do Filament com a view `filament.section-builder.wizard`.

### 6.1 Etapas

- **Stepper compacto no topo:** número em círculo (24px) + rótulo curto.
  - Etapa concluída: troca o número por ✓.
  - Etapa ativa: círculo preenchido em cor primária e fundo suave.
- **Rótulos curtos, de uma palavra** ("Componente", "Conteúdo", "Aparência"). A descrição vai no `title` e só aparece em telas ≥ 1560px.
- **Corpo:** rola sozinho e volta ao topo ao trocar de etapa.
- **Primeira etapa:** abre com um **card-resumo** do item escolhido: ícone, nome, descrição, identificador em `code`, categorias e "fixo" quando bloqueado.

### 6.2 Validação e navegação

| | Criação | Edição |
|---|---|---|
| Avançar | Valida a etapa atual antes (`skippable` = falso) | Navegação livre pelo stepper (`skippable` = verdadeiro) |
| Campos fora das etapas (ex.: tipo na biblioteca) | Conferidos em `afterValidation()` da etapa | — |

> Atenção: um wizard `skippable()` **não valida nada** ao avançar. Só use isso na edição.

### 6.3 Rodapé de ações

- **Uma única ação primária visível por vez.**
- **Criação:**
  - à esquerda, `Cancelar` na 1ª etapa e `← Voltar` nas outras;
  - à direita, **"Continuar para {próxima etapa}"**;
  - na última etapa, `Criar seção` + `Criar e adicionar outra` (cinza).
- **Edição:**
  - à direita, `Próxima →` (cinza) + **`Salvar alterações`** (primário), disponível em todas as etapas.
- As ações do formulário abaixo da página ficam vazias (`getFormActions(): []`). Tudo vive no rodapé do inspetor.
- As ações da página são passadas ao wizard **de forma preguiçosa** (`lazyHtml`), porque o formulário é montado antes do Livewire terminar de iniciar.

---

## 7. Regras para campos do inspetor

O inspetor é estreito (368–440px). Os formulários precisam ser pensados para isso.

1. **Todo campo tem `helperText`.** Uma frase curta e útil: o que é, um exemplo ou o efeito. Não repita o rótulo.
2. **Todo campo que aceita prefixo tem `prefixIcon`** (`TextInput`, `Select`, `TagsInput`, `ColorPicker`, `DateTimePicker`), com `heroicon-m-*`:

| Conteúdo do campo | Ícone |
|---|---|
| Título | `h1` ou `pencil` |
| Subtítulo / texto curto | `bars-3-bottom-left` |
| Pré-título / selo | `bookmark` / `tag` |
| URL / link | `link` |
| Texto de botão | `cursor-arrow-rays` |
| Nome de ícone Lucide | `sparkles` |
| Pessoa / cargo | `user` / `briefcase` |
| E-mail | `envelope` |
| Cidade / local | `map-pin` |
| Ordem | `bars-arrow-down` |
| Opacidade | `eye` / `adjustments-horizontal` |
| Slug / identificador | `hashtag` |

   O teste `todos os campos do formulário têm helper text e, quando suportado, prefix icon` garante as regras 1 e 2.
3. **Agrupe com `Section` compacta e recolhível** (`->compact()->collapsible()`), com ícone e uma frase de descrição.
   - Grupos secundários (slideshow, estatísticas) começam recolhidos (`->collapsed()`).
   - Prefira seções a abas dentro de uma etapa. Abas só quando forem 2 ou 3 e curtas (ex.: Estilo | Publicação).
4. **Uma coluna por padrão.**
   - Duas colunas só para pares curtos (valor + rótulo, texto + link).
   - **Nunca coloque `ToggleButtons` agrupados lado a lado com outro campo**: eles estouram a largura.
5. **Repeaters:**
   - `collapsible()`;
   - `itemLabel()` com o valor mais identificável;
   - `addActionLabel()` com verbo ("Adicionar pergunta");
   - `helperText` explicando a ordem ou o limite.
6. **Uploads de imagem:** `imagePreviewHeight()` entre 80 e 140, e o tamanho recomendado no `helperText` (ex.: "1920 × 1080 px").
7. **Textos longos:** `Textarea` com `rows(2)->autosize()`.
8. **Textos de interface em português**, com sentença simples ("Exibir cabeçalho", não "Exibir Cabeçalho").
9. **Não mude caminhos de estado** (`content.*`) ao reorganizar campos: o site lê exatamente essas chaves.

---

## 8. Checklist para uma nova tela no padrão

- [ ] Página com `MaxWidth::Full` e `getFormActions(): []` (o cabeçalho compacto já vem do painel).
- [ ] Layout com três filhos: seletor (biblioteca), `View` do canvas e `Wizard` com a view do inspetor.
- [ ] Metadados dos tipos no modelo (`*Catalog()`), com uma miniatura skeleton própria por tipo.
- [ ] Endpoint de prévia com token, checagem de área e de usuário, cabeçalhos de segurança e renderização antes do layout.
- [ ] Prévia trata vazio e erro mostrando o skeleton com aviso.
- [ ] Etapas validadas na criação e livres na edição; uma ação primária por vez.
- [ ] Todos os campos com `helperText`; `prefixIcon` onde suportado.
- [ ] Testado em tema claro e escuro, nas três faixas de largura e só com teclado.
- [ ] Testes: renderização da tela, avanço bloqueado sem seleção, prévia com estado não salvo, prévia de cada tipo sem conteúdo, convenção de campos.

## 9. Mapa de arquivos (implementação de referência)

| Parte | Arquivo |
|---|---|
| Layout | `app/Filament/Forms/Components/SectionBuilder.php`, `resources/views/filament/section-builder/layout.blade.php`, `styles.blade.php` |
| Barra de título (global) | `resources/views/vendor/filament-panels/components/header/index.blade.php`, estilos em `resources/views/filament/admin/theme.blade.php`, teste em `tests/Feature/PageHeaderTest.php` |
| Biblioteca | `app/Filament/Forms/Components/SectionTypePicker.php`, `resources/views/filament/forms/components/section-type-picker.blade.php` (+ `/styles`, `/previews/*`) |
| Prévia | `resources/views/filament/section-builder/canvas.blade.php`, `script.blade.php`, `app/Http/Controllers/ContentSectionPreviewController.php`, `resources/views/section-preview.blade.php` |
| Inspetor | `resources/views/filament/section-builder/wizard.blade.php`, `component-summary.blade.php` |
| Integração com a página | `app/Filament/Resources/ContentSectionResource/Concerns/InteractsWithSectionBuilder.php` |
| Formulário por tipo | `app/Filament/Resources/ContentSectionResource.php` (`get*Section()`) |
| Catálogo de tipos | `app/Models/ContentSection.php` (`typeCatalog()`, `typeCategories()`) |
| Listagem (cards, filtros, uso nas páginas, arrastar para ordenar) | `app/Filament/Resources/ContentSectionResource/Pages/ListContentSections.php`, `resources/views/filament/content-sections/` (reaproveita os skeletons de `section-type-picker/previews`), uso em `ContentSection::usageMap()` |
| Testes | `tests/Feature/ContentSectionTypePickerTest.php`, `tests/Feature/ContentSectionListTest.php` |
