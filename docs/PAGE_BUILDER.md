# Page Builder do Codhous: como funciona por dentro

> **Para quem é este documento:** um LLM (ou dev) que vai manter, estender ou recriar o Page Builder do Codhous. Ele explica a arquitetura, as bibliotecas de fora e o raciocínio por trás de cada escolha. Leia do começo ao fim antes de mexer no código. Quando o documento e o código discordarem, **o código vale**; atualize este arquivo junto com a mudança.
>
> Documentos relacionados: [PADRAO_UI_BUILDER.md](PADRAO_UI_BUILDER.md) (regras visuais das telas de construtor do painel) e [estilo-minimalista-design.md](estilo-minimalista-design.md) (visual dos blocos do site público).

---

## 0. Resumo em 30 segundos

- Uma **página** do site é um registro `pages` com uma coluna JSON `content`: uma lista de blocos `{type, data}`.
- No painel (`/admin/pages/{id}/edit`), o campo `content` é um **`Builder` do Filament 3** cuja view foi trocada por um layout próprio em **3 colunas**: biblioteca de blocos, **live preview** (iframe) e estrutura (lista de blocos arrastável).
- O live preview **não é uma imitação**: é o site real (`page.blade.php`) renderizado a partir de um **snapshot do formulário ainda não salvo**, guardado em cache e servido por uma rota protegida.
- Painel e iframe conversam por **`window.postMessage`**: passar o mouse num bloco destaca o outro lado, clicar no preview abre o editor daquele bloco.
- A edição de um bloco abre num **slide-over** (painel lateral) que não bloqueia o preview. Cada tecla digitada gera um novo snapshot e o iframe recarrega **sem piscar** (double-buffer com dois iframes).
- Há **rascunho com auto save**: o estado vai para `pages.draft` sem tocar no que está publicado. "Publicar" é o save normal do Filament.
- O front do site renderiza cada bloco através de **um único `@switch`** em `render-block.blade.php`.

---

## 1. Glossário das tecnologias de fora

Se você não conhece alguma destas peças, leia esta seção primeiro. O resto do documento assume que você sabe o que cada uma faz.

### 1.1 Laravel 11
Framework PHP. O que importa aqui:
- **Blade**: motor de templates (`.blade.php`). `@foreach`, `@switch`, `@php`, `{{ }}` (escapa HTML) e `{!! !!}` (não escapa).
- **Componentes Blade anônimos**: um arquivo em `resources/views/components/ui/section.blade.php` vira a tag `<x-ui.section>`. `@props([...])` declara os atributos.
- **`Blade::render($string, $vars)`**: renderiza uma string Blade em tempo de execução. Usamos isso para poder envolver cada bloco num `try/catch` (ver seção 5.3).
- **Cache** (`Cache::put/get`): guarda o snapshot do preview por 60 minutos.
- **`hash_hmac`**: gera o token do preview, assinado com `APP_KEY` (ver seção 5.1).
- **Eloquent casts**: `'content' => 'array'` faz o JSON virar array PHP automaticamente.

### 1.2 Livewire 3
Framework que deixa componentes PHP "vivos" no navegador. Cada página do Filament é um componente Livewire.
- O estado (`$this->data`, o formulário) mora no servidor. O navegador manda **requisições ("commits")** quando algo muda e recebe HTML novo, que é aplicado com *morphing* do DOM.
- **`$wire`**: objeto JS que representa o componente. `$wire.data.title` lê uma propriedade; `await $wire.generateLivePreviewUrl()` **chama um método PHP público** e recebe o retorno. Isso é o coração do live preview.
- **`Livewire.hook('commit', ({component, commit, succeed}) => …)`**: gancho global que roda em toda requisição. Usamos para saber quando o servidor terminou de processar algo (adicionar bloco, reordenar, aplicar edição) e então atualizar o preview.
- **`#[Renderless]`**: atributo PHP num método. Diz ao Livewire para **não re-renderizar** o componente depois da chamada. Usado em `generateLivePreviewUrl()` porque ele só devolve uma URL; re-renderizar o formulário a cada tecla seria lento e faria o campo perder o foco.
- **`$this->skipRender()`**: mesmo efeito, mas decidido em tempo de execução (usado no auto save).
- **`#[Locked]`**: propriedade que o navegador não pode alterar (usado em `persistedStateHash`).
- **`wire:ignore`**: o Livewire não mexe no DOM deste elemento ao re-renderizar. Os iframes ficam dentro de um `wire:ignore`, senão cada requisição recarregaria o preview do zero.
- **`wire:key`**: identidade estável de um elemento em listas, para o morphing não confundir itens.
- **`TemporaryUploadedFile`**: quando o usuário escolhe uma imagem num `FileUpload`, antes de salvar ela vive como arquivo temporário. Ele tem `temporaryUrl()`, que usamos para mostrar a imagem no preview antes do save.

### 1.3 Alpine.js
Mini framework JS reativo que vem junto com Livewire.
- `x-data="pageBuilderLivePreview(config)"`: cria um componente cujo estado é o objeto retornado pela função.
- `x-show`, `x-bind:class`, `x-on:click`, `x-text`, `x-model`, `x-ref` (referência a elemento via `this.$refs.nome`), `x-cloak`.
- `init()` roda ao montar; `destroy()` ao desmontar. Getters (`get status()`) viram propriedades computadas.
- `this.$wire` dentro de um componente Alpine aponta para o componente Livewire que o contém.
- `x-on:evento.window`: escuta eventos disparados no `window`. Livewire `$this->dispatch('nome', ...)` no PHP vira um `CustomEvent` no `window`, então `x-on:page-builder-block-added.window` recebe o que o PHP despachou.

### 1.4 Filament 3
Framework de painel admin em cima de Livewire. Peças usadas:
- **Resource** (`PageResource`): junta formulário, tabela e páginas (List/Create/Edit) de um model.
- **Form Builder** (`Forms\Components\…`): `TextInput`, `Textarea`, `Repeater`, `FileUpload`, `ToggleButtons`, `Tabs`, `Grid`, `Group`, `Section`, `RichEditor`…
- **`Forms\Components\Builder`**: é o campo que guarda uma lista de blocos tipados. Cada tipo é um `Builder\Block::make('nome')->schema([...])`. O estado é um array **indexado por UUID**:
  ```php
  [
      '9f1c…' => ['type' => 'faq', 'data' => ['title' => '…', 'items' => [...]]],
      'a77e…' => ['type' => 'stats', 'data' => [...]],
  ]
  ```
  Ao salvar no banco, o Filament remove os UUIDs (`array_values`) e grava `[{type, data}, …]`. **No live preview nós mantemos os UUIDs** de propósito (ver 5.1).
