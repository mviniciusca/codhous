# Estúdio de Posts

Editor de artes para redes sociais em `/admin/poster-builder` (`PosterDesignResource`). Substitui o antigo gerador com Intervention Image. Não usa IA, filas nem jobs: o desenho e a exportação acontecem no navegador; o servidor guarda o documento e valida tudo que chega.

## Como funciona

| Parte | Onde |
|---|---|
| Galeria de artes + criação por template | `PosterDesignResource/Pages/ListPosterDesigns.php`, `pages/list.blade.php` |
| Editor em tela cheia (layout `base` do Filament) | `PosterDesignResource/Pages/PosterStudio.php`, `pages/studio.blade.php` + `partials/` |
| Formatos, paletas, fontes, limites | `config/poster-studio.php` (fonte única) |
| Documento: criar, validar, vincular dados, trocar paleta | `app/Services/PosterStudio/PosterDocument.php` |
| Adaptação de formato | `app/Services/PosterStudio/FormatAdapter.php` |
| Dados da empresa (Settings + catálogo) | `app/Services/PosterStudio/BrandKit.php` |
| Templates | `resources/poster-templates/*.json` (`Templates.php`) |
| Frontend (Fabric.js 6 + Alpine) | `resources/js/poster-studio/` (entrada Vite própria) |

### Documento (schema v1)

`poster_designs.document` guarda `{version, templateId, format, width, height, palette, background, elements[], caption}`. Cada elemento (`text`, `image`, `rect`, `ellipse`, `icon`, `qrcode`) tem id estável, geometria (`x, y, width, height, angle`), `opacity`, `locked`, `hidden` e, opcionalmente, `bind` (ex.: `brand.phone`). A ordem de `elements` é a ordem das camadas.

Cores são `{"role": "primary"}` (vinculada à paleta), `"#RRGGBB"` (personalizada, não muda com a paleta) ou `null` (transparente). Trocar a paleta só altera `document.palette`; o renderer resolve os papéis.

Imagens são sempre caminhos do próprio site (`/storage/...`, `/img/...`). URLs externas e base64 são descartadas na validação; isso também evita que o canvas seja "contaminado" e bloqueie a exportação.

### Fluxo

- **Criar**: template (desenhado em 1080×1080) → `FormatAdapter` → vínculos preenchidos com `BrandKit` → validação → `PosterDesign`.
- **Editar**: o `Editor` (Fabric) é a projeção do documento; desfazer/refazer usa snapshots do documento (gravados ao soltar o mouse/parar de digitar, não a cada movimento).
- **Salvar**: autosave com debounce de 1,5 s via `$wire.save()` (métodos `#[Renderless]`). `revision` faz controle otimista: outra aba com revisão antiga recebe "conflito" em vez de sobrescrever. Alterações não confirmadas ficam em `localStorage` e são oferecidas para restauração.
- **Formato**: `adaptFormat()` no servidor. Elementos que cobrem o canvas esticam; os demais escalam proporcionalmente e ficam ancorados ao terço (início/meio/fim) onde estão. Se o template define `layouts.<portrait|story|landscape>`, essa geometria tem prioridade.
- **Exportar**: PNG/JPEG renderizados no navegador em 1:1 com o documento (1× ou 2×), sem controles, guias nem espaços de imagem vazios. O estúdio confere as dimensões do arquivo gerado antes do download.
- **Variações**: miniaturas de todas as paletas renderizadas fora da tela; "salvar como variação" cria um `PosterDesign` com `parent_id`.

## Adicionar um template

Crie `resources/poster-templates/<id>.json` com `id`, `name`, `category`, `description`, `background`, `caption` (aceita `{{brand.phone}}` etc.), `elements` em 1080×1080 e, se quiser, `layouts.portrait` / `layouts.story` / `layouts.landscape` com a geometria por id de elemento. Em stories, mantenha textos e contatos entre y=250 e y=1670 (o Instagram cobre o topo e a base). O teste `todos os templates geram documentos válidos` cobre novos arquivos automaticamente.

## Limitações conhecidas

- Formato horizontal (1200×628) usa só a adaptação automática; revise a arte depois de trocar.
- Sem agrupamento de elementos nem gradientes.
- SVG não é aceito no upload (somente PNG, JPG e WebP).
- O bundle do editor tem ~730 KB (Fabric + biblioteca Lucide completa para a busca de ícones) e só carrega nas páginas do estúdio.
