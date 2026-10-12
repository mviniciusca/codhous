# CLAUDE.md

Codhous is a Laravel 11 + Filament 3 app for small construction/concrete companies. It has two halves: an admin panel at `/admin` (budgets, products, customers, CMS) and a public website built entirely from database-driven page blocks. The stack is TALL (Tailwind 3, Alpine, Livewire 3), with Lucide icons and Flowbite on the front end.

## Commands

```bash
composer install && npm install
php artisan migrate --seed
npm run dev                 # Vite (entry points: resources/css/app.css, resources/js/app.js)
npm run build
php artisan test            # Pest; uses in-memory SQLite (phpunit.xml)
php artisan test --filter=BudgetTest
vendor/bin/pint             # code style
php artisan budget:history  # see app/Console/Commands/ShowBudgetHistory.php
```

CI (`.github/workflows/laravel.yml`) runs on push/PR to `main`. It uses PHP 8.3 and SQLite, builds assets, then runs `php artisan test`.

## Conventions

- **The UI language is Brazilian Portuguese** (`APP_LOCALE=pt_BR`). Labels, helper texts, comments and test names are mostly in Portuguese and often wrapped in `__()`. Keep new UI strings in Portuguese.
- Commits use Conventional Commits (`feat:`, `refactor:`, `style:`, `chore:`, `fix:`), written in English.
- Work happens on feature branches. PRs target `main`.
- Admin "builder" screens (component library + live preview + step inspector) follow `docs/PADRAO_UI_BUILDER.md`. Every admin page gets the compact one-line header (breadcrumb › title, subheading as a `?` tooltip, actions on the right) from the overridden `resources/views/vendor/filament-panels/components/header/index.blade.php`; set heading/subheading/actions through Filament methods, never build a header inside the view. Public-site blocks follow `docs/estilo-minimalista-design.md`.

## Architecture

### Settings singleton
`App\Models\Setting` is a single row whose `settings` column is a JSON array. Read values with `Setting::get('website.navigation')` (dot notation, memoized per request). The top-level keys are `website`, `company`, `layout` and `security`. Admins edit them in `app/Filament/Pages/DesignAndSeo.php` and `SettingResource`. Maintenance mode is enforced by `CheckMaintenanceMode` middleware, which is appended globally in `bootstrap/app.php` and skips `admin*` and `livewire*` routes.

### Public site = Page Builder
- `routes/web.php` ends with a catch-all `/{slug?}` → `PageController@show`. Any new public route **must be registered above it**.
- `Page.content` is a Filament Builder array of `{type, data}` blocks. `resources/views/page.blade.php` loops over the blocks and renders each one through `resources/views/components/render-block.blade.php`, which is one large `@switch($type)` that delegates to `x-section-*` components or Livewire components (`budget`, `calculator`, `showcase-feed`, `section-hero-cep`, …).
- Internal pages without a `page_header` block get an auto-injected header (see `page.blade.php`).
- `Page::booted()` syncs `is_active_in_menu` into `settings.website.navigation` on save and delete.
- **Adding a block type touches several places:**
  1. Add a `getXxxBlock()` method in `app/Filament/Resources/PageResource.php` and register it in the `Builder::make('content')->blocks([...])` list. Wrap the schema with `getBlockTabs($content, $style, $advanced)`.
  2. Add a `@case('xxx')` in `render-block.blade.php`.
  3. Add a preview view at `resources/views/filament/block-previews/xxx.blade.php`, referenced via `->preview(...)`.
  4. If the block should also be reusable as a global section, add it to `ContentSection` (type constants) and `ContentSectionResource`.
- **ContentSection** holds reusable "global modules". The `module_reference` block renders a `ContentSection` by id and merges style overrides on top of it.
- Image fields can arrive as a string path, an array, or a Livewire `TemporaryUploadedFile` (during preview). Use `resolveFilamentImagePath()` (defined at the top of `render-block.blade.php`) when reading them.

### Page Builder live preview (in progress on `posters-builder`)
- `PageResource/Concerns/InteractsWithLivePreview.php` (used by `CreatePage`/`EditPage`) snapshots the unsaved form state, including the block currently open in the slide-over editor. It stores the snapshot in cache under an HMAC token and returns `route('page-builder.preview')`.
- `PageBuilderPreviewController` renders `page.blade.php` with `builderPreview=true`. In that mode each block is wrapped in `.pb-block[data-pb-block=uuid]` and rendered in a try/catch, so a half-filled block shows an error box instead of crashing the page.
- The builder layout is defined by `resources/views/filament/forms/components/page-builder.blade.php` plus `resources/views/filament/page-builder/` (styles, script, and the iframe `preview-bridge`).
- `filament-peek` (`PreviewAction`) is also registered, as a separate "Preview Interno".