- **Actions de componente de formulário**: o Builder tem ações internas `add`, `edit`, `clone`, `delete`, `moveUp`, `moveDown`, `reorder`. No JS, uma ação é aberta com `$wire.mountFormComponentAction(statePath, nomeDaAção, argumentos)`. Enquanto a ação está aberta, o Livewire guarda nas propriedades `mountedFormComponentActions`, `mountedFormComponentActionsComponents`, `mountedFormComponentActionsArguments` e `mountedFormComponentActionsData` o que está aberto e o que está sendo digitado. **O preview lê exatamente essas propriedades** para mostrar o bloco que ainda não foi "Aplicado".
- **`->view('…')`** num componente: troca a view Blade que renderiza aquele componente. Foi assim que o Builder ganhou o layout de 3 colunas sem reescrever a lógica dele.
- **`->editAction(fn ($action) => $action->slideOver()…)`** e **`->addAction(…)`**: personalizam as ações internas do Builder.
- **`->registerActions([...])`**: registra ações extras dentro do componente (usado para "Templates").
- **`Notification::make()`**: toasts. No JS existe `new window.FilamentNotification()`.
- **`dehydrateState()`**: transforma o estado bruto do formulário no que seria salvo (aplica `dehydrateStateUsing`, formata campos…). Chamamos isso manualmente, **sem validar**, para gerar o snapshot.

### 1.5 SortableJS (via Filament)
O Filament já embute o SortableJS e expõe as diretivas Alpine `x-sortable`, `x-sortable-item="{uuid}"` e `x-sortable-handle`. Ao soltar um item, o evento `end` dispara; nós chamamos a ação `reorder` do Builder com `$event.target.sortable.toArray()` (lista de UUIDs na nova ordem). Não instalamos nada: está em `vendor/filament/support/resources/js/sortable.js`.

### 1.6 `window.postMessage` e iframes
- Um `<iframe>` é outra janela. O pai fala com ele por `iframe.contentWindow.postMessage(msg, origem)` e o iframe responde com `window.parent.postMessage(msg, origem)`.
- **Sempre** confira `event.origin === window.location.origin` e um campo de assinatura (`source: 'pb-host'` / `'pb-preview'`). Sem isso, qualquer página poderia mandar comandos.
- O iframe é **mesma origem** (mesmo domínio do painel), por isso o pai também consegue ler `contentWindow.scrollY` diretamente.
- `X-Frame-Options: SAMEORIGIN` na resposta impede que outro site embuta o preview.

### 1.7 Tailwind CSS 3, Lucide e o tema do site
- Os blocos do site usam **Tailwind** com tokens próprios (`bg-background`, `text-foreground`, `bg-primary`, `bg-muted/30`…) definidos por CSS variables geradas em `App\Support\SiteTheme::css()`.
- O Tailwind só gera classes que encontra escritas por extenso nos arquivos listados em `content` do `tailwind.config.js`. Por isso `App\Support\SectionStyle` tem as classes de espaçamento escritas literalmente (`'pt-14 md:pt-20'`) em vez de montadas por concatenação.
- **Lucide**: ícones SVG. No HTML o bloco escreve `<i data-lucide="truck"></i>` e `lucide.createIcons()` (em `resources/js/site.js`) troca pelo SVG. Os campos "Ícone (Lucide)" dos blocos guardam o nome do ícone (ver lucide.dev/icons). Já o painel usa **Heroicons** (`heroicon-o-…`), que são os ícones do Filament.
- **O CSS do builder no painel é CSS puro** dentro de `<style>` em views Blade (`styles.blade.php`, `editor-styles.blade.php`), **não Tailwind**. Motivo: o painel do Filament tem seu próprio build e não queremos depender de recompilar o tema do admin. Ele usa as variáveis do Filament (`--primary-600`, `--gray-500`) para seguir as cores e o modo escuro do painel.

### 1.8 filament-peek
Pacote `pboivin/filament-peek`. Fornece `PreviewAction` + `HasPreviewModal`, um preview em modal. Fica no menu "⋮" como **"Preview interno"** e abre a URL pública da página. É independente do nosso live preview; pode ser ignorado ao estudar o builder.

---

## 2. Mapa de arquivos

```
app/
├── Models/Page.php                                   # model: content, meta, draft, published_at; sincroniza o menu
├── Http/Controllers/
│   ├── PageController.php                            # site público: /{slug?} → view('page')
│   └── PageBuilderPreviewController.php              # live preview: lê o snapshot do cache e renderiza view('page')
├── Filament/
│   ├── Concerns/NormalizesPreviewState.php           # converte estado do form em dados serializáveis
│   └── Resources/
│       ├── PageResource.php                          # form (Builder + Configurações), tabela, TODOS os blocos, ação Templates
│       └── PageResource/
│           ├── PageTemplates.php                     # catálogo de templates de página
│           ├── Concerns/
│           │   ├── InteractsWithLivePreview.php      # gera o snapshot e a URL do preview
│           │   └── InteractsWithPageDrafts.php       # rascunho, auto save, "alterações pendentes"
│           └── Pages/{CreatePage,EditPage,ListPages}.php
├── Support/SectionStyle.php                          # fundo, esquema de cor e espaçamento das seções

resources/views/
├── page.blade.php                                    # página do site; no modo preview envolve cada bloco
├── components/
│   ├── render-block.blade.php                        # @switch($type) → HTML de cada bloco
│   ├── ui/section.blade.php                          # <x-ui.section>: invólucro padrão de toda seção
│   └── layouts/app.blade.php                         # layout do site (header/footer; esconde widgets no preview)
└── filament/
    ├── page-builder/
    │   ├── editor.blade.php                          # view da página Create/Edit: cabeçalho, abas, Alpine pageEditor
    │   ├── editor-styles.blade.php                   # CSS do cabeçalho/abas
    │   ├── styles.blade.php                          # CSS das 3 colunas, slide-over, responsivo
    │   ├── script.blade.php                          # Alpine pageBuilderLivePreview (o "cérebro" do preview)
    │   ├── preview-bridge.blade.php                  # JS+CSS injetados DENTRO do iframe
    │   └── seo-preview.blade.php                     # cartão "como aparece no Google" da aba Configurações
    ├── forms/components/
    │   ├── page-builder.blade.php                    # view do campo Builder: as 3 colunas
    │   └── page-template-picker.blade.php            # seletor visual de templates
    └── block-previews/*.blade.php                    # previews por bloco (->preview()), ver 7.3

routes/web.php                                        # rota page-builder.preview (antes do catch-all)
database/migrations/…add_draft_columns_to_pages_table # draft, draft_saved_at, published_at
tests/Feature/{PageBlocksRenderTest,PageDraftTest,PageTemplateTest}.php
```

---

## 3. Modelo de dados

Tabela `pages` (`App\Models\Page`, com `SoftDeletes`):

| Coluna | Tipo | Para quê |
|---|---|---|
| `title` | string | Nome da página (menu, aba do navegador, cabeçalho automático) |
| `slug` | string | Endereço. A home é `/` (ou `home`/`index`, ver `isHomepage()`) |
| `content` | JSON (array) | **Versão publicada**: `[{type, data}, …]` |
| `meta` | JSON | `title`, `description`, `keywords`, `og_image` (SEO) |
| `is_visible` | bool | Se o site público mostra a página |
| `is_active_in_menu` | bool | Se entra no menu do topo |
| `sort_order` | int | Ordem no menu e na listagem |
| `draft` | JSON nullable | **Rascunho**: o estado inteiro do formulário (title, slug, content com UUIDs, meta, toggles) |
| `draft_saved_at` | timestamp | Quando o rascunho foi salvo |
| `published_at` | timestamp | Última publicação. `null` = nunca publicada (aparece como "Rascunho" na lista) |

Exemplo de `content` publicado:

```json
[
  {"type": "page_header", "data": {"badge": "QUEM SOMOS", "title": "Nossa história", "show_breadcrumbs": true}},
  {"type": "stats", "data": {"items": [{"value": "15+", "label": "Anos", "icon": "calendar-check"}], "overlap": true}},
  {"type": "module_reference", "data": {"content_section_id": 3, "background_color": "bg-muted/30"}}
]
```

`Page::booted()` escuta `saved` e `deleted` para manter `settings.website.navigation` (o menu do site) em sincronia com `is_active_in_menu`. Rascunhos usam `saveQuietly()` para **não** disparar essa sincronização.

---

## 4. A tela do editor, peça por peça

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│ ← Sobre a empresa  ● Rascunho · salvo às 14:02   [Conteúdo|Config. e SEO]   [Salvar rascunho][Publicar][⋮] │  editor.blade.php
│   site.com/sobre                                                                │
├──────────────┬────────────────────────────────────────────┬────────────────────┤
│ BLOCOS       │ ● Ao vivo     [🖥 ▭ 📱]          72%  ⟳  ↗ │ ESTRUTURA  3 [✨Templates]
│ [buscar…]    │ ┌────────────────────────────────────────┐ │ ⠿ 1 Cabeçalho      │
│ ▢ Cabeçalho +│ │                                        │ │ ⠿ 2 Estatísticas   │  page-builder.blade.php
│ ▢ Serviços  +│ │      iframe com o site real            │ │ ⠿ 3 FAQ        ◎⧉🗑│
│ ▢ FAQ       +│ │      (escalado para caber)             │ │                    │
│ …            │ └────────────────────────────────────────┘ │                    │
└──────────────┴────────────────────────────────────────────┴────────────────────┘
                                         ┌───────────────────────┐
     ao clicar num bloco →               │ Editar: FAQ           │  slide-over (editAction)
                                         │ [Conteúdo|Estilo|Avançado]
                                         │ campos…               │
                                         │        [Cancelar][Aplicar]
                                         └───────────────────────┘
```

### 4.1 A página Filament (`CreatePage` / `EditPage`)

Ambas apontam para a mesma view: `protected static string $view = 'filament.page-builder.editor';`.

- `getHeading()` e `getBreadcrumbs()` devolvem vazio: o editor tem cabeçalho próprio (exceção prevista em PADRAO_UI_BUILDER.md, seção 2.1).
- `getFormActions()` devolve `[]`: os botões ficam no cabeçalho (`getHeaderActions()`): "Salvar rascunho", "Publicar" (atalho `mod+shift+s`) e o menu "⋮" (ver página publicada, Preview interno, Descartar rascunho, Excluir).
- Usam os traits `InteractsWithLivePreview` e `InteractsWithPageDrafts`.

### 4.2 O formulário (`PageResource::form`)

São **dois `Group`** lado a lado no schema, alternados por Alpine:

```php
Group::make()->extraAttributes(['x-show' => "tab === 'content'"])   // aba Conteúdo: o Builder
Group::make()->extraAttributes(['x-show' => "tab === 'settings'"])  // aba Configurações e SEO
```

Por que `x-show` e não `Tabs` do Filament? Porque as abas ficam no **cabeçalho do editor** (fora do form) e porque esconder com `x-show` mantém **os dois grupos no DOM e no estado**: tudo continua sendo validado e salvo junto. A variável `tab` vem do `x-data="pageEditor(...)"` em `editor.blade.php`, que envolve o form.

### 4.3 O campo Builder e suas personalizações

```php
Forms\Components\Builder::make('content')
    ->blocks([...])                       // todos os getXxxBlock()
    ->collapsible()->collapsed()->cloneable()
    ->blockPreviews()
    ->registerActions([self::getLoadTemplateAction()])
    ->view('filament.forms.components.page-builder')   // ← o layout de 3 colunas
    ->addAction(fn ($action) => $action
        ->form(null)->modalHidden()                     // adiciona direto, sem modal
        ->after(fn ($component, $livewire) =>
            $livewire->dispatch('page-builder-block-added', uuid: array_key_last($component->getState()))))
    ->editAction(fn ($action) => $action
        ->slideOver()                                    // painel lateral
        ->modalSubmitActionLabel('Aplicar')
        ->closeModalByClickingAway(false)
        ->extraModalWindowAttributes(['class' => 'pb-inspector']));  // classe usada no CSS/JS