### Filament overrides
- `resources/views/vendor/filament-forms/` contains **published, customized** Filament form views (builder, block-picker, wizard, …), and `resources/views/vendor/filament-panels/` overrides panel components (page header, user menu). They are committed. Filament upgrades may require re-syncing them.
- The panel is configured in `app/Providers/Filament/AdminPanelProvider.php`. Resources, pages, widgets and clusters are auto-discovered. Navigation groups: Orçamentos, Website, Empresa, Configurações. The Shield plugin is installed but commented out.

### Budgets (orçamentos)
- `Budget` stores most of its data in a `content` JSON column. A code is generated on create, and the model has items, products, PDFs and documents.
- Versioning uses activity logging (`LogsActivity`, via filament-logger/spatie activitylog) plus `BudgetObserver`, which writes human-readable price-change entries. See `docs/BUDGET_VERSIONING*.md`.
- Pricing math lives in `Services/BudgetCalculatorService`. Shipping fees and service-area checks by CEP (Brazilian postcode) live in `Services/OperationAreaService`, `OperationArea` and `Rules/CepInOperationAreaRule`.
- PDFs are generated with dompdf (`Services/PdfGeneratorService`). Customers download them through a token route (`/budget/pdf/{token}`). The older signed route `/budget/download/{budget}` is kept for backward compatibility.
- `BudgetResource.php` (~1.3k lines) is the largest admin resource.

### Other pieces
- Livewire front-end components live in `app/Livewire` (budget wizard, calculator, cart, newsletter, showcase feed, contact `Mail/Form`).
- Estúdio de Posts (`PosterDesignResource`, `/admin/poster-builder`): a Fabric.js editor for social media posts with JSON templates in `resources/poster-templates/`, config in `config/poster-studio.php`, services in `app/Services/PosterStudio`. See `docs/POSTER_STUDIO.md`.
- Newsletter (`NewsletterCluster`): `SendNewsletter` is a three-step composer (content + email preview → confirmation → result). `Services/Newsletter/NewsletterSender` sends through the default mailer in small batches driven by the page's `wire:poll` (no queue worker needed) and records every send in `NewsletterCampaign`/`NewsletterCampaignRecipient`, which `NewsletterCampaignResource` (history) displays. Content is sanitized and inlined by `NewsletterContent`, and the same `mail.newsletter` view renders both the email and the preview. The signed unsubscribe route `/newsletter/cancelar/{id}` is exempt from CSRF in `bootstrap/app.php` (for one-click `List-Unsubscribe-Post`).
- Alerts (`AlertResource`, `/admin/alerts`): a card listing (`ListAlerts`, views in `resources/views/filament/alerts/`) plus a two-column editor (form + live preview). Publishing is `is_active`, together with the optional `start_at`/`end_at` window (`Alert::status()`). The editor's "Salvar rascunho" and "Publicar aviso" actions set that flag. The preview iframe (`AlertPreviewController`, `alerts.preview`) renders the real home page without the active alerts. `renderAlertPreview()` returns the HTML of the site's own `x-site-alerts` component, which is then posted into the iframe. The look of each of the six styles (palette, Lucide icon inlined as SVG, button labels, demo content shown for empty preview fields) lives only in `App\Support\AlertAppearance`, which emits the `--sa-*` CSS variables used by `components/site-alerts/styles.blade.php` and by the listing thumbnails. `Alert::typeCatalog()` defines which positions each type allows (the model normalizes the position on save), and `Alert::sanitizeUrl()`/`SafeUrlRule` restrict CTA links to http(s), `/path`, `#anchor`, `mailto:` and `tel:`.
- Integrations: Mailgun/SMTP mail, Cloudflare Turnstile (`TurnstileService`), WhatsApp links (`WhatsappService`), CEP/address lookup (`PostcodeFinderService`, `AddressFinderService`).

## Gotchas

- **`routes/web.php` overrides `/admin/login` with a dev auto-login** that logs in `User::first()`, marked "TODO: Remove". Never ship it to production. Don't rely on it in tests either.
- `AppServiceProvider::boot()` queries `settings` whenever the table exists, so a broken settings row affects every request.
- The `*.php`/`*.py` scripts in the repo root (`modify_*.php`, `patch_*.py`, `refactor_wizard*.py`, `generate_*.php`, `check_brackets.py`, `update_resource.patch`) are one-off codemods that were used to rewrite Blade/Filament files. They are not part of the app. Don't run them unless asked, because they overwrite files in `resources/views/vendor/...` and in the resources.
- `.bak` files exist in `app/Livewire` and `resources/views/livewire`. Ignore them.
- Test coverage is thin (`tests/Feature`: Budget, Navigation, User).
- `docs/` has product and design notes (roadmap, permissions, user guide), mostly in Portuguese.