```

Detalhes que parecem bobos mas são essenciais:

- **`addAction` sem form**: com `blockPreviews()`, o Filament injeta o schema do bloco como formulário da ação de adicionar e valida os campos obrigatórios antes de inserir. Como o modal está oculto, a validação falhava **em silêncio** e o bloco nunca aparecia. Removendo o form, o bloco entra com valores padrão e a validação acontece no painel de edição ou no "Publicar".
- **`->after(... dispatch('page-builder-block-added'))`**: avisa o JS do UUID do bloco novo para ele rolar o preview até lá e abrir o editor do bloco automaticamente.
- **`.pb-inspector`**: a classe no slide-over permite ao CSS deixar o fundo do modal **transparente e sem bloquear cliques** (`pointer-events: none` nos wrappers, `auto` só no painel). É isso que deixa rolar e ver o preview enquanto edita:
  ```css
  .fi-modal:has(.pb-inspector) > div { pointer-events: none; }
  .fi-modal:has(.pb-inspector) .pb-inspector { pointer-events: auto; }
  ```

### 4.4 A view das 3 colunas (`page-builder.blade.php`)

Uma view de campo do Filament recebe closures como `$getChildComponentContainers()`, `$getBlockPickerBlocks()`, `$getAction($nome)`, `$getStatePath()`. A view:

1. Pega as ações internas do Builder (`add`, `edit`, `clone`, `delete`, `moveUp`, `moveDown`, `reorder`) e a nossa `loadTemplate`.
2. Monta o `$livePreviewConfig` (`statePath`, nome das ações `add`/`edit`) e passa para o Alpine com `x-data="pageBuilderLivePreview(@js($livePreviewConfig))"`.
3. **Coluna 1 (Blocos)**: um botão por bloco registrado. Clique → `addBlock(nome)` → `$wire.mountFormComponentAction(statePath, 'add', {block: nome})`. A busca usa `x-model="paletteQuery"` e `matches()` (compara sem acento: `Str::ascii` no PHP e `normalize('NFD')` no JS).
4. **Coluna 2 (Preview)**: toolbar (status, dispositivos, zoom, atualizar, abrir em nova aba) e **dois iframes** dentro de `wire:ignore`.
5. **Coluna 3 (Estrutura)**: `<ul x-sortable>` com um `<li x-sortable-item="{uuid}">` por bloco. Cada item mostra ícone, número, label e um subtítulo extraído do próprio estado (primeiro campo preenchido entre `title`, `heading`, `badge`, `label`, `name`, `subtitle`, `description`). As ações de clone/delete/move são **as do próprio Filament** renderizadas com `{{ $itemCloneAction }}`, então confirmações, permissões e estado continuam funcionando. Clique no item → `editBlock(uuid)`; mouse em cima → `highlight(uuid)`.

---

## 5. O live preview em detalhe

Este é o fluxo principal. Entenda-o e o resto é consequência.

```
 Painel (Alpine: pageBuilderLivePreview)            Servidor (Livewire / Laravel)                 iframe (preview-bridge)
 ──────────────────────────────────────             ─────────────────────────────                 ───────────────────────
 usuário digita no slide-over
   └─ evento 'input' capturado (debounce 650ms)
 refresh()
   └─ await $wire.generateLivePreviewUrl() ───────▶ InteractsWithLivePreview
                                                     1. dehydrateState() do Builder (sem validar)
                                                     2. sobrepõe o bloco aberto no slide-over
                                                     3. normaliza (uploads → URL temporária)
                                                     4. Cache::put(prefix.token, snapshot, 60min)
                       ◀──────────── url ────────── 5. return route('page-builder.preview', token)
 load(url) no iframe OCULTO ──────── GET ─────────▶ PageBuilderPreviewController
                                                     - confere permissão e dono do snapshot
                                                     - new Page([...]) em memória (não salva)
                                                     - view('page', builderPreview: true, blockKeys, blockLabels)
                       ◀──────────── HTML ───────── page.blade.php envolve cada bloco em
                                                     <div class="pb-block" data-pb-block="uuid">
 onload: restaura scroll, troca iframe visível                                                    bridge: injeta etiquetas,
   └─ post({type:'select', uuid}) ─────────────────────────────── postMessage ───────────────▶  marca bloco selecionado
                                                                                                 usuário clica num bloco
 onFrameMessage: editBlock(uuid) ◀─────────────────────────────── postMessage ───────────────── post({type:'select', uuid})
```

### 5.1 Gerando o snapshot (`InteractsWithLivePreview`)

`generateLivePreviewUrl()` é um método **público** do componente Livewire (logo, chamável do JS) marcado com `#[Renderless]`.

1. **Acha o Builder** no form: `$this->form->getComponent(fn ($c) => $c instanceof Builder && $c->getName() === 'content', withHidden: true)`.
2. **Dehidrata sem validar**: `$builder->dehydrateState($state)`. Pulamos de propósito o `mutateDehydratedState` do Builder, que é quem faz `array_values()`. Assim **as chaves continuam sendo os UUIDs**, e o preview consegue marcar cada bloco com `data-pb-block="uuid"`, o mesmo UUID da lista de Estrutura. Sem isso não haveria sincronia hover/seleção.
3. **Sobrepõe o bloco em edição** (`getLivePreviewEditingOverride`): enquanto o slide-over está aberto, o que o usuário digita **não está** em `$this->data['content']`; está em `mountedFormComponentActionsData[$index]`. O método percorre `mountedFormComponentActions`, acha a ação `edit` do nosso Builder, pega o UUID em `mountedFormComponentActionsArguments[$index]['item']`, dehidrata o form da ação (`getMountedFormComponentActionForm($index)`) e devolve `[uuid, data]`. Esses dados substituem o `data` do bloco. **É isso que faz o preview mudar antes de clicar em "Aplicar".**
4. **Normaliza** com `NormalizesPreviewState::normalizePreviewValue()`: `TemporaryUploadedFile` vira `temporaryUrl()`, enums viram valor, datas viram string, objetos desconhecidos viram `null`. Tudo precisa ser serializável para ir ao cache.
5. **Guarda no cache** sob `page-builder-preview:{token}` por 60 minutos, junto com `user_id`, título, slug, meta e um mapa `uuid → label do bloco` (para as etiquetas no preview).
6. Devolve `route('page-builder.preview', ['token' => $token])`.

**O token** é `hash_hmac('sha256', session_id | user_id | escopo, APP_KEY)`, onde o escopo é `Classe:idDoRegistro` (ou `:new`). Consequências:
- É **determinístico** por sessão + usuário + página: cada refresh sobrescreve a mesma chave do cache em vez de criar lixo.
- Não dá para adivinhar sem a `APP_KEY` e o id da sessão.
- Duas abas editando páginas diferentes não se misturam.

### 5.2 A rota e o controller

```php
// routes/web.php, ACIMA do catch-all /{slug?}
Route::get('/admin/page-builder/preview/{token}', PageBuilderPreviewController::class)
    ->middleware('auth')
    ->where('token', '[a-f0-9]{64}')
    ->name('page-builder.preview');
```

`PageBuilderPreviewController::__invoke`:
1. Lê o snapshot do cache.
2. `abort_unless($user->canAccessArea(AccessArea::Website), 403)`: a rota está fora do Filament, então a permissão de área é conferida à mão.
3. `abort_unless(hash_equals($payload['user_id'], $user->id), 404)`: só quem gerou vê.
4. Monta um `new Page([...])` **em memória** (nunca salvo) com `content = array_values($blocks)`.
5. Renderiza a **mesma view do site** (`page`) com `builderPreview: true`, `blockKeys` (UUIDs na ordem) e `blockLabels`.
6. Cabeçalhos: `Cache-Control: no-store, private`, `X-Robots-Tag: noindex, nofollow`, `X-Frame-Options: SAMEORIGIN`.

### 5.3 A página no modo preview (`page.blade.php`)

No modo normal, cada bloco vira `<x-render-block :type :data … />`. No modo `builderPreview`:

```blade
<div class="pb-block" data-pb-block="{{ $pbUuid }}" data-pb-label="{{ $blockLabels[$pbUuid] }}">
    @php
        try {
            $pbHtml = Blade::render('<x-render-block … />', [...]);
            $pbError = null;
        } catch (\Throwable $e) {
            $pbHtml = ''; $pbError = $e->getMessage();
        }
    @endphp
    @if($pbError) <div class="pb-block-error">Complete os campos do bloco…</div>
    @else {!! $pbHtml !!} @endif
</div>
```

Por que `Blade::render` dentro de `try/catch`? Um bloco recém-adicionado tem campos vazios e pode lançar exceção (índice inexistente, `null` onde se espera array). Sem o `try`, **a página inteira** do preview quebraria. Com ele, só aquele bloco mostra uma caixa amarela "Complete os campos do bloco". Não dá para fazer `try/catch` em volta de um componente Blade comum; por isso renderizamos para string.

Também no modo preview:
- Página vazia mostra o placeholder `.pb-empty`.
- `@include('filament.page-builder.preview-bridge')` injeta o JS/CSS da ponte.
- `layouts/app.blade.php` não renderiza WhatsApp flutuante, "voltar ao topo" nem alertas/cookies (`@unless($builderPreview)`), que cobririam o conteúdo.

### 5.4 O cérebro no painel (`script.blade.php` → `pageBuilderLivePreview`)

Componente Alpine definido como `window.pageBuilderLivePreview = window.pageBuilderLivePreview || function (config) {…}`. O `||` evita redefinir a função quando o Livewire re-renderiza a view.

**Estado principal**: `device`, `status` (`idle|loading|ready|error`), `url`, `activeFrame` (0 ou 1), `scale`, `hovered`, `selected`, `isEditing`, `paletteQuery`, `pendingScroll`, `_inflight`, `_pending`.

**Quando o preview atualiza** (três gatilhos, todos registrados no `init()`):

1. **Toda requisição Livewire do componente** (`Livewire.hook('commit')`): adicionar, remover, clonar, reordenar, aplicar edição, aplicar template. Depois do `succeed`, chama `syncEditing()` e `queueRefresh(200)`. **Exceção**: se a requisição só chamou `generateLivePreviewUrl` ou `saveDraft`, ela é ignorada. Sem essa checagem haveria um laço infinito (o refresh gera uma requisição, que dispara outro refresh…).
2. **Digitação fora do builder** (`input`, `change`, `trix-change` capturados no `document`): se o alvo está dentro de `.pb-inspector` ou de um form, `queueRefresh(650)`. O `trix-change` é o evento do editor rico (RichEditor usa Trix).
3. **Cliques em toggles, radios, opções e abas** dentro do `.pb-inspector`: esses componentes nem sempre disparam `input`.

**Controle de concorrência** (`refresh()`): se já há uma atualização em andamento (`_inflight`), só marca `_pending = true`; ao terminar, se houver pendência, agenda outra. Resultado: no máximo uma requisição por vez e o último estado sempre aparece.

**Double-buffer sem piscar** (`load(url)`):
1. Lê o `scrollY` do iframe visível.
2. Carrega a nova URL no iframe **oculto** (com `?v=timestamp` para furar cache).
3. No `onload`, aplica o mesmo scroll (`behavior: 'instant'`) e, no próximo `requestAnimationFrame`, troca `activeFrame`. O CSS faz o iframe ativo ficar visível.
4. Reenvia seleção/hover/scroll pendente ao novo iframe.
5. Timeout de 20s → status `error` com botão "Tentar novamente".

**Dispositivos e zoom**: `DEVICES = {desktop: 1440, tablet: 834, mobile: 390}`. O iframe sempre tem a **largura real** do dispositivo (para os breakpoints do Tailwind funcionarem de verdade) e é reduzido com `transform: scale()`. `measure()` calcula `scale = min(1, larguraDisponível / larguraDoDispositivo)`; um `ResizeObserver` recalcula quando a coluna muda. O dispositivo escolhido fica em `localStorage('pb-device')`.

**Sabendo qual bloco está aberto** (`syncEditing()`): lê `$wire.mountedFormComponentActions*` (as mesmas propriedades que o PHP usa em 5.1), descobre o UUID do bloco em edição, atualiza `isEditing`/`selected` e manda `select` ao iframe. Enquanto `isEditing`, as colunas laterais ficam esmaecidas e sem clique (`.pb-shell.is-editing`), e `editBlock()` recusa abrir outro bloco com um toast "Finalize a edição atual".

**Ações**:
- `addBlock(nome)` → `mountFormComponentAction(statePath, 'add', {block})`.
- `onBlockAdded({uuid})` (evento vindo do PHP) → guarda `pendingScroll` e abre `editBlock(uuid)`.
- `editBlock(uuid)` → `mountFormComponentAction(statePath, 'edit', {item: uuid})`.
- `highlight(uuid)`, `locate(uuid)` → mensagens para o iframe.

### 5.5 A ponte dentro do iframe (`preview-bridge.blade.php`)

Script em IIFE com guarda `window.__pbBridge` (roda uma vez só).

- Adiciona a cada `[data-pb-block]` uma etiqueta `.pb-tag` (lápis + nome do bloco) que aparece no hover/seleção.
- **Captura todos os cliques** (`addEventListener('click', …, true)`, fase de captura) e dá `preventDefault()`: links e botões do site não navegam dentro do preview. O clique vira `post({type: 'select', uuid})`. Submits de formulário também são bloqueados.
- `mouseover` → `post({type: 'hover', uuid})` (só quando muda de bloco).
- Recebe do painel: `highlight` (classe `.pb-hover`), `select` (classe `.pb-selected`, e rola se `scroll: true`), `scroll`.
- Ao carregar, manda `{type: 'ready', blocks: [...]}`.
- CSS: contorno com `::after` absoluto e `pointer-events: none` (não interfere no layout do bloco), `scroll-margin-top: 96px` para o header fixo do site não cobrir o bloco ao rolar.

**Protocolo de mensagens** (sempre com `origin` igual ao do painel):

| Direção | `source` | `type` | Campos |
|---|---|---|---|
| painel → iframe | `pb-host` | `highlight` | `uuid` (ou `null`) |
| painel → iframe | `pb-host` | `select` | `uuid`, `scroll` (bool) |
| painel → iframe | `pb-host` | `scroll` | `uuid`, `instant?` |
| iframe → painel | `pb-preview` | `hover` | `uuid` |
| iframe → painel | `pb-preview` | `select` | `uuid` |
| iframe → painel | `pb-preview` | `ready` | `blocks` |

O painel ainda confere `event.source === this.activeWindow()`, ignorando mensagens do iframe oculto.

---

## 6. Rascunho, auto save e publicação (`InteractsWithPageDrafts`)

**Ideia**: o que está em `content`/`meta`/`title`… é o que o site mostra. O editor trabalha num rascunho em `pages.draft`. "Publicar" é o `save` padrão do Filament, que grava os campos reais e **zera** o rascunho.

### 6.1 Servidor

- `getDraftPayload()`: `form->dehydrateState()` sem validação + sobreposição do bloco aberto no slide-over (reaproveita `getLivePreviewEditingOverride` do outro trait) + normalização. **Mantém os UUIDs** em `content`.
- `saveDraft($silent)`: chama `form->callBeforeStateDehydrated()` (é o que move uploads temporários para o storage; sem isso o rascunho guardaria referências a arquivos que somem), persiste, atualiza o hash e, se não havia uploads, `skipRender()` (redesenhar faria o painel aberto perder foco e scroll).
- `dehydrateInteractsWithPageDrafts()`: hook do Livewire que roda **ao fim de toda requisição** (convenção `dehydrate{NomeDoTrait}`). Calcula `md5(json_encode(payload))` e compara com `persistedStateHash` (`#[Locked]`) para definir `hasPendingChanges`. O JS lê essa propriedade.
- `onValidationError()`: se "Publicar" falha, descobre se o erro está no `content` ou nas configurações, troca para a aba certa (`dispatch('page-editor-tab')`) e diz **qual bloco** (posição e nome) tem campo obrigatório vazio.

Diferenças entre as páginas:

| | `EditPage` | `CreatePage` |
|---|---|---|
| `persistDraft` | `forceFill(['draft' => …])->saveQuietly()` | cria um `Page` oculto (`is_visible=false`, `content=[]`, tudo em `draft`) e **redireciona** para o Edit |
| auto save | sim (`supportsAutosave() = true`) | não (não há registro ainda) |
| ao abrir | `mutateFormDataBeforeFill` faz `array_replace($data, $draft)`: o rascunho vem por cima | - |
| ao publicar | `mutateFormDataBeforeSave` zera `draft`, `draft_saved_at` e grava `published_at` | `published_at = now()` |
| extras | `discardDraft()` limpa o rascunho e recarrega o form | slug único com sufixo aleatório se já existir |

### 6.2 Navegador (`editor.blade.php` → `pageEditor`)

- `dirty = localDirty || $wire.hasPendingChanges`. `localDirty` cobre o intervalo entre a tecla e a resposta do servidor.
- Hook `commit`: guarda o instante de início; se não houve digitação depois desse instante, limpa `localDirty`.
- **Auto save** com debounce de 3s (`autosaveDelay`), mas **nunca mais de 15s** com alterações pendentes (`_firstDirtyAt + 15000`). Chama `$wire.saveDraft(true)` (silencioso).
- `Ctrl/Cmd+S` salva o rascunho na hora. `Ctrl/Cmd+Shift+S` publica (key binding da action).
- `beforeunload` avisa se há alterações não salvas (exceto quando o próprio Livewire está redirecionando).
- `status`/`statusLabel`/`statusHint`: `saving`, `error`, `dirty`, `new`, `draft`, `hidden`, `published`. O título e a URL do cabeçalho são reativos a `$wire.data.title`/`slug`.
- Aba ativa refletida no hash (`#configuracoes`).

---

## 7. Como um bloco é definido e renderizado

### 7.1 Definição no painel (`PageResource::getXxxBlock()`)

```php
protected static function getFaqBlock(): Forms\Components\Builder\Block
{
    return Forms\Components\Builder\Block::make('faq')            // 'faq' = valor de "type" no JSON
        ->preview('filament.block-previews.faq')
        ->label(__('FAQ (Perguntas Frequentes)'))                   // nome na biblioteca, na estrutura e nas etiquetas
        ->icon('heroicon-o-question-mark-circle')                  // Heroicon
        ->schema(self::getBlockTabs([                               // aba "Conteúdo"
            Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('…')),
            Forms\Components\Repeater::make('items')->schema([...]),
        ]));
}
```

**`getBlockTabs($conteudo, $estiloExtra = [], $avancadoExtra = [])`** dá a todo bloco as mesmas três abas:
- **Conteúdo**: os campos que você passou.
- **Estilo**: `background_color` (`bg-white`, `bg-background`, `bg-muted/30`, `bg-primary`, `bg-foreground`), `text_color` (`light`/`dark`), `spacing_top`/`spacing_bottom` (`none|xs|sm|md|lg|xl`) + o que você passar em `$estiloExtra`.
- **Avançado**: `custom_id` (âncora HTML) e `custom_css_classes` + extras.

Esses nomes de campo são um **contrato** com `<x-ui.section>` (próxima seção). Use-os sem renomear.

### 7.2 Renderização no site (`render-block.blade.php`)

Um único arquivo com `@props(['type', 'data', 'page', 'theme', 'hideHeader'])` e um `@switch($type)` grande. Cada `@case` monta o HTML ou delega para um componente (`x-section-*`) ou componente Livewire (`budget`, `calculator`, `showcase-feed`…).

O padrão de quase todos os blocos é envolver o conteúdo em **`<x-ui.section :data="$data">`**, que lê `background_color`, `text_color`, `spacing_*`, `custom_id`, `custom_css_classes` e imagem de fundo, e aplica:
- a cor de fundo;
- o **esquema de cor** (`SectionStyle::scheme()`): `dark` (fundo escuro ou texto branco pedido) ou `primary` (fundo `bg-primary`) adicionam uma classe que **redefine os tokens** dentro da seção. Por isso os filhos usam só `text-foreground`, `bg-card`, `text-muted-foreground` e o contraste se ajusta sozinho;
- o padding vertical (`SectionStyle::spacing($data, $padrãoDoBloco)`);
- o container (`default`, `narrow`, `wide`, `none`).

Ajudantes no topo do arquivo:
- **`resolveFilamentImagePath($image)`**: imagem pode chegar como string (`blocks/foo.jpg`), array (`['uuid' => 'blocks/foo.jpg']`, formato do FileUpload), URL absoluta ou `TemporaryUploadedFile`. Sempre use essa função ao ler imagens.
- **`$pickIcon($selecionado, $custom, $padrão)`**: para campos de ícone com opção "Outro" (ver `getIconSelection()` no resource).

**Programe defensivamente**: o bloco é renderizado com dados parciais no preview. Use `$data['x'] ?? null`, `!empty(...)`, `filled(...)`, e não renderize partes cujo dado obrigatório está vazio (ex.: `stats` filtra itens sem `value`). Lembre que um `Repeater` pode vir vazio.

Casos especiais:
- **`module_reference`**: renderiza uma `ContentSection` (seção global reutilizável) pelo id, e faz merge **por cima** dela dos campos de estilo/avançado definidos no Page Builder. É assim que a mesma seção global pode ter fundo diferente em páginas diferentes.
- **Cabeçalho automático** (`page.blade.php`): páginas internas sem bloco `page_header` ganham um `<x-page-header>` automático. Se o primeiro bloco for `services`, o título/badge/descrição dele são "promovidos" para o cabeçalho e o bloco recebe `hideHeader`.
- Há `@case`s que **não** existem como bloco do Page Builder (`hero`, `budget_form`, `calculator`, `team`, `cta_contact`, `simple_banner`, `commercial_partners`…). Eles só chegam via `module_reference` (seções globais).

### 7.3 `->preview()` e `block-previews/`

Cada bloco registra uma view em `resources/views/filament/block-previews/{tipo}.blade.php`, que gera um mini iframe (`iframe-wrapper.blade.php`, via `srcdoc`) com o bloco renderizado. Esse era o preview da view padrão do Builder do Filament (`blockPreviews()`). **O layout de 3 colunas não exibe essas miniaturas**: o preview real é o iframe da coluna central. Mantenha a convenção (o CLAUDE.md pede a view para todo bloco novo), mas saiba que ela não é o que o usuário vê no editor.

---

## 8. Receita: adicionar um bloco novo

Exemplo: bloco `logos_grid`.

1. **Schema** em `app/Filament/Resources/PageResource.php`:
   ```php
   protected static function getLogosGridBlock(): Forms\Components\Builder\Block
   {
       return Forms\Components\Builder\Block::make('logos_grid')
           ->preview('filament.block-previews.logos_grid')
           ->label(__('Grade de Logos'))
           ->icon('heroicon-o-squares-2x2')
           ->schema(self::getBlockTabs([
               Forms\Components\TextInput::make('title')
                   ->label(__('Título'))
                   ->helperText(__('Título acima da grade.'))
                   ->prefixIcon('heroicon-o-h1'),
               Forms\Components\Repeater::make('items')
                   ->label(__('Logos'))
                   ->helperText(__('Uma imagem por empresa.'))
                   ->schema([
                       Forms\Components\FileUpload::make('image')
                           ->label(__('Logo'))->helperText(__('PNG ou SVG.'))
                           ->image()->directory('blocks')->required(),
                   ])
                   ->defaultItems(0),
           ]));
   }
   ```
   E registre em `->blocks([... self::getLogosGridBlock(),])`. Regras de formulário do CLAUDE.md: `helperText` em todo campo, `prefixIcon` onde existir, `Toggle` com `->onIcon('heroicon-m-check')`, ícone em cada opção de `ToggleButtons`. Textos em português.

2. **Renderização** em `resources/views/components/render-block.blade.php`:
   ```blade
   @case('logos_grid')
       @php $logos = array_filter((array) ($data['items'] ?? []), fn ($i) => filled($i['image'] ?? null)); @endphp
       <x-ui.section :data="$data" spacing="sm">
           @if(!empty($data['title']))
               <h2 class="section-title text-center">{{ $data['title'] }}</h2>
           @endif
           <div class="mt-8 grid grid-cols-2 gap-6 md:grid-cols-4">
               @foreach($logos as $logo)
                   <img src="{{ resolveFilamentImagePath($logo['image']) }}" alt="" class="h-12 w-auto object-contain" loading="lazy">
               @endforeach
           </div>
       </x-ui.section>
       @break
   ```
   Visual segundo [estilo-minimalista-design.md](estilo-minimalista-design.md). Classes Tailwind sempre escritas por extenso.

3. **Preview view**: copie `resources/views/filament/block-previews/faq.blade.php` para `logos_grid.blade.php` trocando `'type' => 'faq'`.

4. **Seção global (opcional)**: se o bloco também deve existir em "Seções do site", adicione a constante em `ContentSection` e o tipo em `ContentSectionResource`.

5. **Templates (opcional)**: para usá-lo num template, adicione `['type' => 'logos_grid', 'data' => [...]]` em `PageTemplates::all()`. **Só use chaves que existem no schema**, senão são descartadas ao salvar.

6. **Teste**: acrescente entradas ao `dataset('blocos', [...])` de `tests/Feature/PageBlocksRenderTest.php`, uma com `[]` e outra com dados preenchidos (ex.: `'logos_grid' => ['logos_grid', []]`), e rode `php artisan test --filter=PageBlocksRenderTest`.

Nada precisa mudar no live preview, na ponte ou no JS: qualquer bloco registrado no Builder e com `@case` no switch funciona automaticamente.

---

## 9. Templates de página (`PageTemplates` + ação `loadTemplate`)

- `PageTemplates::all()` é um catálogo PHP: `key → {label, description, category, icon, blocks}`. Categorias em `CATEGORIES`.
- Um item de `blocks` é um bloco normal `['type', 'data']` **ou** `['module' => ContentSection::TYPE_HERO, 'fallback' => [...]?]`. `resolve()` troca o `module` por um `module_reference` apontando para a primeira `ContentSection` ativa daquele tipo; se não houver, usa o `fallback` ou pula (e avisa no toast).
- `summaries($builder)` gera os dados do seletor visual (`page-template-picker.blade.php`), já com labels/ícones dos blocos e marcação de "global"/"faltando".
- A ação `loadTemplate` (registrada com `->registerActions()` no Builder, aberta do JS com `$wire.mountFormComponentAction(statePath, 'loadTemplate')`) pergunta "Adicionar ao final" ou "Substituir tudo". Para cada bloco ela gera um UUID, insere com `data: []` e depois faz `$container->fill()` (valores padrão do schema) seguido de `$container->fill(array_merge(rawState, dadosDoTemplate))`. Esse caminho passa pelos hooks de hidratação dos campos (o `Repeater`, por exemplo, gera as chaves internas dos itens). **Não** atribua o array direto no state: os Repeaters quebrariam.

---

## 10. Armadilhas (leia antes de mudar algo)

1. **Laço de refresh**: o hook `commit` ignora requisições que só chamaram `generateLivePreviewUrl`/`saveDraft`. Se criar outro método chamado pelo JS em segundo plano, inclua-o nessa lista em `script.blade.php`.
2. **`#[Renderless]` é obrigatório** em `generateLivePreviewUrl()`. Sem ele, cada tecla re-renderiza o form e o campo perde o foco.
3. **Os iframes precisam de `wire:ignore`**, senão o morphing do Livewire os recarrega.
4. **UUIDs**: o snapshot e o rascunho mantêm as chaves UUID. Se passar o estado pelo `mutateDehydratedState` do Builder, a sincronia preview/estrutura quebra.
5. **Rota acima do catch-all**: `/admin/page-builder/preview/{token}` precisa ficar antes de `/{slug?}` em `routes/web.php`.
6. **Permissão fora do Filament**: o controller confere `canAccessArea(AccessArea::Website)` e o dono do snapshot. Qualquer rota nova de preview precisa das mesmas checagens.
7. **Upload temporário**: no preview vira `temporaryUrl()`; no rascunho, `callBeforeStateDehydrated()` move o arquivo antes de persistir. Não pule nenhum dos dois.
8. **Bloco que lança exceção** não derruba o preview (try/catch), mas **derruba o site público**. Escreva os `@case` de forma defensiva.
9. **Views vendor customizadas**: `resources/views/vendor/filament-forms/` tem views do Filament publicadas e alteradas. Ao atualizar o Filament, compare-as com as originais. A view `page-builder.blade.php` também depende de APIs internas da view do Builder (`$getAction(...)`, `$getChildComponentContainers()`, nomes das ações); confira-as após upgrades.
10. **Nomes de propriedades Livewire do Filament** (`mountedFormComponentActions*`, `getMountedFormComponentActionForm`) são internos do Filament 3. Num upgrade para Filament 4 eles mudam (ações foram unificadas) e `syncEditing()`/`getLivePreviewEditingOverride()` precisarão ser reescritos.
11. **Classes Tailwind dinâmicas** não são geradas. Escreva-as por extenso (como em `SectionStyle`).
12. **O dev auto-login** de `/admin/login` (ver CLAUDE.md) não deve ser usado em testes; os testes autenticam com `actingAs`.

---

## 11. Testes

```bash
php artisan test --filter=PageBlocksRenderTest   # dataset 'blocos': cada tipo renderiza via Blade::render('<x-render-block …/>'); espaçamento e esquema de cor
php artisan test --filter=PageDraftTest          # rascunho, pendências, publicar, descartar, criar, erro de validação, cabeçalho
php artisan test --filter=PageTemplateTest       # cada template aplica, salva e aparece no site; módulos globais; append/replace
```

Os testes usam Pest com SQLite em memória (`phpunit.xml`), `RefreshDatabase` e `$this->actingAs(User::factory()->create())`. Para Livewire/Filament o padrão do projeto é o helper do Pest:

```php
use function Pest\Livewire\livewire;

livewire(EditPage::class, ['record' => $page->getRouteKey()])
    ->fillForm(['title' => 'Sobre (novo)'])
    ->call('saveDraft', true)
    ->assertSet('hasPendingChanges', false);
```

O JS (Alpine, postMessage, double-buffer) não tem teste automatizado. Depois de mudar `script.blade.php` ou `preview-bridge.blade.php`, verifique à mão no navegador: adicionar bloco abre o editor e rola o preview; digitar atualiza o preview sem piscar; hover e clique sincronizam nos dois sentidos; arrastar reordena; trocar dispositivo ajusta o zoom; auto save muda o status para "Rascunho · salvo às…".

---

## 12. Se for recriar isto em outro projeto

A ordem que funciona:

1. Model com `content` JSON e uma view pública que faz `@foreach` + `@switch($type)`.
2. Resource Filament com `Builder::make('content')` e blocos simples. Confirme que salvar e renderizar funciona.
3. Rota autenticada `preview/{token}` + controller que renderiza a **mesma** view pública a partir de dados do cache.
4. Método `#[Renderless]` no componente que dehidrata o Builder **sem** `mutateDehydratedState`, guarda no cache e devolve a URL.
5. Iframe no painel que chama esse método e carrega a URL. Depois, double-buffer.
6. Wrapper `data-pb-block=uuid` no modo preview + ponte `postMessage`.
7. Sobreposição do bloco em edição (`mountedFormComponentActionsData`) para o preview reagir antes do "Aplicar".
8. View customizada do Builder (3 colunas) reaproveitando as ações nativas do Filament.
9. Rascunho/auto save por último, porque depende do snapshot do passo 4 e 7.
